<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrAttendance;
use App\Models\HrEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

/**
 * HR › Attendance.
 *
 * Attendance management — server-side (Yajra) DataTable, mark/edit an
 * employee's attendance, delete, and CSV export. Adapted from hr_portal's
 * Admin attendance module into the getlead_hq design system.
 */
class AttendanceController extends Controller
{
    /** Statuses used across the filter + mark modal. */
    private const STATUSES = ['present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day', 'on_leave' => 'On Leave'];

    public function index()
    {
        $employees = HrEmployee::orderBy('full_name')->get(['id', 'full_name', 'employee_id']);
        $statuses  = self::STATUSES;

        return view('Hr.attendance.index', compact('employees', 'statuses'));
    }

    /** Yajra DataTables JSON feed. */
    public function data(Request $request)
    {
        $query = $this->filtered($request);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employee', function ($r) {
                $ini = strtoupper(mb_substr($r->full_name ?? 'E', 0, 1));
                return '<div class="emp-cell"><div class="emp-avatar-fb">' . $ini . '</div>'
                    . '<div><div class="nm">' . e($r->full_name ?: '—') . '</div>'
                    . '<div class="em">' . e($r->employee_code ?: '') . '</div></div></div>';
            })
            ->addColumn('date_fmt', fn ($r) => $r->attendance_date ? Carbon::parse($r->attendance_date)->format('d-m-Y') : '—')
            ->addColumn('check_in_fmt', fn ($r) => $r->check_in ? Carbon::parse($r->check_in)->format('h:i A') : '--:--')
            ->addColumn('check_out_fmt', fn ($r) => $r->check_out ? Carbon::parse($r->check_out)->format('h:i A') : '--:--')
            ->addColumn('hours_fmt', fn ($r) => $r->hours !== null ? (float) $r->hours . 'h' : '--')
            ->addColumn('notes', fn ($r) => $r->remarks ? e($r->remarks) : '<span class="text-muted">—</span>')
            ->addColumn('status_badge', function ($r) {
                $label = self::STATUSES[$r->status] ?? ucfirst((string) $r->status);
                $cls   = 'st-' . \Illuminate\Support\Str::slug($r->status ?: 'other');
                return '<span class="att-badge ' . $cls . '">' . e($label) . '</span>';
            })
            ->addColumn('action', function ($r) {
                return '<div class="row-acts">'
                    . '<button type="button" class="ico-btn edit-att" data-id="' . $r->id . '" title="Edit">✎</button>'
                    . '<button type="button" class="ico-btn del-att" data-id="' . $r->id . '" title="Delete">🗑</button>'
                    . '</div>';
            })
            ->rawColumns(['employee', 'notes', 'status_badge', 'action'])
            ->make(true);
    }

    /** Create or update an attendance record (Mark Attendance modal). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id'              => ['nullable', 'integer'],
            'employee_id'     => ['required', 'integer', 'exists:hr_employees,id'],
            'attendance_date' => ['required', 'date'],
            'status'          => ['required', 'in:' . implode(',', array_keys(self::STATUSES))],
            'check_in'        => ['nullable', 'string', 'max:20'],
            'check_out'       => ['nullable', 'string', 'max:20'],
            'remarks'         => ['nullable', 'string', 'max:1000'],
        ]);

        // Prevent duplicate attendance for the same employee + date.
        $dup = HrAttendance::where('employee_id', $data['employee_id'])
            ->whereDate('attendance_date', $data['attendance_date'])
            ->when(! empty($data['id']), fn ($q) => $q->where('id', '!=', $data['id']))
            ->exists();
        if ($dup) {
            return response()->json(['ok' => false, 'message' => 'Attendance already marked for this employee on this date.'], 422);
        }

        $checkIn  = $this->toTime($data['check_in'] ?? null);
        $checkOut = $this->toTime($data['check_out'] ?? null);
        $hours    = null;
        if ($checkIn && $checkOut) {
            $hours = abs(round(Carbon::parse($checkOut)->diffInMinutes(Carbon::parse($checkIn)) / 60, 2));
        }

        $fields = [
            'employee_id'     => $data['employee_id'],
            'attendance_date' => $data['attendance_date'],
            'status'          => $data['status'],
            'check_in'        => $checkIn,
            'check_out'       => $checkOut,
            'hours'           => $hours,
            'remarks'         => $data['remarks'] ?? null,
        ];

        if (! empty($data['id'])) {
            HrAttendance::where('id', $data['id'])->update($fields);
            $msg = 'Attendance updated successfully.';
        } else {
            HrAttendance::create($fields);
            $msg = 'Attendance marked successfully.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    /** Employees split into not-marked / marked for a given date (modal dropdown). */
    public function employeesByDate(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $markedIds = HrAttendance::whereDate('attendance_date', $date)->pluck('employee_id')->flip();

        $notMarked = [];
        $marked    = [];
        foreach (HrEmployee::orderBy('full_name')->get(['id', 'full_name']) as $e) {
            $item = ['id' => $e->id, 'name' => $e->full_name];
            $markedIds->has($e->id) ? $marked[] = $item : $notMarked[] = $item;
        }

        return response()->json(['ok' => true, 'not_marked' => $notMarked, 'marked' => $marked]);
    }

    /** Single record (JSON) to prefill the edit modal. */
    public function edit(int $id)
    {
        $r = HrAttendance::find($id);
        if (! $r) {
            return response()->json(['ok' => false, 'message' => 'Not found.'], 404);
        }

        return response()->json([
            'ok'  => true,
            'row' => [
                'id'              => $r->id,
                'employee_id'     => $r->employee_id,
                'attendance_date' => $r->attendance_date ? Carbon::parse($r->attendance_date)->format('Y-m-d') : '',
                'status'          => $r->status,
                'check_in'        => $r->check_in ? Carbon::parse($r->check_in)->format('h:i A') : '',
                'check_out'       => $r->check_out ? Carbon::parse($r->check_out)->format('h:i A') : '',
                'remarks'         => $r->remarks,
            ],
        ]);
    }

    /** Delete an attendance record. */
    public function destroy(int $id)
    {
        $r = HrAttendance::find($id);
        if (! $r) {
            return response()->json(['ok' => false, 'message' => 'Not found.'], 404);
        }
        $r->delete();

        return response()->json(['ok' => true, 'message' => 'Attendance deleted.']);
    }

    /** CSV export of the filtered records. */
    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Employee', 'Employee ID', 'Date', 'Check In', 'Check Out', 'Hours', 'Status', 'Notes']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->full_name,
                    $r->employee_code,
                    $r->attendance_date ? Carbon::parse($r->attendance_date)->format('d-m-Y') : '',
                    $r->check_in ? Carbon::parse($r->check_in)->format('h:i A') : '',
                    $r->check_out ? Carbon::parse($r->check_out)->format('h:i A') : '',
                    $r->hours,
                    self::STATUSES[$r->status] ?? $r->status,
                    $r->remarks,
                ]);
            }
            fclose($out);
        }, 'attendance_' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Base filtered query shared by data() + export(). */
    private function filtered(Request $request)
    {
        return HrAttendance::query()
            ->from('hr_attendances')
            ->leftJoin('hr_employees', 'hr_attendances.employee_id', '=', 'hr_employees.id')
            ->select(
                'hr_attendances.*',
                'hr_employees.full_name',
                'hr_employees.employee_id as employee_code'
            )
            ->when($request->filled('employee_id'), fn ($q) => $q->where('hr_attendances.employee_id', $request->employee_id))
            ->when($request->filled('status'), fn ($q) => $q->where('hr_attendances.status', $request->status))
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('hr_attendances.attendance_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('hr_attendances.attendance_date', '<=', $request->end_date))
            ->orderByDesc('hr_attendances.attendance_date')
            ->orderByDesc('hr_attendances.id');
    }

    /** Parse a "h:i A" (e.g. "09:30 AM") time string to "H:i:s", or null. */
    private function toTime(?string $t): ?string
    {
        $t = trim((string) $t);
        if ($t === '') {
            return null;
        }
        try {
            return Carbon::parse($t)->format('H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
