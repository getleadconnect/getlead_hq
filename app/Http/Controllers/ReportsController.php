<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    const ROLE_LABELS = [
        'sales_rep'  => ['label' => 'Sales Rep',  'emoji' => '💼'],
        'secretary'  => ['label' => 'Secretary',   'emoji' => '📋'],
        'support'    => ['label' => 'Support',     'emoji' => '🎫'],
        'hr'         => ['label' => 'HR',          'emoji' => '👥'],
        'finance'    => ['label' => 'Finance',     'emoji' => '💰'],
        'developer'  => ['label' => 'Developer',   'emoji' => '💻'],
        'tester'     => ['label' => 'Tester',      'emoji' => '🧪'],
        'admin'      => ['label' => 'Admin',       'emoji' => '⚡'],
    ];

    private function isAdmin(): bool
    {
        return in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary']);
    }

    public function index()
    {
        if (! $this->isAdmin()) abort(403);

        $staffList = Staff::where('active', 1)
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return view('reports.index', compact('staffList'));
    }

    public function apiSummary(Request $request)
    {
        if (! $this->isAdmin()) return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);

        $date     = $request->date ?: today()->format('Y-m-d');
        $memberId = $request->member_id;

        $q = DailyReport::with('staff')->where('report_date', $date);
        if ($memberId) $q->where('staff_id', $memberId);

        $reports = $q->orderByDesc('submitted_at')->get()->map(function ($r) {
            $role  = $r->staff->role ?? 'staff';
            $meta  = self::ROLE_LABELS[$role] ?? ['label' => ucfirst($role), 'emoji' => '👤'];
            return [
                'id'          => $r->staff->id,
                'name'        => $r->staff->name,
                'role'        => $role,
                'role_label'  => $meta['label'],
                'emoji'       => $meta['emoji'],
                'report_data' => $r->report_data,
                'time'        => $r->submitted_at?->format('h:i A') ?? '—',
            ];
        });

        // Missing staff (not submitted today)
        $submittedIds = DailyReport::where('report_date', $date)->pluck('staff_id')->toArray();
        $missing = Staff::where('active', 1)
            ->where('role', '!=', 'admin')
            ->whereNotIn('id', $submittedIds)
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->map(function ($s) {
                $meta = self::ROLE_LABELS[$s->role] ?? ['label' => ucfirst($s->role), 'emoji' => '👤'];
                return ['id' => $s->id, 'name' => $s->name, 'role_label' => $meta['label']];
            });

        return response()->json([
            'ok'      => true,
            'reports' => $reports,
            'pending' => $missing->count(),
            'missing' => $missing,
        ]);
    }
}
