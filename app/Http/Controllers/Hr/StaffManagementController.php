<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrAttendance;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use App\Models\HrEmployee;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

/**
 * HR › Staff Section.
 *
 * Self-service area for a staff member — a vertical-tab page (Dashboard,
 * Attendance, Leave Management) mirroring the Settings layout. The Dashboard
 * summarises the signed-in staff's own attendance and leave. Only the Dashboard
 * tab is implemented for now; the other two are placeholders for a later phase.
 */
class StaffManagementController extends Controller
{
    /** Staff section page — defaults to the Dashboard tab. */
    public function index()
    {
        $employeeId = $this->resolveEmployee();

        return view('Hr.staff.index', [
            'stats'          => $this->dashboardStats($employeeId),
            'charts'         => $this->dashboardCharts($employeeId),
            'leave'          => $this->leaveBalance($employeeId),
            'profile'        => $this->profileData($employeeId),
            'employeeLinked' => (bool) $employeeId,
        ]);
    }

    /**
     * Compute the signed-in staff member's dashboard figures.
     * Mirrors hr_portal's Api\Admin\UserDashboardController::getDashboardStats().
     */
    private function dashboardStats(?int $employeeId): array
    {
        // Total leave allocation from the configured (active) leave settings.
        $totalLeave = (int) HrLeaveSetting::where('status', 1)->whereNull('deleted_at')->sum('no_of_days');

        // Defaults (no linked employee → all zeros).
        $daysPresent    = 0;
        $leaveTaken     = 0;
        $hoursThisMonth = 0.0;
        $pending        = 0;

        $now         = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
               
        // Working days = weekdays from the 1st up to today (inclusive).
        $workingDays = 0;
        for ($day = $startOfMonth->copy(); $day <= $endOfMonth->copy(); $day->addDay()) {
            if ($day->isWeekday()) {
                $workingDays++;
            }
        }

        if ($employeeId) {
            $attendances = HrAttendance::where('employee_id', $employeeId)
                ->whereBetween('attendance_date', [$startOfMonth->format('Y-m-d'), $now->format('Y-m-d')])
                ->get();

            $daysPresent    = $attendances->where('status', 'present')->count();
            $hoursThisMonth = (float) $attendances->sum('hours');

            $leaveTaken = (int) HrLeaveRequest::where('employee_id', $employeeId)
                ->whereNull('deleted_at')
                ->where('status', 'approved')
                ->whereYear('from_date', $now->year)
                ->sum('days');

            $pending = (int) HrLeaveRequest::where('employee_id', $employeeId)
                ->whereNull('deleted_at')
                ->where('status', 'pending')
                ->count();
        }

        return [
            'employee_id'      => $employeeId,
            'days_present'     => $daysPresent,
            'working_days'     => $workingDays,
            'leave_taken'      => $leaveTaken,
            'total_leave'      => $totalLeave,
            'leave_balance'    => max(0, $totalLeave - $leaveTaken),
            'hours_this_month' => abs(round($hoursThisMonth)),
            'pending_requests' => $pending,
        ];
    }

    // ── Attendance ─────────────────────────────────────────────────

