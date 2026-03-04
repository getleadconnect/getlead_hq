<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Role → display fields (ported from admin.php ROLE_FIELDS)
    private const ROLE_FIELDS = [
        'sales_rep' => [
            ['k' => 'calls_made',       'l' => '📞 Calls'],
            ['k' => 'calls_connected',  'l' => '📱 Connected'],
            ['k' => 'demos_scheduled',  'l' => '🎯 Demos Sched'],
            ['k' => 'demos_completed',  'l' => '🎯 Demos Done'],
            ['k' => 'trials',           'l' => '🆕 Trials'],
            ['k' => 'payments_closed',  'l' => '💰 Payments'],
            ['k' => 'payments_amount',  'l' => '💰 Amount ₹',   'fmt' => 'currency'],
            ['k' => 'hot_leads',        'l' => '🔥 Hot Leads'],
            ['k' => 'notes',            'l' => '📝 Notes'],
        ],
        'secretary' => [
            ['k' => 'tickets_handled',  'l' => '🎫 Tickets'],
            ['k' => 'license_updates',  'l' => '📋 Licenses'],
            ['k' => 'followups',        'l' => '✅ Follow-ups'],
            ['k' => 'notes',            'l' => '📝 Notes'],
        ],
        'support' => [
            ['k' => 'tickets_handled',    'l' => '🎫 Handled'],
            ['k' => 'tickets_resolved',   'l' => '✅ Resolved'],
            ['k' => 'avg_response_time',  'l' => '⏱️ Avg Resp (min)'],
            ['k' => 'escalation_count',   'l' => '⚠️ Escalations'],
            ['k' => 'escalation_details', 'l' => '📋 Details'],
            ['k' => 'notes',              'l' => '📝 Notes'],
        ],
        'hr' => [
            ['k' => 'attendance',      'l' => '👥 Attendance'],
            ['k' => 'leave_requests',  'l' => '📋 Leave Req'],
            ['k' => 'interviews',      'l' => '🤝 Interviews'],
            ['k' => 'issues',          'l' => '⚠️ Issues'],
            ['k' => 'notes',           'l' => '📝 Notes'],
        ],
        'finance' => [
            ['k' => 'invoices',          'l' => '📄 Invoices'],
            ['k' => 'collected_count',   'l' => '💰 Collected'],
            ['k' => 'collected_amount',  'l' => '💰 Collected ₹', 'fmt' => 'currency'],
            ['k' => 'pending_count',     'l' => '⏳ Pending'],
            ['k' => 'pending_amount',    'l' => '⏳ Pending ₹',   'fmt' => 'currency'],
            ['k' => 'expenses_count',    'l' => '💸 Expenses'],
            ['k' => 'expenses_amount',   'l' => '💸 Expenses ₹',  'fmt' => 'currency'],
            ['k' => 'notes',             'l' => '📝 Notes'],
        ],
        'developer' => [
            ['k' => 'tasks',       'l' => '✅ Tasks'],
            ['k' => 'commits',     'l' => '🔀 Commits'],
            ['k' => 'bugs_fixed',  'l' => '🐛 Bugs Fixed'],
            ['k' => 'blockers',    'l' => '🚧 Blockers'],
            ['k' => 'notes',       'l' => '📝 Notes'],
        ],
        'tester' => [
            ['k' => 'test_cases',    'l' => '🧪 Test Cases'],
            ['k' => 'bugs_found',    'l' => '🐛 Bugs Found'],
            ['k' => 'bugs_verified', 'l' => '✅ Verified'],
            ['k' => 'blockers',      'l' => '🚧 Blockers'],
            ['k' => 'notes',         'l' => '📝 Notes'],
        ],
        'admin' => [
            ['k' => 'tasks',      'l' => '✅ Tasks'],
            ['k' => 'decisions',  'l' => '🎯 Decisions'],
            ['k' => 'notes',      'l' => '📝 Notes'],
        ],
    ];

    private const ROLE_EMOJI = [
        'sales_rep' => '💼', 'secretary' => '📋', 'support' => '🎫',
        'hr'        => '👥', 'finance'   => '💰', 'developer' => '💻',
        'tester'    => '🧪', 'admin'     => '🎯',
    ];

    public function index()
    {
        $staff = Auth::guard('staff')->user();

        // Time-based greeting (IST)
        $hour     = now('Asia/Kolkata')->hour;
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Good morning',
            $hour >= 12 && $hour < 17 => 'Good afternoon',
            $hour >= 17 && $hour < 21 => 'Good evening',
            default                   => 'Good night',
        };

        // Staff counts
        $staffTotal           = Staff::where('active', true)->count();
        $reportableStaffCount = Staff::where('active', true)->where('role', '!=', 'admin')->count();

        // ── Stat cards ──────────────────────────────────────────────────
        $myTasksCount = $this->safe(fn () => DB::table('tasks')
            ->where('assigned_to', $staff->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count());

        $teamOnlineCount = $this->safe(fn () => DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id'));

        $assetsNeedCheckup = $this->safe(fn () => DB::table('asset_checkups')
            ->whereDate('next_checkup', '<=', today())
            ->count());

        $reportsTodayCount = $this->safe(fn () => DB::table('daily_reports')
            ->whereDate('report_date', today())
            ->count());

        $submissionRate = $reportableStaffCount > 0
            ? round(($reportsTodayCount / $reportableStaffCount) * 100)
            : 0;

        // ── Widget data ──────────────────────────────────────────────────
        $myTasks = $this->safe(fn () => DB::table('tasks')
            ->where('assigned_to', $staff->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(), collect());

        $teamActivity = $this->safe(fn () => DB::table('task_comments as tc')
            ->join('staff as s', 'tc.staff_id', '=', 's.id')
            ->join('tasks as t', 'tc.task_id', '=', 't.id')
            ->select('s.name as staff_name', 't.title as task_title', 'tc.created_at')
            ->orderByDesc('tc.created_at')
            ->limit(8)
            ->get(), collect());

        $projects = $this->safe(fn () => DB::table('projects')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(), collect());

        // ── Assets Alert ─────────────────────────────────────────────────
        $assetAlerts = $this->safe(fn () => DB::table('asset_repairs as ar')
            ->join('assets as a', 'ar.asset_id', '=', 'a.id')
            ->select('a.name as asset_name', 'ar.issue', 'ar.status', 'ar.created_at')
            ->whereNotIn('ar.status', ['resolved', 'completed'])
            ->orderByDesc('ar.created_at')
            ->limit(6)
            ->get(), collect());

        // ── TouchPoint stats ─────────────────────────────────────────────
        $touchpointAtRisk   = $this->safe(fn () => DB::table('touchpoints')->where('status', 'at_risk')->count());
        $touchpointRenewals = $this->safe(fn () => DB::table('touchpoints')->where('status', 'renewal')->count());
        $touchpointHealthy  = $this->safe(fn () => DB::table('touchpoints')->where('status', 'healthy')->count());
        $touchpointTotal    = $this->safe(fn () => DB::table('touchpoints')->count());

        // ── Daily Reports (from admin.php logic) ─────────────────────────
        // Fetch today's submitted reports with staff details
        $reportsToday = $this->safe(fn () => DB::table('daily_reports as dr')
            ->join('staff as s', 'dr.staff_id', '=', 's.id')
            ->select(
                's.id as staff_id',
                's.name as staff_name',
                's.role',
                'dr.report_data',
                'dr.report_date',
                'dr.created_at'
            )
            ->whereDate('dr.report_date', today())
            ->orderByDesc('dr.report_date')
            ->get(), collect());

        // Staff who haven't submitted today (active, non-admin)
        $submittedIds = $reportsToday->pluck('staff_id')->toArray();
        $missingStaff = $this->safe(fn () => Staff::where('active', true)
            ->where('role', '!=', 'admin')
            ->whereNotIn('id', $submittedIds)
            ->get(['id', 'name', 'role']), collect());

        $roleFields = self::ROLE_FIELDS;
        $roleEmoji  = self::ROLE_EMOJI;

        return view('dashboard.index', compact(
            'staff', 'greeting', 'staffTotal', 'reportableStaffCount',
            'myTasksCount', 'teamOnlineCount', 'assetsNeedCheckup',
            'reportsTodayCount', 'submissionRate',
            'myTasks', 'teamActivity', 'projects',
            'assetAlerts',
            'touchpointAtRisk', 'touchpointRenewals', 'touchpointHealthy', 'touchpointTotal',
            'reportsToday', 'missingStaff', 'roleFields', 'roleEmoji'
        ));
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
