<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    public function index()
    {
        $staff   = Auth::guard('staff')->user();
        $isAdmin = $staff->role === 'admin';

        $staffList = $isAdmin
            ? Staff::where('active', true)->orderBy('name')->get(['id', 'name', 'role'])
            : collect();

        $projects = $this->safe(fn () => DB::table('projects')
            ->whereIn('status', ['active', 'on_hold'])
            ->orderBy('name')
            ->get(['id', 'name']), collect());

        return view('tasks.index', compact('staff', 'isAdmin', 'staffList', 'projects'));
    }

    public function datatable(Request $request)
    {
        $staff   = Auth::guard('staff')->user();
        $isAdmin = $staff->role === 'admin';

        $query = Task::query()
            ->leftJoin('staff as assignee', 'tasks.assigned_to', '=', 'assignee.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->select(
                'tasks.*',
                'assignee.name as assignee_name',
                DB::raw("COALESCE(projects.name, '') as project_name")
            );

        // Scope: My Tasks vs All Tasks
        if ($request->view !== 'all' || ! $isAdmin) {
            $query->where('tasks.assigned_to', $staff->id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('tasks.status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('tasks.priority', $request->priority);
        }
        if ($request->filled('assigned_to')) {
            $query->where('tasks.assigned_to', $request->assigned_to);
        }
        if ($request->filled('project_id')) {
            $query->where('tasks.project_id', $request->project_id);
        }
        if ($request->filled('due_after')) {
            $query->where('tasks.due_date', '>=', $request->due_after);
        }
        if ($request->filled('due_before')) {
            $query->where('tasks.due_date', '<=', $request->due_before);
        }
        if ($request->filled('keyword')) {
            $kw = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('tasks.title', 'like', $kw)
                  ->orWhere('tasks.status', 'like', $kw)
                  ->orWhere('tasks.priority', 'like', $kw)
                  ->orWhere('tasks.category', 'like', $kw);
            });
        }

        $todayStr = today()->toDateString();

        return DataTables::of($query)
            ->filterColumn('title', fn ($q, $kw) => $q->where('tasks.title', 'like', "%{$kw}%"))
            ->addColumn('due_date_fmt', function ($t) {
                if (! $t->due_date) return null;
                $date = \Carbon\Carbon::parse($t->due_date);
                // Show year only if not current year
                return $date->year === now()->year
                    ? $date->format('d M')
                    : $date->format('d M Y');
            })
            ->addColumn('is_overdue', function ($t) use ($todayStr) {
                if (! $t->due_date || $t->status === 'done') return false;
                return \Carbon\Carbon::parse($t->due_date)->toDateString() < $todayStr;
            })
            ->addColumn('is_due_today', function ($t) use ($todayStr) {
                if (! $t->due_date || $t->status === 'done') return false;
                return \Carbon\Carbon::parse($t->due_date)->toDateString() === $todayStr;
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'priority' => 'in:low,normal,high,urgent',
            'status'   => 'in:pending,in_progress,blocked,done',
            'category' => 'in:sales,development,support,hr,finance,operations,other',
        ]);

        $staff = Auth::guard('staff')->user();

        DB::beginTransaction();

        try
        {
            $task = Task::create([
                'title'       => $request->title,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to ?: $staff->id,
                'created_by'  => $staff->id,
                'status'      => $request->status ?? 'pending',
                'priority'    => $request->priority ?? 'normal',
                'category'    => $request->category ?? 'other',
                'project_id'  => $request->project_id ?: null,
                'due_date'    => $request->due_date ?: null,
            ]);

            TaskHistory::create([
                'task_id'  => $task->id,
                'staff_id' => $staff->id,
                'action'   => 'created',
                'new_value' => $task->title,
            ]);
            DB::commit();
            return response()->json(['ok' => true, 'task_id' => $task->id]);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['ok' => false, 'task_id' => null]);

        }
    }

    public function show(Task $task)
    {
        $task->load(['assignee:id,name', 'creator:id,name', 'project:id,name',
                     'comments.staff:id,name', 'history.staff:id,name']);

        return response()->json([
            'ok'       => true,
            'task'     => $task,
            'comments' => $task->comments,
            'history'  => $task->history,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $staff   = Auth::guard('staff')->user();
        $allowed = ['title', 'description', 'assigned_to', 'status',
                    'priority', 'category', 'project_id', 'due_date'];

        $changes = [];
        foreach ($allowed as $field) {
            if ($request->has($field)) {
                $old = $task->{$field};
                $new = $request->{$field} ?: null;
                if ((string) $old !== (string) $new) {
                    $changes[$field] = ['old' => $old, 'new' => $new];
                    $task->{$field} = $new;
                }
            }
        }

        $task->save();

        foreach ($changes as $field => $c) {
            $action = match ($field) {
                'status'      => 'status_changed',
                'assigned_to' => 'assigned',
                default       => 'updated',
            };
            TaskHistory::create([
                'task_id'   => $task->id,
                'staff_id'  => $staff->id,
                'action'    => $action,
                'old_value' => (string) $c['old'],
                'new_value' => (string) $c['new'],
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(Task $task)
    {
        $staff = Auth::guard('staff')->user();

        TaskHistory::create([
            'task_id'  => $task->id,
            'staff_id' => $staff->id,
            'action'   => 'deleted',
            'old_value' => $task->title,
        ]);

        $task->delete();
        return response()->json(['ok' => true]);
    }

    public function addComment(Request $request, Task $task)
    {
        $request->validate(['comment' => 'required|string']);
        $staff = Auth::guard('staff')->user();

        $comment = TaskComment::create([
            'task_id'  => $task->id,
            'staff_id' => $staff->id,
            'comment'  => $request->comment,
        ]);

        TaskHistory::create([
            'task_id'  => $task->id,
            'staff_id' => $staff->id,
            'action'   => 'commented',
            'new_value' => substr($request->comment, 0, 100),
        ]);

        $comment->load('staff:id,name');

        return response()->json(['ok' => true, 'comment' => $comment]);
    }

    private function safe(callable $fn, mixed $default = 0): mixed
    {
        try {
            return $fn();
        } catch (\Throwable) {
            return $default;
        }
    }
}
