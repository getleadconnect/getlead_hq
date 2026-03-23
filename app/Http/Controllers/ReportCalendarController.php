<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportCalendarController extends Controller
{
    public function index()
    {
        return view('report-calendar.index');
    }

    public function apiData(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month); // 1-indexed

        // Clamp to valid range
        $year  = max(2020, min(2099, $year));
        $month = max(1,    min(12,   $month));

        $deadline = DB::table('settings')->where('key', 'report_deadline')->value('value') ?? '19:00';
        [$deadlineHour, $deadlineMin] = array_pad(explode(':', $deadline), 2, '00');

        $startDate   = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate     = Carbon::create($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;
        $today       = Carbon::today();

        // Active staff ordered by name
        $staffList = Staff::where('active', 1)->orderBy('name')->get();

        // All reports for this month, keyed by "staff_id_day"
        $reports = DB::table('daily_reports')
            ->whereBetween('report_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(fn($r) => $r->staff_id . '_' . Carbon::parse($r->report_date)->day);

        // Avatar colour palette
        $colors = [
            '#3B82F6','#10B981','#E5484D','#0D9B8C','#EC4899',
            '#F0A30A','#6366F1','#0891B2','#0EA5E9','#D97706',
            '#30A46C','#F97316','#F43F5E','#14B8A6','#8B5CF6','#2563EB',
        ];

        $staffData = $staffList->values()->map(function ($s, $i) use ($colors) {
            $words    = preg_split('/\s+/', trim($s->name));
            $initials = strtoupper(
                substr($words[0], 0, 1) .
                (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0], 1, 1))
            );
            return [
                'id'       => $s->id,
                'name'     => $s->name,
                'role'     => ucwords(str_replace('_', ' ', $s->role)),
                'initials' => $initials,
                'color'    => $colors[$i % count($colors)],
            ];
        });

        // Build report matrix  [staff_id][day] => {status, time?}
        $matrix = [];
        foreach ($staffList as $s) {
            $matrix[$s->id] = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date      = Carbon::create($year, $month, $d);
                $isWeekend = in_array($date->dayOfWeek, [0, 6]);
                $isFuture  = $date->gt($today);

                if ($isWeekend) {
                    $matrix[$s->id][$d] = ['status' => 'weekend'];
                } elseif ($isFuture) {
                    $matrix[$s->id][$d] = ['status' => 'future'];
                } else {
                    $report = $reports->get($s->id . '_' . $d);
                    if ($report) {
                        $submittedAt = Carbon::parse($report->submitted_at);
                        $deadlineDt  = Carbon::create($year, $month, $d, (int)$deadlineHour, (int)$deadlineMin, 0);
                        $status      = $submittedAt->gt($deadlineDt) ? 'late' : 'submitted';
                        $matrix[$s->id][$d] = ['status' => $status, 'time' => $submittedAt->format('h:i A')];
                    } else {
                        $matrix[$s->id][$d] = ['status' => 'missing'];
                    }
                }
            }
        }

        return response()->json([
            'ok'          => true,
            'staff'       => $staffData,
            'reports'     => $matrix,
            'daysInMonth' => $daysInMonth,
            'year'        => $year,
            'month'       => $month - 1, // JS uses 0-indexed months
            'todayDay'    => (int) $today->format('j'),
            'todayMonth'  => $today->month - 1,
            'todayYear'   => $today->year,
        ]);
    }
}
