<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $staff      = Auth::guard('staff')->user();
        $reportDate = $request->get('date', today()->toDateString());

        // Sanitise date
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $reportDate)) {
            $reportDate = today()->toDateString();
        }

        $existing = DailyReport::where('staff_id', $staff->id)
            ->where('report_date', $reportDate)
            ->first();

        $recentReports = DailyReport::where('staff_id', $staff->id)
            ->orderByDesc('report_date')
            ->limit(7)
            ->get(['report_date', 'submitted_at']);

        return view('daily-report.index', compact(
            'staff', 'reportDate', 'existing', 'recentReports'
        ));
    }

    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $date  = $request->input('date', today()->toDateString());

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['ok' => false, 'error' => 'Invalid date']);
        }

        $data = $request->input('data', []);

        $existing = DailyReport::where('staff_id', $staff->id)
            ->where('report_date', $date)
            ->first();

        if ($existing) {
            $existing->report_data = $data;
            $existing->updated_at  = now();
            $existing->save();
        } else {
            DailyReport::create([
                'staff_id'     => $staff->id,
                'report_date'  => $date,
                'report_data'  => $data,
                'submitted_at' => now(),
            ]);
        }

        return response()->json(['ok' => true, 'updated' => (bool) $existing]);
    }

    public function recent()
    {
        $staff = Auth::guard('staff')->user();

        $reports = DailyReport::where('staff_id', $staff->id)
            ->orderByDesc('report_date')
            ->limit(7)
            ->get(['report_date', 'submitted_at']);

        return response()->json($reports->map(fn ($r) => [
            'date'     => $r->report_date->toDateString(),
            'label'    => $r->report_date->format('D, j M'),
            'time'     => $r->submitted_at?->format('h:i A') ?? '—',
            'is_today' => $r->report_date->isToday(),
        ]));
    }
}
