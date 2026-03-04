<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiTaskController extends Controller
{
    private function isAdmin($staff): bool
    {
        return in_array($staff->role, ['admin', 'secretary']);
    }

    private function fireWebhook(string $event, array $data): void
    {
        $url = Setting::get('webhook_url', '');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) return;

        $settingKey = match ($event) {
            'task.created'   => 'notify_task_created',
            'task.completed' => 'notify_task_completed',
            default          => null,
        };
        if ($settingKey && Setting::get($settingKey, '1') !== '1') return;

        try {
            Http::withoutVerifying()->timeout(5)->post($url, [
                'event'     => $event,
                'token'     => 'glops_' . sha1('getlead_hq_webhook_secret'),
                'data'      => $data,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Throwable) {}
    }

    private function formatTask(Task $t): array
    {
        return [
            'id'            => $t->id,
            'title'         => $t->title,
            'description'   => $t->description,
            'assigned_to'   => $t->assigned_to,
            'created_by'    => $t->created_by,
            'priority'      => $t->priority,
            'status'        => $t->status,
            'due_date'      => $t->due_date?->toDateString(),
            'completed_at'  => $t->completed_at,
            'notes'         => $t->notes ?? null,
            'category'      => $t->category,
            'created_at'    => $t->created_at,
            'updated_at'    => $t->updated_at,
            'assignee_name' => $t->assignee?->name,
            'assignee_role' => $t->assignee?->role,
            'creator_name'  => $t->creator?->name,
        ];
    }

    // GET /api/tasks
    public function index(Request $request)
    {
        $staff   = $request->user();
        $isAdmin = $this->isAdmin($staff);

        $query = Task::with(['assignee:id,name,role', 'creator:id,name'])->select('tasks.*');

        if (!$isAdmin) {
            $query->where('assigned_to', $staff->id);
        } elseif ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->integer('assigned_to'));
        }

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('project_id')) $query->where('project_id', $request->integer('project_id'));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%$s%")->orWhere('description', 'like', "%$s%"));
        }

        // Sort: active first by priority, done last
        $query->orderByRaw("CASE status WHEN 'done' THEN 1 ELSE 0 END ASC")
              ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END ASC")
              ->orderBy('due_date', 'ASC');

        $limit = min((int) $request->input('limit', 20), 100);
        $page  = max((int) $request->input('page', 1), 1);
        $total = $query->count();
        $tasks = $query->forPage($page, $limit)->get()->map(fn($t) => $this->formatTask($t));

        return response()->json([
            'tasks' => $tasks,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'pages' => (int) ceil($total / max($limit, 1)),
        ]);
    }

    // GET /api/tasks/{id}
    public function show(Request $request, int $id)
    {
        $staff   = $request->user();
        $isAdmin = $this->isAdmin($staff);

        try
        {
            $task    = Task::with(['assignee:id,name', 'creator:id,name'])->findOrFail($id);
        
            if (!$isAdmin && $task->assigned_to !== $staff->id) {
                return response()->json(['ok' => false, 'error' => 'Not authorized'], 403);
            }

            $comments = TaskComment::with('staff:id,name,role')
                ->where('task_id', $id)
                ->orderBy('created_at')
                ->get()
                ->map(fn($c) => [
                    'id'         => $c->id,
                    'task_id'    => $c->task_id,
                    'staff_id'   => $c->staff_id,
                    'comment'    => $c->comment,
                    'created_at' => $c->created_at,
                    'staff_name' => $c->staff?->name,
                    'staff_role' => $c->staff?->role,
                ]);

            $history = TaskHistory::with('staff:id,name')
                ->where('task_id', $id)
                ->orderBy('created_at')
                ->get()
                ->map(fn($h) => [
                    'id'         => $h->id,
                    'task_id'    => $h->task_id,
                    'staff_id'   => $h->staff_id,
                    'action'     => $h->action,
                    'old_value'  => $h->old_value,
                    'new_value'  => $h->new_value,
                    'created_at' => $h->created_at,
                    'staff_name' => $h->staff?->name,
                ]);

            return response()->json([
                'task'     => $this->formatTask($task),
                'comments' => $comments,
                'history'  => $history,
            ]);
            
        }
        catch(\Exception $e)
        {
            return response()->json([
                'task'     => [],
                'comments' => [],
                'history'  => [],
            ]);
        }
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        $staff = $request->user();
        if (!$this->isAdmin($staff)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $title = trim($request->input('title', ''));
        if (!$title) {
            return response()->json(['ok' => false, 'error' => 'Title required'], 422);
        }

        $assignees = $request->input('assignees', []);
        if (empty($assignees)) $assignees = [$staff->id];

        $count = 0;
        foreach ($assignees as $assigneeId) {
            $task = Task::create([
                'title'       => $title,
                'description' => $request->input('description'),
                'assigned_to' => (int) $assigneeId,
                'created_by'  => $staff->id,
                'priority'    => $request->input('priority', 'normal'),
                'status'      => 'pending',
                'category'    => $request->input('category', 'other'),
                'due_date'    => $request->input('due_date') ?: null,
            ]);

            TaskHistory::create([
                'task_id'   => $task->id,
                'staff_id'  => $staff->id,
                'action'    => 'created',
                'new_value' => $title,
            ]);

            $this->fireWebhook('task.created', [
                'task_id'     => $task->id,
                'title'       => $title,
                'assigned_to' => (int) $assigneeId,
                'priority'    => $task->priority,
                'due_date'    => $request->input('due_date'),
                'created_by'  => $staff->id,
            ]);

            $count++;
        }

        return response()->json(['ok' => true, 'count' => $count]);
    }

    // PUT /api/tasks/{id}
    public function update(Request $request, int $id)
    {
        $staff   = $request->user();
        $isAdmin = $this->isAdmin($staff);
        $task    = Task::findOrFail($id);

        if (!$isAdmin && $task->assigned_to !== $staff->id) {
            return response()->json(['ok' => false, 'error' => 'Not authorized'], 403);
        }

        // Non-admin: only status changes allowed
        if (!$isAdmin) {
            if ($request->has('status')) {
                $old = $task->status;
                $new = $request->input('status');
                $task->status = $new;
                if ($new === 'done') $task->completed_at = now();
                $task->save();

                TaskHistory::create([
                    'task_id'   => $task->id,
                    'staff_id'  => $staff->id,
                    'action'    => 'status_changed',
                    'old_value' => $old,
                    'new_value' => $new,
                ]);

                if ($new === 'done') {
                    $this->fireWebhook('task.completed', [
                        'task_id'      => $task->id,
                        'title'        => $task->title,
                        'completed_by' => $staff->id,
                        'assigned_to'  => $task->assigned_to,
                    ]);
                }
            }
            return response()->json(['ok' => true]);
        }

        // Admin: update any allowed field
        $adminFields = ['title', 'description', 'priority', 'assigned_to', 'category', 'due_date', 'notes'];
        $completedNow = false;

        foreach ($adminFields as $field) {
            if ($request->has($field)) {
                $old = $task->$field instanceof \Illuminate\Support\Carbon
                    ? $task->$field->toDateString()
                    : $task->$field;
                $new = $request->input($field);
                if ((string) $old !== (string) $new) {
                    $task->$field = $new ?: null;
                    TaskHistory::create([
                        'task_id'   => $task->id,
                        'staff_id'  => $staff->id,
                        'action'    => $field === 'assigned_to' ? 'assigned' : 'updated',
                        'old_value' => (string) $old,
                        'new_value' => (string) $new,
                    ]);
                }
            }
        }

        if ($request->has('status')) {
            $old = $task->status;
            $new = $request->input('status');
            if ($old !== $new) {
                $task->status = $new;
                if ($new === 'done') {
                    $task->completed_at = now();
                    $completedNow = true;
                }
                TaskHistory::create([
                    'task_id'   => $task->id,
                    'staff_id'  => $staff->id,
                    'action'    => 'status_changed',
                    'old_value' => $old,
                    'new_value' => $new,
                ]);
            }
        }

        $task->save();

        if ($completedNow) {
            $this->fireWebhook('task.completed', [
                'task_id'      => $task->id,
                'title'        => $task->title,
                'completed_by' => $staff->id,
                'assigned_to'  => $task->assigned_to,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // DELETE /api/tasks/{id}
    public function destroy(Request $request, int $id)
    {
        $staff = $request->user();
        if (!$this->isAdmin($staff)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $task = Task::findOrFail($id);
        TaskComment::where('task_id', $id)->delete();
        TaskHistory::where('task_id', $id)->delete();
        $task->delete();

        return response()->json(['ok' => true]);
    }

    // POST /api/tasks/{id}/comment
    public function addComment(Request $request, int $id)
    {
        $staff   = $request->user();
        $isAdmin = $this->isAdmin($staff);
        $task    = Task::findOrFail($id);

        if (!$isAdmin && $task->assigned_to !== $staff->id) {
            return response()->json(['ok' => false, 'error' => 'Not authorized'], 403);
        }

        $comment = trim($request->input('comment', ''));
        if (!$comment) {
            return response()->json(['ok' => false, 'error' => 'Comment required'], 422);
        }

        TaskComment::create([
            'task_id'  => $id,
            'staff_id' => $staff->id,
            'comment'  => $comment,
        ]);

        TaskHistory::create([
            'task_id'   => $id,
            'staff_id'  => $staff->id,
            'action'    => 'commented',
            'new_value' => $comment,
        ]);

        return response()->json(['ok' => true]);
    }

    // PATCH /api/tasks/{id}/status  — quick status change
    public function statusUpdate(Request $request, int $id)
    {
        return $this->update($request, $id);
    }
}
