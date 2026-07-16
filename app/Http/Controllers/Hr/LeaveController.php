<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * HR › Leave Requests.
 *
 * Server-side (Yajra) DataTable of leave requests with status tabs, add,
 * approve / reject, and delete. Adapted from hr_portal's admin leave module.
 */
class LeaveController extends Controller
{
    public function index()
    {
        $employees   = HrEmployee::orderBy('full_name')->get(['id', 'full_name']);
        $departments = DB::table('hr_departments')->whereNull('deleted_at')->orderBy('department_name')->get();
        $leaveTypes  = DB::table('hr_leave_settings')->whereNull('deleted_at')->where('status', 1)->orderBy('id')->pluck('leave_type');
        $counts      = $this->statusCounts();

        return view('Hr.leave-requests.index', compact('employees', 'departments', 'leaveTypes', 'counts'));
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
                    . '<div class="em">' . e($r->department_name ?: '') . '</div></div></div>';
            })
            ->addColumn('leave_type_txt', fn ($r) => e($r->leave_type ?: '—'))
            ->addColumn('from_fmt', fn ($r) => $r->from_date ? Carbon::parse($r->from_date)->format('d/m/Y') : '—')
            ->addColumn('to_fmt', fn ($r) => $r->to_date ? Carbon::parse($r->to_date)->format('d/m/Y') : '—')
            ->addColumn('days_txt', fn ($r) => $r->days ?? '—')
            ->addColumn('reason_txt', fn ($r) => $r->reason ? e($r->reason) : '<span class="text-muted">—</span>')
            ->addColumn('status_badge', function ($r) {
                $cls = 'st-' . \Illuminate\Support\Str::slug($r->status ?: 'pending');
                return '<span class="att-badge ' . $cls . '">' . ucfirst((string) $r->status) . '</span>';
            })
            ->addColumn('action', function ($r) {
                $a = '<div class="row-acts">';
                if ($r->status === 'pending') {
                    $a .= '<button type="button" class="ico-btn app-lv" data-id="' . $r->id . '" title="Approve" style="color:var(--success)">✔</button>';
                    $a .= '<button type="button" class="ico-btn rej-lv" data-id="' . $r->id . '" title="Reject" style="color:var(--danger)">✖</button>';
                }
                $a .= '<button type="button" class="ico-btn del-lv" data-id="' . $r->id . '" title="Delete">🗑</button>';
                $a .= '</div>';
                return $a;
            })
            ->rawColumns(['employee', 'reason_txt', 'status_badge', 'action'])
            ->make(true);
    }

    /** Status tab counts (JSON). */
    public function counts()
    {
        return response()->json($this->statusCounts());
    }

    /** Create a leave request (pending). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:hr_employees,id'],
            'leave_type'  => ['required', 'string', 'max:100'],
            'from_date'   => ['required', 'date'],
            'to_date'     => ['required', 'date', 'after_or_equal:from_date'],
            'days'        => ['required', 'numeric', 'min:0.5'],
            'reason'      => ['nullable', 'string', 'max:1000'],
        ]);

        HrLeaveRequest::create([
            'employee_id' => $data['employee_id'],
            'leave_type'  => $data['leave_type'],
            'from_date'   => $data['from_date'],
            'to_date'     => $data['to_date'],
            'days'        => $data['days'],
            'reason'      => $data['reason'] ?? null,
            'status'      => 'pending',
        ]);

        return response()->json(['ok' => true, 'message' => 'Leave request added.']);
    }

    /** Approve / reject a leave request. */
    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected,pending']]);

        $lr = HrLeaveRequest::find($id);
        if (! $lr) {
            return response()->json(['ok' => false, 'message' => 'Leave request not found.'], 404);
        }

        $lr->status = $data['status'];
        if (in_array($data['status'], ['approved', 'rejected'], true)) {
            $lr->approved_by = auth('staff')->id();
            $lr->approved_at = now();
        } else {
            $lr->approved_by = null;
            $lr->approved_at = null;
        }
        $lr->save();

        return response()->json(['ok' => true, 'message' => 'Leave request ' . $data['status'] . '.']);
    }

    /** Delete a leave request (soft delete). */
    public function destroy(int $id)
    {
        $lr = HrLeaveRequest::find($id);
        if (! $lr) {
            return response()->json(['ok' => false, 'message' => 'Leave request not found.'], 404);
        }
        $lr->delete();

        return response()->json(['ok' => true, 'message' => 'Leave request deleted.']);
    }

    // ── Helpers ────────────────────────────────────────────────────

    private function filtered(Request $request)
    {
        return HrLeaveRequest::query()
            ->from('hr_leave_requests')
            ->leftJoin('hr_employees', 'hr_leave_requests.employee_id', '=', 'hr_employees.id')
            ->leftJoin('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->whereNull('hr_leave_requests.deleted_at')
            ->select(
                'hr_leave_requests.*',
                'hr_employees.full_name',
                'hr_employees.department_id',
                'hr_departments.department_name'
            )
            ->when($request->filled('status'), fn ($q) => $q->where('hr_leave_requests.status', $request->status))
            ->when($request->filled('department_id'), fn ($q) => $q->where('hr_employees.department_id', $request->department_id))
            ->when($request->filled('leave_type'), fn ($q) => $q->where('hr_leave_requests.leave_type', $request->leave_type))
            ->orderByDesc('hr_leave_requests.id');
    }

    private function statusCounts(): array
    {
        $raw = HrLeaveRequest::whereNull('deleted_at')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        return [
            'all'      => (int) $raw->sum(),
            'pending'  => (int) ($raw['pending'] ?? 0),
            'approved' => (int) ($raw['approved'] ?? 0),
            'rejected' => (int) ($raw['rejected'] ?? 0),
        ];
    }
}