    /**
     * Today's status + monthly summary + calendar for the signed-in staff.
     * Ports UserDashboardController::getAttendance() + getCalendarAttendance().
     */
    public function attendanceOverview(Request $request)
    {
        $employeeId = $this->resolveEmployee();
        if (! $employeeId) {
            return response()->json(['success' => false, 'message' => 'Employee profile not linked to this account.'], 404);
        }

        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $today = Carbon::today();

        $attendances = HrAttendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->attendance_date)->format('Y-m-d'));

        // Working days = weekdays from the 1st up to today (or month end, whichever comes first).
        $compareDate = $end < $today ? $end : $today;
        $workingDays = 0;
        for ($d = $start->copy(); $d <= $compareDate; $d->addDay()) {
            if ($d->isWeekday()) {
                $workingDays++;
            }
        }

        $present   = $attendances->where('status', 'present')->count();
        $absent    = $attendances->where('status', 'absent')->count();
        $leave     = $attendances->where('status', 'on_leave')->count();
        $totalHours = abs(round($attendances->sum('hours')));

        // Approved leave dates that fall in this month (calendar fallback).
        $leaveDates = $this->approvedLeaveDates($employeeId, $start, $end);

        // Today's attendance card (always today, regardless of viewed month).
        $todayRec = HrAttendance::where('employee_id', $employeeId)
            ->where('attendance_date', $today->format('Y-m-d'))->first();
        $todayData = $todayRec ? [
            'check_in'  => $this->fmtTime($todayRec->check_in),
            'check_out' => $this->fmtTime($todayRec->check_out),
            'status'    => $todayRec->status,
            'hours'     => $this->fmtHours($todayRec->hours),
        ] : null;

        // Calendar grid.
        $days = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $key      = $d->format('Y-m-d');
            $isWeekend = $d->isWeekend();
            $isFuture  = $d->gt($today);

            $status = null;
            if (! $isFuture && ! $isWeekend) {
                if ($attendances->has($key)) {
                    $status = $attendances[$key]->status;
                } elseif (isset($leaveDates[$key])) {
                    $status = 'on_leave';
                } elseif ($d->lt($today)) {
                    $status = 'absent';
                }
            }

            $days[] = [
                'date'       => $key,
                'day'        => $d->day,
                'weekday'    => (int) $d->dayOfWeek, // 0=Sun … 6=Sat
                'is_weekend' => $isWeekend,
                'is_future'  => $isFuture,
                'is_today'   => $d->isSameDay($today),
                'status'     => $status,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'year'  => $year,
                'label' => $start->format('F Y'),
                'today' => $todayData,
                'today_date' => $today->format('l, j F Y'),
                'summary' => [
                    'working_days' => $workingDays,
                    'present'      => $present,
                    'absent'       => $absent,
                    'leave'        => $leave,
                    'total_hours'  => $totalHours,
                ],
                'days' => $days,
            ],
        ]);
    }

    /** Attendance history rows for a given month/year. */
    public function attendanceHistory(Request $request)
    {
        $employeeId = $this->resolveEmployee();
        if (! $employeeId) {
            return response()->json(['success' => false, 'message' => 'Employee profile not linked to this account.'], 404);
        }

        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $records = HrAttendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->orderByDesc('attendance_date')
            ->get()
            ->map(fn ($a) => [
                'date'      => Carbon::parse($a->attendance_date)->format('d-m-Y'),
                'check_in'  => $this->fmtTime($a->check_in),
                'check_out' => $this->fmtTime($a->check_out),
                'hours'     => $this->fmtHours($a->hours),
                'status'    => $a->status,
            ]);

        return response()->json(['success' => true, 'data' => ['records' => $records]]);
    }

    /** Check in for today. */
    public function checkIn()
    {
        $employeeId = $this->resolveEmployee();
        if (! $employeeId) {
            return response()->json(['success' => false, 'message' => 'Employee profile not linked to this account.'], 404);
        }

        $now   = Carbon::now();
        $today = $now->format('Y-m-d');

        $existing = HrAttendance::where('employee_id', $employeeId)->where('attendance_date', $today)->first();
        if ($existing && $existing->check_in) {
            return response()->json(['success' => false, 'message' => 'Already checked in for today.'], 422);
        }

        if ($existing) {
            $existing->update(['check_in' => $now->format('H:i:s'), 'status' => 'present']);
        } else {
            HrAttendance::create([
                'employee_id'     => $employeeId,
                'attendance_date' => $today,
                'check_in'        => $now->format('H:i:s'),
                'status'          => 'present',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Checked in successfully at ' . $now->format('h:i A')]);
    }

    /** Check out for today (computes hours). */
    public function checkOut()
    {
        $employeeId = $this->resolveEmployee();
        if (! $employeeId) {
            return response()->json(['success' => false, 'message' => 'Employee profile not linked to this account.'], 404);
        }

        $now   = Carbon::now();
        $today = $now->format('Y-m-d');

        $attendance = HrAttendance::where('employee_id', $employeeId)->where('attendance_date', $today)->first();
        if (! $attendance || ! $attendance->check_in) {
            return response()->json(['success' => false, 'message' => 'Please check in first.'], 422);
        }
        if ($attendance->check_out) {
            return response()->json(['success' => false, 'message' => 'Already checked out for today.'], 422);
        }

        $checkIn = Carbon::parse($today . ' ' . $attendance->check_in);
        $hours   = round($checkIn->diffInMinutes($now) / 60, 2);

        $attendance->update(['check_out' => $now->format('H:i:s'), 'hours' => $hours]);

        return response()->json(['success' => true, 'message' => 'Checked out successfully at ' . $now->format('h:i A')]);
    }

    // ── Leave Management ───────────────────────────────────────────

    /** Per-type leave balance + configured types for the signed-in staff. */
    private function leaveBalance(?int $employeeId): array
    {
        $year     = now()->year;
        $settings = HrLeaveSetting::where('status', 1)->whereNull('deleted_at')->get();

        $balance = [];
        $total   = ['allowed' => 0, 'taken' => 0, 'remaining' => 0];
        foreach ($settings as $setting) {
            $taken = $employeeId ? (int) HrLeaveRequest::where('employee_id', $employeeId)
                ->where('leave_type', $setting->leave_type)
                ->where('status', 'approved')
                ->whereYear('from_date', $year)
                ->sum('days') : 0;
            $allowed   = (int) $setting->no_of_days;
            $remaining = max(0, $allowed - $taken);

            $balance[] = [
                'leave_type' => $setting->leave_type,
                'allowed'    => $allowed,
                'taken'      => $taken,
                'remaining'  => $remaining,
            ];
            $total['allowed']   += $allowed;
            $total['taken']     += $taken;
            $total['remaining'] += $remaining;
        }

        return [
            'balance' => $balance,
            'total'   => $total,
            'types'   => $settings->pluck('leave_type')->values(),
        ];
    }

    /**
     * Build and return the table-row HTML for a leave status (Pending/Approved/Rejected tabs).
     * Reads the rows for the status and returns a ready-to-inject <tr>… string.
     */
    public function leaveRows(Request $request)
    {
        $status = (string) $request->query('status');
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return response()->json(['status' => false, 'html' => ''], 422);
        }

        $employeeId = $this->resolveEmployee();
        $rows = $employeeId
            ? HrLeaveRequest::where('employee_id', $employeeId)
                ->whereNull('deleted_at')
                ->where('status', $status)
                ->orderByDesc('from_date')
                ->get()
            : collect();

        $html = '';
        foreach ($rows as $r) {
            $action = $r->status === 'pending'
                ? '<button type="button" class="lv-btn link-danger lv-cancel" data-id="' . $r->id . '" data-type="' . e($r->leave_type) . '">Cancel</button>'
                : '<span style="color:var(--text-3)">—</span>';

            $html .= '<tr data-id="' . $r->id . '">'
                . '<td style="color:var(--text-1);font-weight:500">' . e($r->leave_type) . '</td>'
                . '<td>' . Carbon::parse($r->from_date)->format('d-m-Y') . '</td>'
                . '<td>' . Carbon::parse($r->to_date)->format('d-m-Y') . '</td>'
                . '<td>' . (int) $r->days . '</td>'
                . '<td>' . ($r->reason ? e($r->reason) : '—') . '</td>'
                . '<td><span class="lv-badge ' . $r->status . '">' . ucfirst($r->status) . '</span></td>'
                . '<td>' . $action . '</td>'
                . '</tr>';
        }

        if ($html === '') {
            $html = '<tr><td colspan="7" class="lv-nempty">No leave requests found</td></tr>';
        }

        return response()->json(['status' => true, 'html' => $html]);
    }

    /** Server-side DataTables feed for the signed-in staff's leave requests. */
    public function leaveData(Request $request)
    {
        $employeeId = $this->resolveEmployee();

        $query = HrLeaveRequest::query()
            ->where('employee_id', $employeeId ?: 0)
            ->whereNull('deleted_at')
            ->select('hr_leave_requests.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('leave_type', fn ($r) => e($r->leave_type))
            ->editColumn('from_date', fn ($r) => Carbon::parse($r->from_date)->format('d-m-Y'))
            ->editColumn('to_date', fn ($r) => Carbon::parse($r->to_date)->format('d-m-Y'))
            ->editColumn('reason', fn ($r) => $r->reason ? e($r->reason) : '—')
            ->addColumn('status_badge', fn ($r) => '<span class="lv-badge ' . $r->status . '">' . ucfirst($r->status) . '</span>')
            ->addColumn('action', fn ($r) => $r->status === 'pending'
                ? '<button type="button" class="lv-btn link-danger lv-cancel" data-id="' . $r->id
                    . '" data-type="' . e($r->leave_type) . '">Cancel</button>'
                : '<span style="color:var(--text-3)">—</span>')
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /** Submit a new leave request (form POST). Ports createLeaveRequest(). */
    public function leaveStore(Request $request)
    {
        $employeeId = $this->resolveEmployee();
        if (! $employeeId) {
            return redirect()->route('hr.staff', ['tab' => 'leave-management'])
                ->with('leave_error', 'Employee profile not linked to this account.');
        }

        $allowedTypes = HrLeaveSetting::where('status', 1)->whereNull('deleted_at')->pluck('leave_type')->all();

        $data = $request->validate([
            'leave_type' => ['required', Rule::in($allowedTypes)],
            'from_date'  => ['required', 'date'],
            'to_date'    => ['required', 'date', 'after_or_equal:from_date'],
            'reason'     => ['nullable', 'string', 'max:1000'],
        ]);

        $from = Carbon::parse($data['from_date']);
        $to   = Carbon::parse($data['to_date']);
        $days = $from->diffInDays($to) + 1;

        // Reject overlapping (non-rejected) requests.
        $overlap = HrLeaveRequest::where('employee_id', $employeeId)
            ->whereNull('deleted_at')
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($data) {
                $q->whereBetween('from_date', [$data['from_date'], $data['to_date']])
                  ->orWhereBetween('to_date', [$data['from_date'], $data['to_date']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('from_date', '<=', $data['from_date'])
                         ->where('to_date', '>=', $data['to_date']);
                  });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('leave_error', 'You already have a leave request for these dates.');
        }

        HrLeaveRequest::create([
            'employee_id' => $employeeId,
            'leave_type'  => $data['leave_type'],
            'from_date'   => $data['from_date'],
            'to_date'     => $data['to_date'],
            'days'        => $days,
            'reason'      => $data['reason'] ?? null,
            'status'      => 'pending',
        ]);

        return redirect()->route('hr.staff', ['tab' => 'leave-management'])
            ->with('leave_success', 'Leave request submitted successfully.');
    }

    /** Cancel a pending leave request (AJAX from the DataTable). Ports deleteLeaveRequest(). */
    public function leaveDestroy(int $id)
    {
        $employeeId = $this->resolveEmployee();
        if (! $employeeId) {
            return response()->json(['status' => false, 'msg' => 'Employee profile not linked to this account.'], 404);
        }

        $leave = HrLeaveRequest::where('id', $id)
            ->where('employee_id', $employeeId)
            ->whereNull('deleted_at')
            ->first();

        if (! $leave) {
            return response()->json(['status' => false, 'msg' => 'Leave request not found.'], 404);
        }
        if ($leave->status !== 'pending') {
            return response()->json(['status' => false, 'msg' => 'Only pending leave requests can be cancelled.'], 422);
        }

        $leave->delete();

        return response()->json(['status' => true, 'msg' => 'Leave request cancelled successfully.']);
    }

    /** Map of Y-m-d → true for approved-leave days overlapping the given range. */
    private function approvedLeaveDates(int $employeeId, Carbon $start, Carbon $end): array
    {
        $requests = HrLeaveRequest::where('employee_id', $employeeId)
            ->whereNull('deleted_at')
            ->where('status', 'approved')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('from_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                  ->orWhereBetween('to_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('from_date', '<=', $start->format('Y-m-d'))
                         ->where('to_date', '>=', $end->format('Y-m-d'));
                  });
            })
            ->get();

        $dates = [];
        foreach ($requests as $leave) {
            $d = Carbon::parse($leave->from_date);
            $to = Carbon::parse($leave->to_date);
            while ($d <= $to) {
                $dates[$d->format('Y-m-d')] = true;
                $d->addDay();
            }
        }

        return $dates;
    }

    /** Format a time column ('H:i:s') as 'h:i A', or null. */
    private function fmtTime($time): ?string
    {
        return $time ? Carbon::parse($time)->format('h:i A') : null;
    }

    /** Format decimal hours as "8h 30m", or null. */
    private function fmtHours($hours): ?string
    {
        if ($hours === null || (float) $hours == 0.0) {
            return null;
        }
        $h = abs((float) $hours);
        return (int) floor($h) . 'h ' . (int) round(($h - floor($h)) * 60) . 'm';
    }

    // ── Profile ────────────────────────────────────────────────────

    /** Signed-in staff + linked employee details for the Profile tab. Ports getProfile(). */
    private function profileData(?int $employeeId): array
    {
        $staff    = Auth::guard('staff')->user();
        $employee = $employeeId ? HrEmployee::find($employeeId) : null;

        $dept = $employee && $employee->department_id
            ? optional(HrDepartment::find($employee->department_id))->department_name : null;
        $desig = $employee && $employee->designation_id
            ? optional(HrDesignation::find($employee->designation_id))->designation_name : null;

        $name     = $employee->full_name ?? ($staff->name ?? 'User');
        $initials = collect(preg_split('/\s+/', trim($name)))->filter()
            ->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        $fmt    = fn ($d, $f = 'd M Y') => $d ? Carbon::parse($d)->format($f) : '—';
        $mobile = $employee->mobile_number ?? ($staff->mobile ?? null);
        $image  = $employee && $employee->profile_image
            ? config('constants.file_path') . $employee->profile_image : null;

        return [
            'name'            => $name,
            'initials'        => $initials ?: 'U',
            'role'            => $staff ? ucwords(str_replace('_', ' ', (string) $staff->role)) : '—',
            'avatar'          => $image,
            'employee_id'     => $employee->employee_id ?? '—',
            'department'      => $dept ?: '—',
            'designation'     => $desig ?: '—',
            'location'        => $employee->work_location ?: '—',
            'joined'          => $fmt($employee->join_date ?? null),
            'email'           => $employee->email ?: '—',
            'phone'           => $mobile ? '91 ' . $mobile : '—',
            'dob'             => $fmt($employee->date_of_birth ?? null, 'd F Y'),
            'gender'          => $employee->gender ?: '—',
            'marital_status'  => $employee->marital_status ?: '—',
            'date_of_joining' => $fmt($employee->join_date ?? null, 'd F Y'),
            'work_location'   => $employee->work_location ?: '—',
            'linked'          => (bool) $employee,
        ];
    }

    /** Change the signed-in staff member's 4-digit login PIN. */
    public function changePassword(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $pin     = (string) $request->input('new_pin');
        $confirm = (string) $request->input('new_pin_confirmation');

        if (! preg_match('/^\d{4}$/', $pin)) {
            return redirect()->route('hr.staff', ['tab' => 'profile'])
                ->with('profile_error', 'PIN must be exactly 4 digits.');
        }
        if ($pin !== $confirm) {
            return redirect()->route('hr.staff', ['tab' => 'profile'])
                ->with('profile_error', 'PIN confirmation does not match.');
        }

        $staff->pin = $pin;   // hashed automatically by the model cast
        $staff->save();

        return redirect()->route('hr.staff', ['tab' => 'profile'])
            ->with('profile_success', 'Password updated successfully.');
    }

    /** Initial dashboard chart data (current year yearly + current month hours). */
    private function dashboardCharts(?int $employeeId): array
    {
        $year   = now()->year;
        $yearly = $this->yearlyStats($employeeId, $year);
        $hours  = $this->monthlyHours($employeeId, $year, now()->month);

        return [
            'year'         => $year,
            'labels'       => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'attendance'   => $yearly['attendance'],
            'leave'        => $yearly['leave'],
            'month'        => now()->month,
            'month_label'  => $hours['label'],
            'hours_labels' => $hours['labels'],
            'hours'        => $hours['hours'],
            'years'        => range($year, $year - 5),   // year-selector options (newest first)
        ];
    }

    /** 12-month present-days + approved-leave-days arrays for a year. */
    private function yearlyStats(?int $employeeId, int $year): array
    {
        $attendance = array_fill(1, 12, 0);
        $leave      = array_fill(1, 12, 0);

        if ($employeeId) {
            $present = HrAttendance::where('employee_id', $employeeId)
                ->whereYear('attendance_date', $year)
                ->where('status', 'present')
                ->selectRaw('MONTH(attendance_date) as m, COUNT(*) as c')
                ->groupBy('m')->pluck('c', 'm');
            foreach ($present as $m => $c) {
                $attendance[(int) $m] = (int) $c;
            }

            $approved = HrLeaveRequest::where('employee_id', $employeeId)
                ->whereNull('deleted_at')
                ->where('status', 'approved')
                ->whereYear('from_date', $year)
                ->selectRaw('MONTH(from_date) as m, SUM(days) as s')
                ->groupBy('m')->pluck('s', 'm');
            foreach ($approved as $m => $s) {
                $leave[(int) $m] = (int) $s;
            }
        }

        return ['attendance' => array_values($attendance), 'leave' => array_values($leave)];
    }

    /** Per-day working hours for a given month. */
    private function monthlyHours(?int $employeeId, int $year, int $month): array
    {
        $start       = Carbon::create($year, $month, 1);
        $daysInMonth = $start->daysInMonth;
        $hours       = array_fill(1, $daysInMonth, 0);

        if ($employeeId) {
            $daily = HrAttendance::where('employee_id', $employeeId)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month)
                ->selectRaw('DAY(attendance_date) as d, SUM(hours) as s')
                ->groupBy('d')->pluck('s', 'd');
            foreach ($daily as $d => $s) {
                $hours[(int) $d] = round((float) $s, 1);
            }
        }

        return [
            'label'  => $start->format('F Y'),
            'labels' => range(1, $daysInMonth),
            'hours'  => array_values($hours),
        ];
    }

    /** AJAX: yearly attendance + approved-leave for the year picker. */
    public function dashboardYearly(Request $request)
    {
        $employeeId = $this->resolveEmployee();
        $year       = (int) $request->query('year', now()->year);

        return response()->json(['success' => true, 'data' => $this->yearlyStats($employeeId, $year) + ['year' => $year]]);
    }

    /** AJAX: per-day working hours for the month navigator. */
    public function dashboardHours(Request $request)
    {
        $employeeId = $this->resolveEmployee();
        $year       = (int) $request->query('year', now()->year);
        $month      = (int) $request->query('month', now()->month);
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        return response()->json(['success' => true, 'data' => $this->monthlyHours($employeeId, $year, $month) + ['month' => $month, 'year' => $year]]);
    }

    /** The signed-in staff member's linked HR employee (via hr_employees.staff_id). */
    private function resolveEmployee()
        {
        $staff_id=Auth::guard('staff')->user()?->id;
        $employee_id=HrEmployee::where('staff_id',$staff_id)->pluck('id')->first();
        return $employee_id;
    }
}
