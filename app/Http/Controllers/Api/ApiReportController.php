<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Setting;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiReportController extends Controller
{
    const ROLE_LABELS = [
        'admin'     => 'Admin',
        'secretary' => 'Secretary',
        'sales_rep' => 'Sales Rep',
        'support'   => 'Support',
        'hr'        => 'HR',
        'finance'   => 'Finance',
        'developer' => 'Developer',
        'tester'    => 'Tester',
    ];

    const ROLE_EMOJIS = [
        'admin'     => '⚡',
        'secretary' => '📋',
        'sales_rep' => '💼',
        'support'   => '🎧',
        'hr'        => '👥',
        'finance'   => '💰',
        'developer' => '💻',
        'tester'    => '🧪',
    ];

    private function isAdmin($staff): bool
    {
        return in_array($staff->role, ['admin', 'secretary']);
    }

    private function fireWebhook(string $event, array $data): void
    {
        $url = Setting::get('webhook_url', '');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) return;

        $settingKey = match ($event) {
            'report.submitted' => 'notify_report_submitted',
            default            => null,
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

    private function formatReport(DailyReport $r, bool $decodeData = false): array
    {
        $role = $r->staff?->role ?? '';
        return [
            'id'           => $r->id,
            'name'         => $r->staff?->name,
            'role'         => $role,
            'report_data'  => $decodeData ? $r->report_data : json_encode($r->report_data),
            'submitted_at' => $r->submitted_at,
            'updated_at'   => $r->updated_at,
            'role_label'   => self::ROLE_LABELS[$role] ?? $role,
            'emoji'        => self::ROLE_EMOJIS[$role] ?? '',
            'time'         => $r->submitted_at?->format('h:i A'),
        ];
    }

    // POST /api/reports  — submit / update daily report
    public function submit(Request $request)
    {
        $staff = $request->user();
        $date  = $request->input('date', now()->toDateString());
        $data  = $request->input('data', []);

        $existing = DailyReport::where('staff_id', $staff->id)
            ->where('report_date', $date)
            ->first();

        $isUpdate = (bool) $existing;

        if ($existing) {
            $existing->report_data = $data;
            $existing->updated_at  = now();
            $existing->save();
        } else {
            DailyReport::create([
                'staff_id'    => $staff->id,
                'report_date' => $date,
                'report_data' => $data,
                'submitted_at' => now(),
            ]);

            $this->fireWebhook('report.submitted', [
                'staff_name' => $staff->name,
                'role'       => $staff->role,
                'date'       => $date,
            ]);
        }

        return response()->json(['ok' => true, 'updated' => $isUpdate]);
    }

    // GET /api/reports/summary?date=YYYY-MM-DD
    public function summary(Request $request)
    {
        $staff = $request->user();

        /*if (!$this->isAdmin($staff)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }*/

        $date    = $request->input('date', now()->toDateString());
        $reports = DailyReport::with('staff:id,name,role')
            ->where('report_date', $date)
            ->get()
            ->map(fn($r) => $this->formatReport($r, false));

        $totalStaff = Staff::where('active', true)->where('role', '!=', 'admin')->count();

        return response()->json([
            'date'        => $date,
            'reports'     => $reports,
            'total_staff' => $totalStaff,
            'submitted'   => $reports->count(),
            'pending'     => max(0, $totalStaff - $reports->count()),
        ]);
    }

    // GET /api/reports/today  — today's reports with decoded data
    public function today(Request $request)
    {
        $staff = $request->user();

        if (!$this->isAdmin($staff)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $today   = now()->toDateString();
        $reports = DailyReport::with('staff:id,name,role')
            ->where('report_date', $today)
            ->get()
            ->map(fn($r) => $this->formatReport($r, true));

        $totalStaff = Staff::where('active', true)->where('role', '!=', 'admin')->count();

        return response()->json([
            'date'        => $today,
            'reports'     => $reports,
            'total_staff' => $totalStaff,
            'submitted'   => $reports->count(),
            'pending'     => max(0, $totalStaff - $reports->count()),
        ]);
    }

    // GET /api/reports/missing
    public function missing(Request $request)
    {
        $staff = $request->user();
        
        /*if (!$this->isAdmin($staff)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }*/

        $today    = now()->toDateString();
        $reported = DailyReport::where('report_date', $today)->pluck('staff_id');

        $missing = Staff::where('active', true)
            ->where('role', '!=', 'admin')
            ->whereNotIn('id', $reported)
            ->get()
            ->map(fn($s) => ['name' => $s->name, 'role' => $s->role]);

        return response()->json([
            'date'    => $today,
            'missing' => $missing,
            'count'   => $missing->count(),
        ]);
    }
}
