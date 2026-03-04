<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $staff     = Auth::guard('staff')->user();
        $isAdmin   = in_array($staff->role, ['admin', 'secretary']);
        $staffList = Staff::where('active', true)->orderBy('name')->get(['id', 'name', 'role']);

        return view('projects.index', compact('staff', 'isAdmin', 'staffList'));
    }

    public function dashboard()
    {
        $today = today()->toDateString();

        $projects = Project::withCount([
            'tasks',
            'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
        ])
        ->with('lead:id,name')
        ->orderBy('name')
        ->get();

        $stats = [
            'total'     => $projects->count(),
            'active'    => $projects->where('status', 'active')->count(),
            'on_hold'   => $projects->where('status', 'on_hold')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
        ];

        $activeProjects = $projects->where('status', 'active')->map(function ($p) use ($today) {
            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'status'      => $p->status,
                'total_tasks' => $p->tasks_count,
                'done_tasks'  => $p->done_tasks_count,
                'lead_name'   => $p->lead?->name,
                'target_date' => $p->target_date?->toDateString(),
                'overdue'     => $p->target_date && $p->target_date->toDateString() < $today,
            ];
        })->values();

        $activity = DB::table('task_history as th')
            ->join('tasks as t', 'th.task_id', '=', 't.id')
            ->join('staff as s', 'th.staff_id', '=', 's.id')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->whereIn('th.action', ['created', 'status_changed', 'commented'])
            ->select('th.action', 's.name as staff_name', 't.title as task_title', 'p.name as project_name', 'th.created_at')
            ->orderByDesc('th.created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'ok'             => true,
            'stats'          => $stats,
            'activeProjects' => $activeProjects,
            'activity'       => $activity,
        ]);
    }

    public function list(Request $request)
    {
        $today = today()->toDateString();

        $query = Project::withCount([
            'tasks',
            'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
        ])->with('lead:id,name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('lead')) {
            $query->where('project_lead', $request->lead);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $projects = $query->orderBy('name')->get()->map(function ($p) use ($today) {
            return [
                'id'           => $p->id,
                'name'         => $p->name,
                'description'  => $p->description,
                'status'       => $p->status,
                'project_lead' => $p->project_lead,
                'lead_name'    => $p->lead?->name,
                'start_date'   => $p->start_date?->toDateString(),
                'target_date'  => $p->target_date?->toDateString(),
                'total_tasks'  => $p->tasks_count,
                'done_tasks'   => $p->done_tasks_count,
                'overdue'      => $p->target_date && $p->target_date->toDateString() < $today && $p->status !== 'completed',
            ];
        });

        return response()->json(['ok' => true, 'projects' => $projects]);
    }

    public function show(Project $project)
    {
        $project->load(['lead:id,name', 'creator:id,name']);

        $tasks = Task::where('project_id', $project->id)
            ->with('assignee:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'id'            => $t->id,
                    'title'         => $t->title,
                    'status'        => $t->status,
                    'priority'      => $t->priority,
                    'due_date'      => $t->due_date?->toDateString(),
                    'assignee_name' => $t->assignee?->name,
                ];
            });

        return response()->json([
            'ok'      => true,
            'project' => [
                'id'           => $project->id,
                'name'         => $project->name,
                'description'  => $project->description,
                'status'       => $project->status,
                'project_lead' => $project->project_lead,
                'lead_name'    => $project->lead?->name,
                'start_date'   => $project->start_date?->toDateString(),
                'target_date'  => $project->target_date?->toDateString(),
                'creator_name' => $project->creator?->name,
                'created_at'   => $project->created_at?->toDateString(),
            ],
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'in:active,on_hold,completed,archived',
        ]);

        $staff = Auth::guard('staff')->user();

        $project = Project::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'status'       => $request->status ?? 'active',
            'project_lead' => $request->project_lead ?: null,
            'start_date'   => $request->start_date ?: null,
            'target_date'  => $request->target_date ?: null,
            'created_by'   => $staff->id,
        ]);

        return response()->json(['ok' => true, 'project_id' => $project->id]);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'status' => 'sometimes|in:active,on_hold,completed,archived',
        ]);

        $allowed = ['name', 'description', 'status', 'project_lead', 'start_date', 'target_date'];

        foreach ($allowed as $field) {
            if ($request->has($field)) {
                $project->{$field} = $request->{$field} ?: null;
            }
        }

        $project->save();

        return response()->json(['ok' => true]);
    }

    public function destroy(Project $project)
    {
        Task::where('project_id', $project->id)->update(['project_id' => null]);
        $project->delete();

        return response()->json(['ok' => true]);
    }
}
