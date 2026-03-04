<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\LoginHistory;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SettingsController extends Controller
{
    // Default values for settings that might not yet exist in DB
    const DEFAULTS = [
        'company_name'             => 'Getlead Analytics',
        'app_name'                 => 'Getlead HQ',
        'timezone'                 => 'Asia/Kolkata',
        'default_priority'        => 'normal',
        'auto_archive_days'       => '30',
        'report_deadline'         => '19:00',
        'weekend_reports'         => '0',
        'webhook_url'             => '',
        'notify_task_created'     => '1',
        'notify_task_completed'   => '1',
        'notify_task_overdue'     => '1',
        'notify_report_submitted' => '1',
        'notify_report_missing'   => '1',
        'session_timeout'         => '86400',
    ];

    // Keys that admin is allowed to update
    const ALLOWED_KEYS = [
        'company_name', 'app_name', 'timezone',
        'default_priority', 'auto_archive_days',
        'report_deadline', 'weekend_reports',
        'webhook_url',
        'notify_task_created', 'notify_task_completed', 'notify_task_overdue',
        'notify_report_submitted', 'notify_report_missing',
        'session_timeout',
    ];

    private function isAdmin(): bool
    {
        return in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary']);
    }

    public function index()
    {
        if (!$this->isAdmin()) abort(403);
        return view('settings.index');
    }

    // ── API: Get all settings ────────────────────────────────────────
    public function apiGet()
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);

        $stored   = Setting::getAll();
        $settings = array_merge(self::DEFAULTS, $stored);

        // Database stats
        $dbName  = DB::getDatabaseName();
        $dbBytes = DB::selectOne("
            SELECT SUM(data_length + index_length) AS size
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$dbName])?->size ?? 0;

        $settings['_stats'] = [
            'total_tasks'    => Task::count(),
            'total_reports'  => DailyReport::count(),
            'total_staff'    => Staff::count(),
            'total_comments' => TaskComment::count(),
            'db_size'        => $this->formatBytes((int) $dbBytes),
        ];

        return response()->json(['ok' => true, 'settings' => $settings]);
    }

    // ── API: Update one setting ──────────────────────────────────────
    public function apiUpdate(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);

        $data = $request->validate([
            'key'   => 'required|string',
            'value' => 'nullable|string|max:1000',
        ]);

        if (!in_array($data['key'], self::ALLOWED_KEYS)) {
            return response()->json(['ok' => false, 'error' => 'Invalid setting key']);
        }

        Setting::set($data['key'], $data['value'] ?? '');

        return response()->json(['ok' => true]);
    }

    // ── API: Login history ───────────────────────────────────────────
    public function apiLoginHistory()
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);

        $history = LoginHistory::with('staff:id,name')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(fn($h) => [
                'staff_name' => $h->staff?->name ?? 'Unknown',
                'ip_address' => $h->ip_address,
                'created_at' => $h->created_at,
            ]);

        return response()->json(['ok' => true, 'history' => $history]);
    }

    // ── Export: All data as JSON ─────────────────────────────────────
    public function exportData()
    {
        if (!$this->isAdmin()) abort(403);

        $data = [
            'exported_at' => now()->toIso8601String(),
            'staff'       => Staff::all(['id', 'name', 'role', 'mobile', 'active', 'created_at']),
            'tasks'       => Task::with('assignee:id,name', 'creator:id,name')->get(),
            'reports'     => DailyReport::with('staff:id,name')->get(),
            'settings'    => Setting::all(),
        ];

        return Response::make(
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            200,
            [
                'Content-Type'        => 'application/json',
                'Content-Disposition' => 'attachment; filename="getlead-export-' . now()->format('Y-m-d') . '.json"',
            ]
        );
    }

    // ── Export: Reports as CSV ───────────────────────────────────────
    public function exportReports()
    {
        if (!$this->isAdmin()) abort(403);

        $reports = DailyReport::with('staff:id,name,role')
            ->orderBy('report_date', 'desc')
            ->get();

        $rows   = [];
        $rows[] = ['ID', 'Staff', 'Role', 'Date', 'Submitted At', 'Data (JSON)'];
        foreach ($reports as $r) {
            $rows[] = [
                $r->id,
                $r->staff?->name ?? 'Unknown',
                $r->staff?->role ?? '',
                $r->report_date,
                $r->created_at,
                json_encode($r->report_data),
            ];
        }

        $csv = implode("\n", array_map(fn($row) =>
            implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row))
        , $rows));

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="getlead-reports-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2)    . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2)       . ' KB';
        return $bytes . ' B';
    }
}
