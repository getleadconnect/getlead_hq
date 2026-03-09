<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Staff;
use App\Models\Task;
use App\Models\TpCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private function isAdmin(): bool
    {
        return in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary']);
    }

    private function dateRange(Request $request): array
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        return [$from, $to];
    }

    public function index()
    {
        if (!$this->isAdmin()) abort(403);
        return view('analytics.index');
    }

    // ── Task Analytics ──────────────────────────────────────────────

    public function apiTasks(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);
        [$from, $to] = $this->dateRange($request);

        $base = fn() => Task::whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);

        $totalCreated   = $base()->count();
        $totalCompleted = $base()->where('status', 'done')->count();
        $completionRate = $totalCreated ? round($totalCompleted / $totalCreated * 100) : 0;
        $overdueCount   = Task::where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', today())->count();
        $avgDays        = $base()->where('status', 'done')
                            ->selectRaw('ROUND(AVG(DATEDIFF(updated_at, created_at))) as avg_days')
                            ->value('avg_days') ?? 0;

        // By person
        $byPerson = $base()->whereNotNull('assigned_to')
            ->with('assignee:id,name')
            ->get(['id', 'assigned_to', 'status', 'due_date'])
            ->groupBy('assigned_to')
            ->map(fn($tasks) => [
                'name'      => $tasks->first()->assignee?->name ?? 'Unknown',
                'total'     => $tasks->count(),
                'completed' => $tasks->where('status', 'done')->count(),
                'rate'      => $tasks->count() > 0 ? round($tasks->where('status', 'done')->count() / $tasks->count() * 100) : 0,
            ])
            ->sortByDesc('completed')
            ->values();

        // By category
        $byCategory = $base()->select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        // Trend: created vs completed per day
        $created   = $base()->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as cnt'))
                        ->groupBy('date')->pluck('cnt', 'date');
        $completed = Task::where('status', 'done')
                        ->whereBetween(DB::raw('DATE(updated_at)'), [$from, $to])
                        ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as cnt'))
                        ->groupBy('date')->pluck('cnt', 'date');

        $trend = [];
        $cur   = Carbon::parse($from);
        $end   = Carbon::parse($to);
        while ($cur->lte($end)) {
            $d       = $cur->toDateString();
            $trend[] = ['date' => $d, 'created' => $created[$d] ?? 0, 'completed' => $completed[$d] ?? 0];
            $cur->addDay();
        }
        if (count($trend) > 30) {
            $step  = (int) ceil(count($trend) / 30);
            $trend = array_values(array_filter($trend, fn($_, $i) => $i % $step === 0, ARRAY_FILTER_USE_BOTH));
        }

        return response()->json([
            'ok'                  => true,
            'total_created'       => $totalCreated,
            'total_completed'     => $totalCompleted,
            'completion_rate'     => $completionRate,
            'overdue_count'       => $overdueCount,
            'avg_completion_days' => $avgDays,
            'by_person'           => $byPerson,
            'by_category'         => $byCategory,
            'trend'               => $trend,
        ]);
    }

    // ── Report Analytics ────────────────────────────────────────────

    public function apiReports(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);
        [$from, $to] = $this->dateRange($request);

        $totalDays    = max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1);
        $staff        = Staff::where('active', 1)->where('role', '!=', 'admin')->orderBy('name')->get(['id', 'name']);
        $submittedMap = DailyReport::whereBetween('report_date', [$from, $to])
                            ->select('staff_id', DB::raw('COUNT(*) as cnt'))
                            ->groupBy('staff_id')
                            ->pluck('cnt', 'staff_id');

        $byPerson = $staff->map(fn($s) => [
            'name'       => $s->name,
            'submitted'  => $submittedMap[$s->id] ?? 0,
            'total_days' => $totalDays,
            'rate'       => round(($submittedMap[$s->id] ?? 0) / $totalDays * 100),
        ])->sortByDesc('submitted')->values();

        // Streaks: consecutive days submitted up to today
        $streaks = $staff->map(function ($s) {
            $streak = 0;
            for ($i = 0; $i < 60; $i++) {
                $d = now()->subDays($i)->toDateString();
                if (!DailyReport::where('staff_id', $s->id)->where('report_date', $d)->exists()) break;
                $streak++;
            }
            return ['name' => $s->name, 'streak' => $streak];
        })->filter(fn($s) => $s['streak'] > 0)->sortByDesc('streak')->values()->take(10);

        // Daily trend
        $trend = DailyReport::whereBetween('report_date', [$from, $to])
            ->select('report_date', DB::raw('COUNT(*) as submitted'))
            ->groupBy('report_date')->orderBy('report_date')
            ->get()->map(fn($r) => ['date' => $r->report_date->toDateString(), 'submitted' => $r->submitted]);

        return response()->json(['ok' => true, 'by_person' => $byPerson, 'streaks' => $streaks, 'trend' => $trend]);
    }

    // ── Team Performance ─────────────────────────────────────────────

    public function apiTeam(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);
        [$from, $to] = $this->dateRange($request);

        $totalDays  = max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1);
        $staff      = Staff::where('active', 1)->where('role', '!=', 'admin')->orderBy('name')->get(['id', 'name', 'role']);
        $roleLabels = ReportsController::ROLE_LABELS;

        $allTasks = Task::whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->whereIn('assigned_to', $staff->pluck('id'))
            ->get(['id', 'assigned_to', 'status', 'due_date']);

        $reportCounts = DailyReport::whereBetween('report_date', [$from, $to])
            ->whereIn('staff_id', $staff->pluck('id'))
            ->select('staff_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('staff_id')->pluck('cnt', 'staff_id');

        $members = $staff->map(function ($s) use ($allTasks, $reportCounts, $totalDays, $roleLabels) {
            $tasks   = $allTasks->where('assigned_to', $s->id);
            $total   = $tasks->count();
            $done    = $tasks->where('status', 'done')->count();
            $pending = $tasks->whereIn('status', ['pending', 'in_progress', 'blocked'])->count();
            $overdue = $tasks->where('status', '!=', 'done')
                             ->filter(fn($t) => $t->due_date && $t->due_date < now())->count();
            $reports = $reportCounts[$s->id] ?? 0;

            $taskScore   = $total ? min(60, round($done / $total * 60)) : 0;
            $reportScore = min(40, round($reports / $totalDays * 40));
            $meta        = $roleLabels[$s->role] ?? ['label' => ucfirst($s->role), 'emoji' => '👤'];

            return [
                'name'               => $s->name,
                'role_label'         => $meta['label'],
                'initials'           => strtoupper(substr($s->name, 0, 2)),
                'tasks_completed'    => $done,
                'tasks_pending'      => $pending,
                'reports_submitted'  => $reports,
                'overdue'            => $overdue,
                'productivity_score' => $taskScore + $reportScore,
            ];
        })->sortByDesc('productivity_score')->values();

        return response()->json(['ok' => true, 'members' => $members]);
    }

    // ── HR Analytics ─────────────────────────────────────────────────

    public function apiHr(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);
        [$from, $to] = $this->dateRange($request);

        $totalDays  = max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1);
        $staff      = Staff::where('active', 1)->where('role', '!=', 'admin')->get(['id', 'name']);
        $totalStaff = $staff->count();

        $dailyCounts = DailyReport::whereBetween('report_date', [$from, $to])
            ->select('report_date', DB::raw('COUNT(DISTINCT staff_id) as cnt'))
            ->groupBy('report_date')->orderBy('report_date')
            ->get()->keyBy(fn($r) => $r->report_date->toDateString());

        $presentDays    = $dailyCounts->filter(fn($r) => $totalStaff > 0 && ($r->cnt / $totalStaff) >= 0.5)->count();
        $attendanceRate = $totalDays ? round($presentDays / $totalDays * 100) : 0;

        $trend = $dailyCounts->values()->map(fn($r) => [
            'date'    => $r->report_date->toDateString(),
            'present' => $r->cnt,
        ]);

        $submittedMap    = DailyReport::whereBetween('report_date', [$from, $to])
                            ->select('staff_id', DB::raw('COUNT(*) as cnt'))
                            ->groupBy('staff_id')->pluck('cnt', 'staff_id');
        $leaveByPerson   = $staff->map(fn($s) => [
            'name'         => $s->name,
            'total_leaves' => max(0, $totalDays - ($submittedMap[$s->id] ?? 0)),
        ])->filter(fn($s) => $s['total_leaves'] > 0)->sortByDesc('total_leaves')->values()->take(10);

        return response()->json([
            'ok'         => true,
            'attendance' => [
                'present_days'    => $presentDays,
                'half_days'       => 0,
                'leave_days'      => $totalDays - $presentDays,
                'attendance_rate' => $attendanceRate,
                'trend'           => $trend,
            ],
            'leave_by_person' => $leaveByPerson,
        ]);
    }

    // ── Marketing Analytics ──────────────────────────────────────────

    public function apiMarketing(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);
        [$from, $to] = $this->dateRange($request);

        $all      = TpCustomer::all();
        $registered = TpCustomer::whereBetween(DB::raw('DATE(created_at)'), [$from, $to])->count();

        $activePaid   = $all->whereNotIn('subscription_type', ['free_trial', 'extended_trial'])
                            ->where('status', '!=', 'churned')->count();
        $activeTrials = $all->whereIn('subscription_type', ['free_trial', 'extended_trial'])
                            ->where('status', 'active')->count();
        $churned      = $all->where('status', 'churned')->count();
        $trialTotal   = $all->whereIn('subscription_type', ['free_trial', 'extended_trial'])->count();
        $conversionRate = ($activePaid + $trialTotal) > 0
            ? round($activePaid / ($activePaid + $trialTotal) * 100)
            : 0;

        $pricing = ['1_month' => 999, '3_month' => 2499, '1_year' => 7999];
        $revenue  = 0;
        foreach ($pricing as $type => $price) {
            $revenue += $all->where('subscription_type', $type)->where('status', '!=', 'churned')->count() * $price;
        }

        $funnel = [
            ['stage' => 'Free Trial',    'count' => $all->where('subscription_type', 'free_trial')->count()],
            ['stage' => 'Ext. Trial',    'count' => $all->where('subscription_type', 'extended_trial')->count()],
            ['stage' => 'Paid (Active)', 'count' => $activePaid],
            ['stage' => 'Churned',       'count' => $churned],
        ];

        $campaigns = [];
        $subLabels = ['free_trial' => 'Free Trial', 'extended_trial' => 'Ext. Trial', '1_month' => '1 Month', '3_month' => '3 Month', '1_year' => '1 Year'];
        foreach ($subLabels as $type => $label) {
            $count = $all->where('subscription_type', $type)->count();
            if ($count > 0) $campaigns[] = ['name' => $label, 'leads' => $count];
        }

        return response()->json([
            'ok'      => true,
            'summary' => [
                'total_revenue'       => $revenue,
                'total_registrations' => $registered,
                'conversion_rate'     => $conversionRate,
                'active_leads'        => $activeTrials,
            ],
            'funnel'    => $funnel,
            'campaigns' => $campaigns,
        ]);
    }
}
