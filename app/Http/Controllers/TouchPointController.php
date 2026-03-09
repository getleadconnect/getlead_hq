<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\TpCallLog;
use App\Models\TpCustomer;
use App\Models\TpTouchpoint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TouchPointController extends Controller
{
    // ── Constants ──────────────────────────────────────────────────
    const TOUCHPOINTS = [
        'usage_check' => ['1_month' => 7,  '3_month' => 30, '1_year' => 90],
        'payment'     => ['1_month' => 3,  '3_month' => 7,  '1_year' => 15],
    ];

    const SUB_DAYS = [
        '1_month' => 30, '3_month' => 90, '1_year' => 365,
        'free_trial' => 7, 'extended_trial' => 14,
    ];

    const ONBOARDING = [
        'welcome_call' => 0, 'setup_check' => 2, 'feature_walkthrough' => 4,
        'usage_checkin' => 6, 'conversion_nudge' => 7,
    ];

    const EXTENDED_ONBOARDING = [
        'extended_checkin' => 10, 'final_conversion' => 14,
    ];

    const ONBOARDING_STAGES = [
        'welcome_call', 'setup_check', 'feature_walkthrough',
        'usage_checkin', 'conversion_nudge', 'extended_checkin', 'final_conversion',
    ];

    const STAGES = [
        'usage_check'        => 'Usage Check',
        'payment'            => 'Payment Collection',
        'welcome_call'       => '🚀 Welcome Call',
        'setup_check'        => '🚀 Setup Check',
        'feature_walkthrough'=> '🚀 Feature Walkthrough',
        'usage_checkin'      => '🚀 Usage Check-in',
        'conversion_nudge'   => '🚀 Conversion Nudge',
        'extended_checkin'   => '🚀 Extended Check-in',
        'final_conversion'   => '🚀 Final Conversion',
    ];

    const OUTCOMES = [
        'usage_check'         => ['using_well'=>'Using Well','needs_help'=>'Needs Help','not_using'=>'Not Using','wants_cancel'=>'Wants to Cancel'],
        'payment'             => ['paid'=>'Paid','promised'=>'Promised Date','issue'=>'Payment Issue','churned'=>'Churned'],
        'welcome_call'        => ['connected_setup'=>'Set up together','self_setup'=>'Will do themselves','needs_help'=>'Needs more help','no_answer'=>'No answer'],
        'setup_check'         => ['setup_done'=>'Fully set up','partial'=>'Partially done','not_started'=>"Haven't started",'no_answer'=>'No answer'],
        'feature_walkthrough' => ['engaged'=>'Actively using','interested'=>'Showed interest','confused'=>'Needs more help','no_answer'=>'No answer'],
        'usage_checkin'       => ['aha_reached'=>'Using well, seeing value','struggling'=>'Using but struggling','inactive'=>'Not using','no_answer'=>'No answer'],
        'conversion_nudge'    => ['converted'=>'Paid! 🎉','extending'=>'Extended trial to 14d','considering'=>'Thinking about it','churned'=>'Not interested'],
        'extended_checkin'    => ['converted'=>'Paid! 🎉','needs_push'=>'Needs push','likely_churn'=>'Likely to churn','no_answer'=>'No answer'],
        'final_conversion'    => ['converted'=>'Paid! 🎉','churned'=>'Not interested','special_offer'=>'Needs discount/deal'],
    ];

    const CALL_OUTCOMES = [
        'connected'    => 'Connected',
        'no_answer'    => 'No Answer',
        'busy'         => 'Busy',
        'callback'     => 'Callback Scheduled',
        'wrong_number' => 'Wrong Number',
    ];

    // ── Helpers ────────────────────────────────────────────────────
    private function staff()
    {
        return Auth::guard('staff')->user();
    }

    private function isAdmin(): bool
    {
        return in_array($this->staff()->role, ['admin', 'secretary']);
    }

    private function staffMap(): array
    {
        return Staff::where('active', 1)->pluck('name', 'id')->toArray();
    }

    private function generateTouchpoints(int $customerId): void
    {
        $c = TpCustomer::find($customerId);
        if (!$c) return;

        if (in_array($c->subscription_type, ['free_trial', 'extended_trial'])) {
            $this->generateOnboarding($customerId);
            return;
        }

        $today   = Carbon::today();
        $expiry  = Carbon::parse($c->expiry_date);
        $sub     = $c->subscription_type;
        $pending = TpTouchpoint::where('customer_id', $customerId)
            ->where('due_date', '>=', $today)
            ->count();
        if ($pending > 0) return;

        foreach (['usage_check', 'payment'] as $stage) {
            $days = self::TOUCHPOINTS[$stage][$sub] ?? 7;
            $due  = $expiry->copy()->subDays($days)->format('Y-m-d');
            if ($due >= $today->format('Y-m-d')) {
                TpTouchpoint::create([
                    'customer_id' => $customerId,
                    'stage'       => $stage,
                    'due_date'    => $due,
                ]);
            }
        }
    }

    private function generateOnboarding(int $customerId, bool $extendedOnly = false): void
    {
        $c = TpCustomer::find($customerId);
        if (!$c) return;

        $startDate = Carbon::parse($c->start_date);

        // Get support staff for assignment (Arya or Anjali)
        $supportStaff = Staff::where('active', 1)
            ->where(function ($q) {
                $q->where('name', 'like', '%Arya%')
                  ->orWhere('name', 'like', '%Anjali%');
            })
            ->pluck('id')
            ->toArray();

        if (empty($supportStaff)) {
            $supportStaff = Staff::where('active', 1)->pluck('id')->toArray();
        }

        $stages = $extendedOnly ? self::EXTENDED_ONBOARDING : self::ONBOARDING;
        if (!$extendedOnly && $c->subscription_type === 'extended_trial') {
            $stages = array_merge(self::ONBOARDING, self::EXTENDED_ONBOARDING);
        }

        $staffCount = count($supportStaff);
        $staffIdx   = 0;
        foreach ($stages as $stage => $dayOffset) {
            $exists = TpTouchpoint::where('customer_id', $customerId)
                ->where('stage', $stage)
                ->exists();
            if ($exists) continue;

            TpTouchpoint::create([
                'customer_id' => $customerId,
                'stage'       => $stage,
                'due_date'    => $startDate->copy()->addDays($dayOffset)->format('Y-m-d'),
                'assigned_to' => $staffCount > 0 ? $supportStaff[$staffIdx % $staffCount] : null,
            ]);
            $staffIdx++;
        }
    }

    // ── Main view ──────────────────────────────────────────────────
    public function index()
    {
        $staffList = Staff::where('active', 1)->orderBy('name')->get(['id', 'name', 'role']);
        $staffMap  = $staffList->pluck('name', 'id')->toArray();
        $isAdmin   = $this->isAdmin();
        $staffId   = $this->staff()->id;

        return view('touchpoint.index', compact('staffList', 'staffMap', 'isAdmin', 'staffId'));
    }

    // ── API: Dashboard ─────────────────────────────────────────────
    public function apiDashboard()
    {
        $staffId = $this->staff()->id;
        $isAdmin = $this->isAdmin();
        $today   = Carbon::today()->format('Y-m-d');
        $weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');

        $q = TpTouchpoint::query();
        if (!$isAdmin) $q->where('assigned_to', $staffId);

        $overdue       = (clone $q)->where('due_date', '<', $today)->where('status', 'pending')->count();
        $todayCount    = (clone $q)->where('due_date', $today)->where('status', 'pending')->count();
        $upcoming      = (clone $q)->whereBetween('due_date', [
            Carbon::tomorrow()->format('Y-m-d'),
            Carbon::today()->addDays(7)->format('Y-m-d'),
        ])->where('status', 'pending')->count();
        $completedWeek = (clone $q)->where('status', 'completed')
            ->where('completed_at', '>=', $weekStart)->count();

        // Health breakdown
        $health = ['healthy' => 0, 'at_risk' => 0, 'critical' => 0, 'unknown' => 0];
        TpCustomer::where('status', 'active')
            ->selectRaw('health, COUNT(*) as c')
            ->groupBy('health')
            ->get()
            ->each(function ($r) use (&$health) {
                $key = $r->health === 'churning' ? 'critical' : $r->health;
                if (isset($health[$key])) $health[$key] = $r->c;
            });

        // Renewal pipeline (next 4 weeks)
        $pipeline = [];
        $mondayThisWeek = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 4; $i++) {
            $ws = $mondayThisWeek->copy()->addWeeks($i)->format('Y-m-d');
            $we = $mondayThisWeek->copy()->addWeeks($i)->addDays(6)->format('Y-m-d');
            $cnt = TpCustomer::where('status', 'active')
                ->whereBetween('expiry_date', [$ws, $we])->count();
            $pipeline[] = [
                'week'  => 'Week ' . ($i + 1),
                'range' => Carbon::parse($ws)->format('d M') . ' – ' . Carbon::parse($we)->format('d M'),
                'count' => $cnt,
            ];
        }

        // Upcoming/overdue touchpoints list
        $listQ = TpTouchpoint::with(['customer', 'assignee'])
            ->where('status', 'pending')
            ->where('due_date', '<=', $today)
            ->orderBy('due_date');
        if (!$isAdmin) $listQ->where('assigned_to', $staffId);
        $upcomingList = $listQ->limit(10)->get()->map(function ($t) {
            return [
                'id'            => $t->id,
                'customer_name' => $t->customer->name,
                'company'       => $t->customer->company,
                'phone'         => $t->customer->phone,
                'expiry_date'   => $t->customer->expiry_date?->format('Y-m-d'),
                'stage'         => $t->stage,
                'due_date'      => $t->due_date?->format('Y-m-d'),
                'assigned_to'   => $t->assigned_to,
                'assigned_name' => $t->assignee?->name,
            ];
        });

        // Active trials
        $activeTrials = TpCustomer::whereIn('subscription_type', ['free_trial', 'extended_trial'])
            ->where('status', 'active')->count();

        // Trial conversion rate
        $totalTrials    = TpCustomer::whereIn('subscription_type', ['free_trial', 'extended_trial'])->count();
        $convertedTrials = TpTouchpoint::whereIn('stage', ['conversion_nudge', 'extended_checkin', 'final_conversion'])
            ->where('outcome', 'converted')->count();
        $trialConvRate = $totalTrials > 0 ? round($convertedTrials / max($totalTrials, 1) * 100) : 0;

        // Onboarding pipeline
        $onboardingPipeline = [];
        foreach (self::ONBOARDING_STAGES as $os) {
            $cnt  = TpTouchpoint::where('stage', $os)->where('status', 'pending')->count();
            $done = TpTouchpoint::where('stage', $os)->where('status', 'completed')->count();
            if ($cnt > 0 || $done > 0) {
                $onboardingPipeline[] = ['stage' => $os, 'pending' => $cnt, 'done' => $done];
            }
        }

        return response()->json([
            'ok'                  => true,
            'overdue'             => $overdue,
            'today'               => $todayCount,
            'upcoming'            => $upcoming,
            'completed_week'      => $completedWeek,
            'health'              => $health,
            'pipeline'            => $pipeline,
            'upcoming_list'       => $upcomingList,
            'active_trials'       => $activeTrials,
            'trial_conv_rate'     => $trialConvRate,
            'onboarding_pipeline' => $onboardingPipeline,
        ]);
    }

    // ── API: Customers ─────────────────────────────────────────────
    public function apiCustomers(Request $request)
    {
        $q = TpCustomer::query();
        if ($s = $request->search) {
            $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('company', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }
        if ($st = $request->status) $q->where('status', $st);
        if ($sub = $request->subscription) $q->where('subscription_type', $sub);

        $customers = $q->orderBy('expiry_date')->get()->map(fn($c) => [
            'id'                => $c->id,
            'name'              => $c->name,
            'company'           => $c->company,
            'phone'             => $c->phone,
            'email'             => $c->email,
            'subscription_type' => $c->subscription_type,
            'start_date'        => $c->start_date?->format('Y-m-d'),
            'expiry_date'       => $c->expiry_date?->format('Y-m-d'),
            'status'            => $c->status,
            'health'            => $c->health,
            'notes'             => $c->notes,
        ]);

        return response()->json(['ok' => true, 'customers' => $customers]);
    }

    public function apiCustomerSave(Request $request)
    {
        $data       = $request->all();
        $subType    = $data['subscription_type'];
        $expiryDate = $data['expiry_date'];

        if (in_array($subType, ['free_trial', 'extended_trial']) && !empty($data['start_date'])) {
            $trialDays  = $subType === 'free_trial' ? 7 : 14;
            $expiryDate = Carbon::parse($data['start_date'])->addDays($trialDays)->format('Y-m-d');
        }

        if ($id = $data['id'] ?? null) {
            $c = TpCustomer::findOrFail($id);
            $c->update([
                'name'              => $data['name'],
                'company'           => $data['company'] ?? null,
                'phone'             => $data['phone'],
                'email'             => $data['email'] ?? null,
                'subscription_type' => $subType,
                'start_date'        => $data['start_date'],
                'expiry_date'       => $expiryDate,
                'status'            => $data['status'] ?? 'active',
                'health'            => $data['health'] ?? 'unknown',
                'notes'             => $data['notes'] ?? null,
            ]);
            $this->generateTouchpoints($id);
            return response()->json(['ok' => true, 'msg' => 'Customer updated']);
        } else {
            $c = TpCustomer::create([
                'name'              => $data['name'],
                'company'           => $data['company'] ?? null,
                'phone'             => $data['phone'],
                'email'             => $data['email'] ?? null,
                'subscription_type' => $subType,
                'start_date'        => $data['start_date'],
                'expiry_date'       => $expiryDate,
                'notes'             => $data['notes'] ?? null,
            ]);
            $this->generateTouchpoints($c->id);
            return response()->json(['ok' => true, 'msg' => 'Customer added', 'id' => $c->id]);
        }
    }

    public function apiCustomerDelete(Request $request)
    {
        $c = TpCustomer::findOrFail($request->id);
        TpCallLog::whereIn('touchpoint_id', $c->touchpoints()->pluck('id'))->delete();
        $c->touchpoints()->delete();
        $c->delete();
        return response()->json(['ok' => true]);
    }

    public function apiCustomerRegen(Request $request)
    {
        TpTouchpoint::where('customer_id', $request->id)
            ->where('status', 'pending')
            ->delete();
        $this->generateTouchpoints($request->id);
        return response()->json(['ok' => true]);
    }

    // ── API: Touchpoints ───────────────────────────────────────────
    public function apiTouchpoints(Request $request)
    {
        $staffId = $this->staff()->id;
        $isAdmin = $this->isAdmin();
        $today   = Carbon::today()->format('Y-m-d');

        $q = TpTouchpoint::with(['customer', 'assignee'])
            ->select('tp_touchpoints.*');

        if ($stage = $request->stage)   $q->where('stage', $stage);
        if ($status = $request->tp_status) $q->where('status', $status);

        if (!$isAdmin) {
            $q->where('assigned_to', $staffId);
        } elseif ($assigned = $request->assigned) {
            if ($assigned === 'unassigned') $q->whereNull('assigned_to');
            else $q->where('assigned_to', $assigned);
        }

        if ($df = $request->date_filter) {
            if ($df === 'overdue')  $q->where('due_date', '<', $today);
            elseif ($df === 'today') $q->where('due_date', $today);
            elseif ($df === 'upcoming') $q->whereBetween('due_date', [
                Carbon::tomorrow()->format('Y-m-d'),
                Carbon::today()->addDays(7)->format('Y-m-d'),
            ]);
        }

        $touchpoints = $q->orderBy('due_date')->get()->map(function ($t) {
            return [
                'id'            => $t->id,
                'customer_id'   => $t->customer_id,
                'customer_name' => $t->customer->name,
                'company'       => $t->customer->company,
                'phone'         => $t->customer->phone,
                'email'         => $t->customer->email,
                'expiry_date'   => $t->customer->expiry_date?->format('Y-m-d'),
                'stage'         => $t->stage,
                'due_date'      => $t->due_date?->format('Y-m-d'),
                'assigned_to'   => $t->assigned_to,
                'assigned_name' => $t->assignee?->name,
                'status'        => $t->status,
                'outcome'       => $t->outcome,
                'outcome_notes' => $t->outcome_notes,
                'completed_at'  => $t->completed_at?->toDateTimeString(),
            ];
        });

        return response()->json(['ok' => true, 'touchpoints' => $touchpoints]);
    }

    public function apiTpAssign(Request $request)
    {
        TpTouchpoint::findOrFail($request->id)->update(['assigned_to' => $request->assigned_to ?: null]);
        return response()->json(['ok' => true]);
    }

    public function apiTpComplete(Request $request)
    {
        $tpId    = $request->id;
        $outcome = $request->outcome;
        $notes   = $request->notes ?? '';

        $t = TpTouchpoint::findOrFail($tpId);
        $t->update([
            'status'        => 'completed',
            'outcome'       => $outcome,
            'outcome_notes' => $notes,
            'completed_at'  => now(),
        ]);

        $stage = $t->stage;
        $health = null;
        $cStatus = null;
        $extra = [];

        if ($stage === 'usage_check') {
            if ($outcome === 'using_well')   $health = 'healthy';
            elseif (in_array($outcome, ['needs_help', 'not_using'])) $health = 'at_risk';
            elseif ($outcome === 'wants_cancel') $health = 'churning';
        }
        if ($stage === 'payment') {
            if ($outcome === 'paid')    { $health = 'healthy'; $cStatus = 'renewed'; }
            elseif ($outcome === 'churned') { $health = 'churning'; $cStatus = 'churned'; }
        }

        if (in_array($outcome, ['aha_reached', 'engaged', 'connected_setup'])) $health = 'healthy';
        if (in_array($outcome, ['partial', 'struggling', 'interested']))        $health = 'at_risk';
        if (in_array($outcome, ['inactive', 'not_started']))                    $health = 'critical';
        if ($outcome === 'confused')     $health = 'at_risk';
        if ($outcome === 'no_answer' && in_array($stage, self::ONBOARDING_STAGES)) $health = 'at_risk';

        if ($outcome === 'converted') {
            $health = 'healthy';
            $cStatus = 'active';
            $extra['prompt_plan'] = true;
        }
        if ($outcome === 'extending' && $stage === 'conversion_nudge') {
            $c = TpCustomer::find($t->customer_id);
            if ($c) {
                $newExpiry = Carbon::parse($c->start_date)->addDays(14)->format('Y-m-d');
                $c->update(['subscription_type' => 'extended_trial', 'expiry_date' => $newExpiry]);
                $this->generateOnboarding($t->customer_id, true);
                $extra['extended'] = true;
            }
        }
        if ($outcome === 'churned')      { $health = 'churning'; $cStatus = 'churned'; }
        if ($outcome === 'likely_churn') $health = 'churning';
        if ($outcome === 'needs_push')   $health = 'at_risk';

        if ($health)  TpCustomer::where('id', $t->customer_id)->update(['health'  => $health]);
        if ($cStatus) TpCustomer::where('id', $t->customer_id)->update(['status' => $cStatus]);

        return response()->json(array_merge(['ok' => true], $extra));
    }

    public function apiExtendTrial(Request $request)
    {
        $c = TpCustomer::findOrFail($request->id);
        if ($c->subscription_type !== 'free_trial') {
            return response()->json(['ok' => false, 'error' => 'Customer is not on a free trial']);
        }
        $newExpiry = Carbon::parse($c->start_date)->addDays(14)->format('Y-m-d');
        $c->update(['subscription_type' => 'extended_trial', 'expiry_date' => $newExpiry]);
        $this->generateOnboarding($c->id, true);
        return response()->json(['ok' => true, 'msg' => 'Trial extended to 14 days', 'new_expiry' => $newExpiry]);
    }

    public function apiConvertTrial(Request $request)
    {
        $plan = $request->plan;
        if (!isset(self::SUB_DAYS[$plan])) {
            return response()->json(['ok' => false, 'error' => 'Invalid plan']);
        }
        $c         = TpCustomer::findOrFail($request->id);
        $startDate = Carbon::today()->format('Y-m-d');
        $expiry    = Carbon::today()->addDays(self::SUB_DAYS[$plan])->format('Y-m-d');
        $c->update([
            'subscription_type' => $plan,
            'start_date'        => $startDate,
            'expiry_date'       => $expiry,
            'status'            => 'active',
            'health'            => 'healthy',
        ]);
        TpTouchpoint::where('customer_id', $c->id)
            ->where('status', 'pending')
            ->whereIn('stage', self::ONBOARDING_STAGES)
            ->delete();
        $this->generateTouchpoints($c->id);
        return response()->json(['ok' => true, 'msg' => 'Converted to ' . $plan . ' plan!']);
    }

    public function apiTpBulkAssign(Request $request)
    {
        $ids = $request->ids ?? [];
        $to  = $request->assigned_to;
        TpTouchpoint::whereIn('id', $ids)->update(['assigned_to' => $to ?: null]);
        return response()->json(['ok' => true, 'count' => count($ids)]);
    }

    // ── API: Call Logs ─────────────────────────────────────────────
    public function apiLogCall(Request $request)
    {
        TpCallLog::create([
            'touchpoint_id'  => $request->touchpoint_id,
            'called_by'      => $request->called_by ?? $this->staff()->id,
            'outcome'        => $request->outcome,
            'notes'          => $request->notes ?? null,
            'follow_up_date' => $request->follow_up_date ?? null,
        ]);
        return response()->json(['ok' => true]);
    }

    public function apiCallLogs(Request $request)
    {
        $logs = TpCallLog::with('caller')
            ->where('touchpoint_id', $request->touchpoint_id)
            ->orderByDesc('call_time')
            ->get()
            ->map(fn($l) => [
                'id'              => $l->id,
                'caller_name'     => $l->caller?->name ?? 'Unknown',
                'call_time'       => $l->call_time?->toDateTimeString(),
                'outcome'         => $l->outcome,
                'notes'           => $l->notes,
                'follow_up_date'  => $l->follow_up_date?->format('Y-m-d'),
            ]);
        return response()->json(['ok' => true, 'logs' => $logs]);
    }

    // ── API: Reports ───────────────────────────────────────────────
    public function apiReports(Request $request)
    {
        $period    = $request->period ?? 'week';
        $startDate = match ($period) {
            'month' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'all'   => '2000-01-01',
            default => Carbon::now()->startOfWeek()->format('Y-m-d'),
        };
        $today = Carbon::today()->format('Y-m-d');

        $total    = TpCustomer::count();
        $active   = TpCustomer::where('status', 'active')->count();
        $churned  = TpCustomer::where('status', 'churned')->count();
        $renewed  = TpCustomer::where('status', 'renewed')->count();
        $completedTasks = TpTouchpoint::where('status', 'completed')
            ->where('completed_at', '>=', $startDate)->count();
        $overdueTasks = TpTouchpoint::where('due_date', '<', $today)
            ->where('status', 'pending')->count();
        $totalCalls = TpCallLog::where('call_time', '>=', $startDate)->count();
        $retention  = $total > 0 ? round(($active + $renewed) / $total * 100) : 0;

        // Usage/payment outcomes
        $usageOutcomes   = [];
        $paymentOutcomes = [];
        TpTouchpoint::where('stage', 'usage_check')->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->selectRaw('outcome, COUNT(*) as c')->groupBy('outcome')
            ->get()->each(fn($r) => $usageOutcomes[$r->outcome] = $r->c);
        TpTouchpoint::where('stage', 'payment')->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->selectRaw('outcome, COUNT(*) as c')->groupBy('outcome')
            ->get()->each(fn($r) => $paymentOutcomes[$r->outcome] = $r->c);

        // Team performance
        $teamPerf  = [];
        Staff::where('active', 1)->get()->each(function ($s) use (&$teamPerf, $startDate) {
            $completed = TpTouchpoint::where('assigned_to', $s->id)->where('status', 'completed')
                ->where('completed_at', '>=', $startDate)->count();
            $pending   = TpTouchpoint::where('assigned_to', $s->id)->where('status', 'pending')->count();
            $calls     = TpCallLog::where('called_by', $s->id)->where('call_time', '>=', $startDate)->count();
            if ($completed > 0 || $pending > 0 || $calls > 0) {
                $teamPerf[] = ['name' => $s->name, 'role' => $s->role, 'completed' => $completed, 'pending' => $pending, 'calls' => $calls];
            }
        });

        // Onboarding metrics
        $trialSignups  = TpCustomer::whereIn('subscription_type', ['free_trial', 'extended_trial'])->count();
        $trialConverted = TpTouchpoint::whereIn('stage', ['conversion_nudge', 'extended_checkin', 'final_conversion'])
            ->where('outcome', 'converted')->where('completed_at', '>=', $startDate)->count();
        $trialConvRate = $trialSignups > 0 ? round($trialConverted / $trialSignups * 100) : 0;

        $avgDays = TpTouchpoint::join('tp_customers', 'tp_customers.id', '=', 'tp_touchpoints.customer_id')
            ->where('tp_touchpoints.outcome', 'converted')
            ->whereIn('tp_touchpoints.stage', ['conversion_nudge', 'extended_checkin', 'final_conversion'])
            ->where('tp_touchpoints.completed_at', '>=', $startDate)
            ->selectRaw('AVG(DATEDIFF(tp_touchpoints.completed_at, tp_customers.start_date)) as avg_days')
            ->value('avg_days');

        $stageDropOff = [];
        foreach (['welcome_call','setup_check','feature_walkthrough','usage_checkin','conversion_nudge'] as $os) {
            $stageDropOff[$os] = TpTouchpoint::where('stage', $os)->where('status', 'completed')
                ->where('completed_at', '>=', $startDate)->count();
        }

        return response()->json([
            'ok'                  => true,
            'total'               => $total,
            'active'              => $active,
            'churned'             => $churned,
            'renewed'             => $renewed,
            'completed_tasks'     => $completedTasks,
            'overdue_tasks'       => $overdueTasks,
            'total_calls'         => $totalCalls,
            'retention'           => $retention,
            'usage_outcomes'      => $usageOutcomes,
            'payment_outcomes'    => $paymentOutcomes,
            'team'                => $teamPerf,
            'trial_signups'       => $trialSignups,
            'trial_converted'     => $trialConverted,
            'trial_conv_rate'     => $trialConvRate,
            'avg_days_to_convert' => $avgDays ? round($avgDays, 1) : null,
            'stage_drop_off'      => $stageDropOff,
        ]);
    }
}
