<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use App\Models\HrEmployeeDocument;
use App\Models\HrEmployeeSalary;
use App\Models\HrPayroll;
use App\Models\HrLeaveSetting;
use App\Models\HrLeaveRequest;
use App\Models\HrAttendance;
use App\Models\HrQualification;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\Storage;

/**
 * HR › Employees.
 *
 * Employee directory with a List (server-side Yajra DataTable) / Card view
 * toggle. Department / designation / qualification names are joined from their
 * lookup tables (hr_departments, hr_designations, hr_qualifications).
 */
class EmployeeController extends Controller
{
    /** List page — the table itself is loaded from the data() endpoint. */
    public function index()
    {
        $departments  = HrDepartment::whereNull('deleted_at')->orderBy('department_name')->get();
        $designations = HrDesignation::whereNull('deleted_at')->orderBy('designation_name')->get();

        $counts = [
            'total'    => HrEmployee::count(),
            'active'   => HrEmployee::where('status', HrEmployee::ACTIVE)->count(),
            'inactive' => HrEmployee::where('status', HrEmployee::INACTIVE)->count(),
        ];

        return view('Hr.employees.index', compact('departments', 'designations', 'counts'));
    }



    /** Yajra DataTables JSON feed (also carries raw fields for the card view). */
    public function data(Request $request)
    {
        $query = HrEmployee::query()
            ->leftJoin('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->leftJoin('hr_designations', 'hr_employees.designation_id', '=', 'hr_designations.id')
            ->leftJoin('hr_qualifications', 'hr_employees.qualification_id', '=', 'hr_qualifications.id')
            ->select(
                'hr_employees.*',
                'hr_departments.department_name',
                'hr_designations.designation_name',
                'hr_qualifications.qualification as qualification_name'
            )
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('hr_employees.created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('hr_employees.created_at', '<=', $request->end_date))
            ->when($request->filled('department_id'), fn ($q) => $q->where('hr_employees.department_id', $request->department_id))
            ->when($request->filled('designation_id'), fn ($q) => $q->where('hr_employees.designation_id', $request->designation_id))
            ->when($request->filled('status') && $request->status !== '', fn ($q) => $q->where('hr_employees.status', (int) $request->status))
            ->orderByDesc('hr_employees.id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employee', function ($r) {
                $img = $this->photoUrl($r->profile_image);
                $initial = strtoupper(mb_substr($r->full_name ?? 'E', 0, 1));
                $avatar = $img
                    ? '<img src="' . e($img) . '" class="emp-avatar" onerror="this.outerHTML=\'<div class=&quot;emp-avatar-fb&quot;>' . $initial . '</div>\'">'
                    : '<div class="emp-avatar-fb">' . $initial . '</div>';
                return '<div class="emp-cell">' . $avatar . '<div>
                <div class="nm"><a href="'.route('hr.employees.show',$r->id).'" class="alink">' . e($r->full_name) . '</a></div><div class="em">' . e($r->email ?: '—') . '</div></div></div>';
            })
            ->addColumn('photo_url', fn ($r) => $this->photoUrl($r->profile_image))
            ->editColumn('employee_id', fn ($r) => $r->employee_id ?: '—')
            ->editColumn('mobile_number', fn ($r) => $r->mobile_number ?: '—')
            ->editColumn('job_title', fn ($r) => $r->job_title ?: '—')
            ->addColumn('department_name', fn ($r) => $r->department_name ?: '—')
            ->addColumn('designation_name', fn ($r) => $r->designation_name ?: '—')
            ->addColumn('join_date_fmt', fn ($r) => $r->join_date ? Carbon::parse($r->join_date)->format('d-m-Y') : '—')
            ->addColumn('status_badge', function ($r) {
                return $r->status
                    ? '<span class="badge b-active">Active</span>'
                    : '<span class="badge b-inactive">Inactive</span>';
            })
            ->rawColumns(['employee', 'status_badge'])
            ->make(true);
    }

    /** Show the "add employee" form. */
    public function create()
    {
        return view('Hr.employees.create', $this->formLookups());
    }

    /** Validate and store a new employee (+ file uploads) into hr_employees. */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        // Uploads → public/uploads/user_files (store filename only; photoUrl() prefixes the dir).
        foreach ($this->fileFields() as $field) {
            $data[$field] = $request->hasFile($field)
                ? $this->storeFile($request->file($field), $data['full_name'] . '_' . $field)
                : null;
        }

        $data['status'] = HrEmployee::ACTIVE;

        HrEmployee::create($data);

        return redirect()->route('hr.employees')->with('success', 'Employee added successfully.');
    }

    /** Show the "edit employee" form pre-filled with the record. */
    public function edit(int $id)
    {
        $emp = HrEmployee::find($id);
        if (! $emp) {
            return redirect()->route('hr.employees')->with('error', 'Employee not found.');
        }

        return view('Hr.employees.edit', array_merge(['emp' => $emp], $this->formLookups()));
    }

    /** Validate and update an employee (replacing files only when re-uploaded). */
    public function update(Request $request, int $id)
    {
        $emp = HrEmployee::find($id);
        if (! $emp) {
            return redirect()->route('hr.employees')->with('error', 'Employee not found.');
        }

        $data = $request->validate($this->rules($id));

        // Only overwrite a file column when a new file is actually uploaded —
        // and delete the previous file from storage so it isn't orphaned.
        foreach ($this->fileFields() as $field) {
            if ($request->hasFile($field)) {
                $this->deleteFile($emp->{$field});
                $data[$field] = $this->storeFile($request->file($field), $data['full_name'] . '_' . $field);
            }
        }

        $emp->update($data);

        return redirect()->route('hr.employees.show', $emp->id)->with('success', 'Employee updated successfully.');
    }

    /** Inline (modal) update of salary / HRA / TA — returns the recomputed breakdown. */
    public function updateSalary(Request $request, int $id)
    {
        $emp = HrEmployee::find($id);
        if (! $emp) {
            return response()->json(['ok' => false, 'message' => 'Employee not found.'], 404);
        }

        $data = $request->validate([
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hra'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ta'     => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $emp->update([
            'salary' => $request->filled('salary') ? $data['salary'] : null,
            'hra'    => $request->filled('hra') ? $data['hra'] : null,
            'ta'     => $request->filled('ta') ? $data['ta'] : null,
        ]);

        $base   = (float) ($emp->salary ?? 0);
        $hraPct = (float) ($emp->hra ?? 0);
        $taPct  = (float) ($emp->ta ?? 0);
        $hraAmt = $hraPct > 0 ? $base * $hraPct / 100 : null;
        $taAmt  = $taPct > 0 ? $base * $taPct / 100 : null;
        $gross  = $base + ($hraAmt ?? 0) + ($taAmt ?? 0);

        $fmt = fn ($v) => '$' . number_format((float) $v, 2);
        $pct = fn ($v) => rtrim(rtrim(number_format($v, 2), '0'), '.');

        return response()->json([
            'ok'         => true,
            'base'       => $base ?: '',
            'hra_pct'    => $hraPct ?: '',
            'ta_pct'     => $taPct ?: '',
            'base_fmt'   => $base > 0 ? $fmt($base) : 'N/A',
            'hra_label'  => 'HRA (' . $pct($hraPct) . '%)',
            'hra_fmt'    => $hraAmt !== null ? $fmt($hraAmt) : 'N/A',
            'ta_label'   => 'Transport Allowance (' . $pct($taPct) . '%)',
            'ta_fmt'     => $taAmt !== null ? $fmt($taAmt) : 'N/A',
            'gross_fmt'  => $fmt($gross),
            'monthly_fmt'=> $gross > 0 ? $fmt($gross) : 'N/A',
        ]);
    }

    /** Inline (modal) update of the employee's bank details. */
    public function updateBank(Request $request, int $id)
    {
        $emp = HrEmployee::find($id);
        if (! $emp) {
            return response()->json(['ok' => false, 'message' => 'Employee not found.'], 404);
        }

        $data = $request->validate([
            'bank_name'      => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code'      => ['nullable', 'string', 'max:20'],
        ]);

        $emp->update([
            'bank_name'      => $data['bank_name'] ?: null,
            'account_number' => $data['account_number'] ?: null,
            'ifsc_code'      => $data['ifsc_code'] ?: null,
        ]);

        return response()->json([
            'ok'          => true,
            'name'        => $emp->bank_name ?: 'N/A',
            'name_raw'    => $emp->bank_name ?: '',
            'account'     => $emp->account_number ?: 'N/A',
            'account_raw' => $emp->account_number ?: '',
            'ifsc'        => $emp->ifsc_code ?: 'N/A',
            'ifsc_raw'    => $emp->ifsc_code ?: '',
        ]);
    }

    /** File-upload fields on the employee form. */
    private function fileFields(): array
    {
        return ['profile_image', 'aadhar_file', 'pancard_file', 'experience_certificate', 'other_document'];
    }

    /** Validation rules shared by store()/update(). Pass the id to ignore on update. */
    private function rules(?int $ignoreId = null): array
    {
        return [
            // Personal
            'full_name'                => ['required', 'string', 'max:255'],
            'employee_id'              => ['required', 'string', 'max:100', Rule::unique('hr_employees', 'employee_id')->ignore($ignoreId)],
            'date_of_birth'            => ['nullable', 'date'],
            'gender'                   => ['nullable', 'in:Male,Female,Other'],
            'marital_status'           => ['nullable', 'in:Single,Married,Divorced,Widowed'],
            'qualification_id'         => ['nullable', 'integer', 'exists:hr_qualifications,id'],
            'technology_stack'         => ['nullable', 'string', 'max:255'],
            'join_date'                => ['nullable', 'date'],
            'releaving_date'           => ['nullable', 'date'],
            // Contact
            'mobile_number'            => ['required', 'string', 'max:15'],
            'alternative_number_1'     => ['nullable', 'string', 'max:15'],
            'alternative_number_2'     => ['nullable', 'string', 'max:15'],
            'email'                    => ['nullable', 'email', 'max:255'],
            'address'                  => ['nullable', 'string', 'max:500'],
            'city'                     => ['nullable', 'string', 'max:100'],
            'state'                    => ['nullable', 'string', 'max:100'],
            'country'                  => ['nullable', 'string', 'max:100'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:255'],
            'emergency_contact_number' => ['nullable', 'string', 'max:15'],
            'relationship'             => ['nullable', 'string', 'max:100'],
            // Employment
            'job_title'                => ['nullable', 'string', 'max:255'],
            'department_id'            => ['nullable', 'integer', 'exists:hr_departments,id'],
            'designation_id'           => ['nullable', 'integer', 'exists:hr_designations,id'],
            'date_of_hire'             => ['nullable', 'date'],
            'work_location'            => ['nullable', 'string', 'max:255'],
            'salary'                   => ['nullable', 'numeric', 'min:0'],
            'hra'                      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ta'                       => ['nullable', 'numeric', 'min:0', 'max:100'],
            // Documents & bank
            'aadhar_number'            => ['nullable', 'string', 'max:50'],
            'pancard_number'           => ['nullable', 'string', 'max:50'],
            'bank_name'                => ['nullable', 'string', 'max:255'],
            'account_number'           => ['nullable', 'string', 'max:50'],
            'ifsc_code'                => ['nullable', 'string', 'max:20'],
            'profile_image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'aadhar_file'              => ['nullable', 'mimes:pdf,jpeg,png,jpg', 'max:4096'],
            'pancard_file'             => ['nullable', 'mimes:pdf,jpeg,png,jpg', 'max:4096'],
            'experience_certificate'   => ['nullable', 'mimes:pdf,jpeg,png,jpg,doc,docx', 'max:4096'],
            'other_document'           => ['nullable', 'mimes:pdf,jpeg,png,jpg,doc,docx', 'max:4096'],
        ];
    }

    /** Full-page employee details. */
    public function show(int $id)
    {
        $emp = HrEmployee::query()
            ->leftJoin('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->leftJoin('hr_designations', 'hr_employees.designation_id', '=', 'hr_designations.id')
            ->leftJoin('hr_qualifications', 'hr_employees.qualification_id', '=', 'hr_qualifications.id')
            ->select(
                'hr_employees.*',
                'hr_departments.department_name',
                'hr_designations.designation_name',
                'hr_qualifications.qualification as qualification_name'
            )
            ->where('hr_employees.id', $id)
            ->first();

        if (! $emp) {
            return redirect()->route('hr.employees')->with('error', 'Employee not found.');
        }

        $emp->profile_url = $emp->profile_image ? $this->photoUrl($emp->profile_image) : '';

        // Uploaded documents (skip the profile photo — that's the avatar).
        $documents = collect([
            'Aadhar Card'            => $emp->aadhar_file,
            'PAN Card'               => $emp->pancard_file,
            'Experience Certificate' => $emp->experience_certificate,
            'Other Document'         => $emp->other_document,
        ])->filter()->map(fn ($f, $label) => [
            'label' => $label,
            'url'   => $this->photoUrl($f),
            'name'  => $f,
        ])->values();

        // Current-month attendance for the header stat cards.
        $cur = $this->attendanceSummary($id, now()->startOfMonth(), now()->endOfMonth());
        $emp->att_rate        = $cur['working_days'] > 0 ? (int) round($cur['present'] / $cur['working_days'] * 100) : 0;
        $emp->att_hours_month = $cur['total_hours'];

        // Month options for the Attendance tab (last 12 months).
        $months = collect(range(0, 11))->map(function ($i) {
            $m = now()->startOfMonth()->subMonths($i);
            return ['value' => $m->format('Y-m'), 'label' => $m->format('F Y')];
        });

        // Leave History tab data.
        $leave = $this->leaveSummary($id);

        // Payroll tab data.
        $payroll = $this->payrollSummary($emp);

        // Documents tab — uploaded employee documents.
        $empDocuments = HrEmployeeDocument::where('employee_id', $id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($d) => [
                'id'       => $d->id,
                'name'     => $d->document_name ?: $d->file_name,
                'url'      => $this->photoUrl($d->file_path ?: $d->file_name),
                'size'     => $this->formatBytes($d->file_size),
                'uploaded' => $d->created_at ? Carbon::parse($d->created_at)->format('M j, Y') : '—',
            ]);

        return view('Hr.employees.details', compact('emp', 'documents', 'months', 'leave', 'payroll', 'empDocuments'));
    }

    /** Upload a new document for an employee (Documents tab). */
    public function uploadDocument(Request $request, int $id)
    {
        $emp = HrEmployee::find($id);
        if (! $emp) {
            return response()->json(['ok' => false, 'message' => 'Employee not found.'], 404);
        }

        $data = $request->validate([
            'document_name' => ['required', 'string', 'max:255'],
            'document_file' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('document_file');

        // Capture metadata BEFORE moving the file — once moved, the temp file is
        // gone and getSize()/getClientMimeType() would stat a missing path.
        $originalName = $file->getClientOriginalName();
        $mimeType     = $file->getClientMimeType();
        $size         = $file->getSize();

        $stored = $this->storeFile($file, $emp->full_name . '_doc');

        $docId = HrEmployeeDocument::insertGetId([
            'employee_id'   => $id,
            'document_name' => $data['document_name'],
            'file_name'     => $originalName,
            'file_path'     => $stored,
            'file_type'     => $mimeType,
            'file_size'     => $size,
            'uploaded_by'   => auth('staff')->id(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json([
            'ok'  => true,
            'doc' => [
                'id'       => $docId,
                'name'     => $data['document_name'],
                'url'      => $this->photoUrl($stored),
                'size'     => $this->formatBytes($size),
                'uploaded' => now()->format('M j, Y'),
            ],
        ]);
    }

    /** Delete an uploaded employee document (soft delete). */
    public function deleteDocument(int $id, int $doc)
    {
        $row = HrEmployeeDocument::where('id', $doc)
            ->where('employee_id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $row) {
            return response()->json(['ok' => false, 'message' => 'Document not found.'], 404);
        }

        HrEmployeeDocument::where('id', $doc)->update(['deleted_at' => now()]);

        // Remove the underlying file from storage.
        $this->deleteFile($row->file_path);

        return response()->json(['ok' => true, 'message' => 'Document deleted.']);
    }

    /** Human-readable file size. */
    private function formatBytes($bytes): string
    {
        $bytes = (int) $bytes;
        if ($bytes <= 0) {
            return '—';
        }
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024) . ' KB';
        }
        return round($bytes / 1048576, 1) . ' MB';
    }

    /** Salary breakdown, bank details and payroll history for an employee. */
    private function payrollSummary($emp): array
    {
        // Prefer the latest salary-structure record; fall back to the employee's salary.
        $struct = HrEmployeeSalary::where('employee_id', $emp->id)
            ->whereNull('deleted_at')
            ->orderByDesc('effective_from')
            ->first();

        $base   = (float) ($struct->base_salary ?? $emp->salary ?? 0);
        $hraPct = (float) ($struct->hra_percentage ?? $emp->hra ?? 0);
        $taPct  = (float) ($struct->ta_percentage ?? $emp->ta ?? 0);
        $hraAmt = $hraPct > 0 ? $base * $hraPct / 100 : null;
        $taAmt  = $taPct > 0 ? $base * $taPct / 100 : null;
        $gross  = $base + ($hraAmt ?? 0) + ($taAmt ?? 0);

        $history = HrPayroll::where('employee_id', $emp->id)
            ->whereNull('deleted_at')
            ->orderByDesc('month')
            ->get()
            ->map(function ($p) {
                try {
                    $month = Carbon::parse($p->month)->format('F Y');
                } catch (\Throwable) {
                    $month = (string) $p->month;
                }
                return [
                    'month'        => $month,
                    'working_days' => $p->working_days,
                    'present'      => $p->present_days,
                    'gross'        => (float) $p->base_salary,
                    'deduction'    => (float) $p->deduction,
                    'net'          => (float) $p->net_salary,
                    'status'       => $p->status,
                ];
            });

        return [
            'base'    => $base,
            'hra_pct' => $hraPct,
            'hra_amt' => $hraAmt,
            'ta_pct'  => $taPct,
            'ta_amt'  => $taAmt,
            'gross'   => $gross,
            'bank'    => [
                'name'    => $emp->bank_name,
                'account' => $emp->account_number,
                'ifsc'    => $emp->ifsc_code,
                'type'    => 'Checking',
            ],
            'history' => $history,
        ];
    }

    /** Leave balances, yearly statistics and request history for an employee. */
    private function leaveSummary(int $employeeId): array
    {
        $year = now()->year;

        $settings = HrLeaveSetting::whereNull('deleted_at')
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        // Days taken per type — approved requests in the current year.
        $takenByType = HrLeaveRequest::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereYear('from_date', $year)
            ->selectRaw('leave_type, SUM(days) as d')
            ->groupBy('leave_type')
            ->pluck('d', 'leave_type');

        $balances = $settings->map(function ($s) use ($takenByType) {
            $used = (float) ($takenByType[$s->leave_type] ?? 0);
            $total = (int) $s->no_of_days;
            return [
                'type'      => $s->leave_type,
                'total'     => $total,
                'used'      => $used,
                'remaining' => max(0, $total - $used),
            ];
        });

        $allocated = (int) $settings->sum('no_of_days');
        $taken     = (float) $takenByType->sum();
        $pending   = HrLeaveRequest::where('employee_id', $employeeId)->where('status', 'pending')->count();
        $mostUsed  = $takenByType->sortDesc()->keys()->first();

        $history = HrLeaveRequest::where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($r) => [
                'leave_type' => $r->leave_type,
                'from_date'  => $r->from_date ? Carbon::parse($r->from_date)->format('d/m/Y') : '—',
                'to_date'    => $r->to_date ? Carbon::parse($r->to_date)->format('d/m/Y') : '—',
                'days'       => $r->days,
                'reason'     => $r->reason ?: '—',
                'status'     => $r->status,
                'applied_on' => $r->created_at ? Carbon::parse($r->created_at)->format('d/m/Y') : '—',
            ]);

        return [
            'balances' => $balances,
            'stats'    => [
                'year'      => $year,
                'allocated' => $allocated,
                'taken'     => $taken,
                'remaining' => max(0, $allocated - $taken),
                'pending'   => $pending,
                'most_used' => $mostUsed ?: 'None',
            ],
            'history'  => $history,
        ];
    }

    /** JSON: an employee's attendance for a given month (Attendance tab). */
    public function attendance(Request $request, int $id)
    {
        if (! HrEmployee::where('id', $id)->exists()) {
            return response()->json(['ok' => false, 'message' => 'Employee not found.'], 404);
        }

        $month = (string) $request->query('month', now()->format('Y-m'));
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            $start = now()->startOfMonth();
        }
        $end = $start->copy()->endOfMonth();

        $s = $this->attendanceSummary($id, $start, $end);

        $meta = [
            'present'  => ['Present', 'st-present'],
            'absent'   => ['Absent', 'st-absent'],
            'on_leave' => ['Leave', 'st-leave'],
            'half_day' => ['Half Day', 'st-half'],
        ];

        $records = $s['records']->map(function ($r) use ($meta) {
            [$label, $cls] = $meta[$r->status] ?? [ucfirst(str_replace('_', ' ', (string) $r->status)), 'st-other'];
            return [
                'date'      => Carbon::parse($r->attendance_date)->format('M j, Y'),
                'status'    => $label,
                'class'     => $cls,
                'check_in'  => $r->check_in ? Carbon::parse($r->check_in)->format('h:i A') : '--:--',
                'check_out' => $r->check_out ? Carbon::parse($r->check_out)->format('h:i A') : '--:--',
                'hours'     => $r->hours !== null ? (float) $r->hours . 'h' : '--',
                'remarks'   => $r->remarks ?: '—',
            ];
        })->values();

        return response()->json([
            'ok'           => true,
            'month_label'  => $start->format('F Y'),
            'working_days' => $s['working_days'],
            'present'      => $s['present'],
            'absent'       => $s['absent'],
            'leave'        => $s['leave'],
            'total_hours'  => $s['total_hours'],
            'records'      => $records,
        ]);
    }

    /** Attendance aggregates + records for an employee within a date range. */
    private function attendanceSummary(int $employeeId, Carbon $start, Carbon $end): array
    {
        $records = HrAttendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('attendance_date')
            ->get();

        $workingDays = 0;
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            if (! $d->isWeekend()) {
                $workingDays++;
            }
        }

        return [
            'working_days' => $workingDays,
            'present'      => $records->where('status', 'present')->count(),
            'absent'       => $records->where('status', 'absent')->count(),
            'leave'        => $records->where('status', 'on_leave')->count(),
            'total_hours'  => round((float) $records->sum('hours'), 1),
            'records'      => $records,
        ];
    }

    /** Dropdown lookups for the form. */
    private function formLookups(): array
    {
        return [
            'departments'    => HrDepartment::whereNull('deleted_at')->orderBy('department_name')->get(),
            'designations'   => HrDesignation::whereNull('deleted_at')->orderBy('designation_name')->get(),
            'qualifications' => HrQualification::whereNull('deleted_at')->orderBy('qualification')->get(),
        ];
    }

    /** Spaces folder that employee files/documents are stored under. */
    private const SPACES_DIR = 'Resume-Getlead/employees/';

    /** Upload a file to the "spaces" disk; return the stored filename. */
    private function storeFile($file, string $label): string
    {
        $name = (Str::slug($label, '_') ?: 'file') . '_' . now()->timestamp . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        Storage::disk('spaces')->put(self::SPACES_DIR . $name, file_get_contents($file), 'public');

        return $name;
    }

    /** Remove a previously-stored file from the "spaces" disk (best-effort). */
    private function deleteFile(?string $name): void
    {
        if (! $name) {
            return;
        }
        try {
            Storage::disk('spaces')->delete(self::SPACES_DIR . $name);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Resolve an employee photo path to a browser URL (empty when none). */
    private function photoUrl(?string $file): string
    {
        if (! $file) {
            return '';
        }
        //return preg_match('#^https?://#i', $file) ? $file : asset('uploads/user_files/' . ltrim($file, '/'));
        return  config('constants.file_path') . $file;
    }
}
