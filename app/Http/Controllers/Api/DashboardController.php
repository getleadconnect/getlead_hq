<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Staff;
use App\Models\Task;
use App\Models\TaskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function isAdmin($staff): bool
    {
        return in_array($staff->role, ['admin', 'secretary']);
    }

    // GET /api/dashboard/my
    public function myDashboard(Request $request)
    {
        $staff = $request->user();
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfWeek  = now()->startOfWeek()->toDateString();

        $base = Task::where('assigned_to', $staff->id);

        $tasksOpen           = (clone $base)->whereIn('status', ['pending', 'in_progress', 'blocked'])->count();
        $tasksCompletedMonth = (clone $base)->where('status', 'done')->whereDate('updated_at', '>=', $startOfMonth)->count();
        $tasksCompletedWeek  = (clone $base)->where('status', 'done')->whereDate('updated_at', '>=', $startOfWeek)->count();
        $tasksOverdue        = (clone $base)->whereIn('status', ['pending', 'in_progress', 'blocked'])
                                            ->whereNotNull('due_date')->where('due_date', '<', $today)->count();

        $totalTasks     = (clone $base)->count();
        $doneTasks      = (clone $base)->where('status', 'done')->count();
        $completionRate = $totalTasks > 0 ? round($doneTasks / $totalTasks * 100) : 0;

        $avgDays = Task::where('assigned_to', $staff->id)
            ->where('status', 'done')
            ->avg(DB::raw('DATEDIFF(updated_at, created_at)'));

        // Report data
        $reportDates = DailyReport::where('staff_id', $staff->id)
            ->orderByDesc('report_date')
            ->limit(60)
            ->pluck('report_date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // Report streak
        $streak = 0;
        $check  = in_array($today, $reportDates) ? $today : Carbon::yesterday()->toDateString();
        foreach ($reportDates as $d) {
            if ($d === $check) {
                $streak++;
                $check = Carbon::parse($check)->subDay()->toDateString();
            }
        }

        $lastReport    = DailyReport::where('staff_id', $staff->id)->orderByDesc('report_date')->first();
        $reportedToday = in_array($today, $reportDates);

        // Calendar: last 14 days
        $calendar = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $calendar[] = ['date' => $date, 'submitted' => in_array($date, $reportDates)];
        }

        return response()->json([
            'ok'                       => true,
            'tasks_open'               => $tasksOpen,
            'tasks_completed_month'    => $tasksCompletedMonth,
            'tasks_overdue'            => $tasksOverdue,
            'completion_rate'          => $completionRate,
            'tasks_completed_week'     => $tasksCompletedWeek,
            'report_streak'            => $streak,
            'last_report_date'         => $lastReport?->report_date?->toDateString(),
            'last_report_submitted_at' => $lastReport?->submitted_at,
            'report_calendar'          => $calendar,
            'avg_completion_days'      => round((float) $avgDays, 1),
            'reported_today'           => $reportedToday,
        ]);
    }

    // GET /api/dashboard/admin
    public function adminDashboard(Request $request)
    {
        $staff = $request->user();
        if (!$this->isAdmin($staff)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $today       = now()->toDateString();
        $startOfWeek = now()->startOfWeek()->toDateString();

        $totalStaff     = Staff::where('active', true)->count();
        $totalTasks     = Task::count();
        $overdueTasks   = Task::whereIn('status', ['pending', 'in_progress', 'blocked'])
                              ->whereNotNull('due_date')->where('due_date', '<', $today)->count();
        $completedToday = Task::where('status', 'done')->whereDate('updated_at', $today)->count();
        $weekCompletion = Task::where('status', 'done')->where('updated_at', '>=', $startOfWeek)->count();

        $reportedToday = DailyReport::where('report_date', $today)->pluck('staff_id');
        $reportsCount  = $reportedToday->count();
        $reportRate    = $totalStaff > 0 ? round($reportsCount / $totalStaff * 100) : 0;

        // Recent activity
        $recentActivity = TaskHistory::with(['staff:id,name', 'task:id,title'])
            ->orderByDesc('created_at')
            ->limit(10)
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
                'task_title' => $h->task?->title,
            ]);

        // Team status
        $activeStaff = Staff::where('active', true)->get();
        $teamStatus  = $activeStaff->map(function ($s) use ($today, $reportedToday) {
            return [
                'id'             => $s->id,
                'name'           => $s->name,
                'role'           => $s->role,
                'pending_tasks'  => Task::where('assigned_to', $s->id)->whereIn('status', ['pending', 'in_progress', 'blocked'])->count(),
                'overdue_tasks'  => Task::where('assigned_to', $s->id)->whereIn('status', ['pending', 'in_progress', 'blocked'])->whereNotNull('due_date')->where('due_date', '<', $today)->count(),
                'last_report'    => DailyReport::where('staff_id', $s->id)->orderByDesc('report_date')->value('report_date'),
                'reported_today' => (int) $reportedToday->contains($s->id),
            ];
        });

        $reportsMissing = $activeStaff
            ->filter(fn($s) => !$reportedToday->contains($s->id))
            ->values()
            ->map(fn($s) => ['name' => $s->name, 'role' => $s->role]);

        $reportsSubmittedList = DailyReport::with('staff:id,name,role')
            ->where('report_date', $today)
            ->get()
            ->map(fn($r) => [
                'name'         => $r->staff?->name,
                'role'         => $r->staff?->role,
                'submitted_at' => $r->submitted_at,
            ]);

        return response()->json([
            'stats' => [
                'total_staff'            => $totalStaff,
                'total_tasks'            => $totalTasks,
                'overdue_tasks'          => $overdueTasks,
                'completed_today'        => $completedToday,
                'reports_submitted'      => $reportsCount,
                'report_rate'            => $reportRate,
                'week_completion'        => $weekCompletion,
                'recent_activity'        => $recentActivity,
                'team_status'            => $teamStatus,
                'reports_missing'        => $reportsMissing,
                'reports_submitted_list' => $reportsSubmittedList,
            ],
        ]);
    }
}
