<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrAllowance;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use App\Models\HrJobCategory;
use App\Models\HrLeaveSetting;
use App\Models\HrNotificationSetting;
use App\Models\HrQualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

/**
 * HR › Settings.
 *
 * Vertical-tab settings hub. Only the "Job Category" tab is implemented for now;
 * the remaining tabs (Qualifications, Departments, Designations, Leave Settings,
 * Allowances, Telegram Notifications) are placeholders for a later phase.
 */
class SettingsController extends Controller
{
    /** Settings page — defaults to the Job Category tab. */
    public function index()
    {
        // Telegram notification toggles, keyed for easy lookup in the view.
        $notifications = HrNotificationSetting::whereIn('setting_key', [
            HrNotificationSetting::KEY_NEW_APPLICATION,
            HrNotificationSetting::KEY_STATUS_CHANGE,
        ])->get()->keyBy('setting_key');

        $telegramConfigured = $this->telegramConfigured();

        return view('Hr.settings.index', compact('notifications', 'telegramConfigured'));
    }

    /** Toggle a Telegram notification setting on/off. */
    public function notificationToggle(Request $request)
    {
        $data = $request->validate([
            'setting_key' => ['required', 'string', Rule::in([
                HrNotificationSetting::KEY_NEW_APPLICATION,
                HrNotificationSetting::KEY_STATUS_CHANGE,
            ])],
            'enabled' => ['required', 'boolean'],
        ]);

        $setting = HrNotificationSetting::where('setting_key', $data['setting_key'])->first();
        if (! $setting) {
            return response()->json(['status' => false, 'msg' => 'Setting not found.'], 404);
        }

        // Only allow turning a notification ON when the Telegram env credentials exist.
        if ($data['enabled'] && ! $this->telegramConfigured()) {
            return response()->json([
                'status'     => false,
                'configured' => false,
                'msg'        => 'TELEGRAM_HR_BOT_TOKEN and TELEGRAM_HR_CHAT_ID are not configured in the .env file. Please set them in the environment before enabling Telegram notifications.',
            ], 422);
        }

        $setting->setting_value = $data['enabled'] ? 1 : 0;
        $setting->save();

        return response()->json(['status' => true, 'enabled' => (bool) $setting->setting_value, 'msg' => 'Notification setting updated.']);
    }

    /** True when both TELEGRAM_HR_BOT_TOKEN and TELEGRAM_HR_CHAT_ID are set in the environment. */
    private function telegramConfigured(): bool
    {
        return filled(config('services.telegram.hr.bot_token'))
            && filled(config('services.telegram.hr.chat_id'));
    }

    // ── Job Category ───────────────────────────────────────────────

    /** Server-side DataTables feed for job categories. */
    public function jobCategoryData(Request $request)
    {
        $query = HrJobCategory::query()->select(['id', 'category_name', 'status', 'created_at']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('category_name', fn ($c) => e($c->category_name))
            ->addColumn('status_badge', fn ($c) => (int) $c->status === 1
                ? '<span class="badge b-active js-badge">Active</span>'
                : '<span class="badge b-inactive js-badge">Inactive</span>')
            ->addColumn('action', fn ($c) => '<div class="row-acts">'
                . '<button type="button" class="ic-btn edit-jc" data-id="' . $c->id
                . '" data-name="' . e($c->category_name) . '" title="Edit">'
                . '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>'
                . '<button type="button" class="ic-btn danger del-jc" data-id="' . $c->id
                . '" data-name="' . e($c->category_name) . '" title="Delete">'
                . '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
                . '</div>')
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /** Create a new job category. */
    public function jobCategoryStore(Request $request)
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:hr_job_category,category_name'],
        ]);

        HrJobCategory::create(['category_name' => $data['category_name'], 'status' => 1]);

        return response()->json(['status' => true, 'msg' => 'Category added.']);
    }

    /** Rename an existing job category. */
    public function jobCategoryUpdate(Request $request, int $id)
    {
        $cat = HrJobCategory::find($id);
        if (! $cat) {
            return response()->json(['status' => false, 'msg' => 'Category not found.'], 404);
        }

        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:hr_job_category,category_name,' . $id],
        ]);

        $cat->update(['category_name' => $data['category_name']]);

        return response()->json(['status' => true, 'msg' => 'Category updated.']);
    }

    /** Toggle a job category active/inactive. */
    public function jobCategoryToggle(int $id)
    {
        $cat = HrJobCategory::find($id);
        if (! $cat) {
            return response()->json(['status' => false, 'msg' => 'Category not found.'], 404);
        }

        $cat->status = (int) $cat->status === 1 ? 0 : 1;
        $cat->save();

        return response()->json(['status' => true, 'active' => (int) $cat->status === 1, 'msg' => 'Status updated.']);
    }

    /** Delete a job category (soft delete). */
    public function jobCategoryDestroy(int $id)
    {
        $cat = HrJobCategory::find($id);
        if (! $cat) {
            return response()->json(['status' => false, 'msg' => 'Category not found.'], 404);
        }

        $cat->delete();

        return response()->json(['status' => true, 'msg' => 'Category deleted.']);
    }

    // ── Qualifications ─────────────────────────────────────────────

    /** Server-side DataTables feed for qualifications. */
    public function qualificationData(Request $request)
    {
        $query = HrQualification::query()
            ->leftJoin('users', 'hr_qualifications.created_by', '=', 'users.id')
            ->select([
                'hr_qualifications.id',
                'hr_qualifications.qualification',
                'hr_qualifications.created_at',
                'users.name as creator_name',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('qualification', fn ($q) => e($q->qualification))
            ->addColumn('creator', fn ($q) => '<span class="creator-badge">' . e($q->creator_name ?: 'Admin') . '</span>')
            ->addColumn('action', fn ($q) => '<div class="row-acts">'
                . '<button type="button" class="ic-btn edit-ql" data-id="' . $q->id
                . '" data-name="' . e($q->qualification) . '" title="Edit">'
                . '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>'
                . '<button type="button" class="ic-btn danger del-ql" data-id="' . $q->id
                . '" data-name="' . e($q->qualification) . '" title="Delete">'
                . '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
                . '</div>')
            ->rawColumns(['creator', 'action'])
            ->make(true);
    }

    /** Create a new qualification. */
    public function qualificationStore(Request $request)
    {
        $data = $request->validate([
            'qualification' => ['required', 'string', 'max:100', 'unique:hr_qualifications,qualification'],
        ]);

        HrQualification::create([
            'qualification' => $data['qualification'],
            'status'        => 1,
            'created_by'    => Auth::guard('staff')->id(),
        ]);

        return response()->json(['status' => true, 'msg' => 'Qualification added.']);
    }

    /** Rename an existing qualification. */
    public function qualificationUpdate(Request $request, int $id)
    {
        $q = HrQualification::find($id);
        if (! $q) {
            return response()->json(['status' => false, 'msg' => 'Qualification not found.'], 404);
        }

        $data = $request->validate([
            'qualification' => ['required', 'string', 'max:100', 'unique:hr_qualifications,qualification,' . $id],
        ]);

        $q->update(['qualification' => $data['qualification']]);

        return response()->json(['status' => true, 'msg' => 'Qualification updated.']);
    }

    /** Delete a qualification (soft delete). */
    public function qualificationDestroy(int $id)
    {
        $q = HrQualification::find($id);
        if (! $q) {
            return response()->json(['status' => false, 'msg' => 'Qualification not found.'], 404);
        }

        $q->delete();

        return response()->json(['status' => true, 'msg' => 'Qualification deleted.']);
    }

    // ── Departments ────────────────────────────────────────────────

    /** Server-side DataTables feed for departments. */
    public function departmentData(Request $request)
    {
        $query = HrDepartment::query()
            ->leftJoin('users', 'hr_departments.created_by', '=', 'users.id')
            ->select([
                'hr_departments.id',
                'hr_departments.department_name',
                'hr_departments.created_at',
                'users.name as creator_name',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('department_name', fn ($d) => e($d->department_name))
            ->addColumn('creator', fn ($d) => '<span class="creator-badge">' . e($d->creator_name ?: 'Admin') . '</span>')
            ->addColumn('action', fn ($d) => '<div class="row-acts">'
                . '<button type="button" class="ic-btn edit-dp" data-id="' . $d->id
                . '" data-name="' . e($d->department_name) . '" title="Edit">'
                . '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>'
                . '<button type="button" class="ic-btn danger del-dp" data-id="' . $d->id
                . '" data-name="' . e($d->department_name) . '" title="Delete">'
                . '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
                . '</div>')
            ->rawColumns(['creator', 'action'])
            ->make(true);
    }

    /** Create a new department. */
    public function departmentStore(Request $request)
    {
        $data = $request->validate([
            'department_name' => ['required', 'string', 'max:191', 'unique:hr_departments,department_name'],
        ]);

        HrDepartment::create([
            'department_name' => $data['department_name'],
            'status'          => 1,
            'created_by'      => Auth::guard('staff')->id(),
        ]);

        return response()->json(['status' => true, 'msg' => 'Department added.']);
    }

    /** Rename an existing department. */
    public function departmentUpdate(Request $request, int $id)
    {
        $dept = HrDepartment::find($id);
        if (! $dept) {
            return response()->json(['status' => false, 'msg' => 'Department not found.'], 404);
        }

        $data = $request->validate([
            'department_name' => ['required', 'string', 'max:191', 'unique:hr_departments,department_name,' . $id],
        ]);

        $dept->update(['department_name' => $data['department_name']]);

        return response()->json(['status' => true, 'msg' => 'Department updated.']);
    }

    /** Delete a department (soft delete). */
    public function departmentDestroy(int $id)
    {
        $dept = HrDepartment::find($id);
        if (! $dept) {
            return response()->json(['status' => false, 'msg' => 'Department not found.'], 404);
        }

        $dept->delete();

        return response()->json(['status' => true, 'msg' => 'Department deleted.']);
    }

    // ── Designations ───────────────────────────────────────────────

    /** Server-side DataTables feed for designations. */
    public function designationData(Request $request)
    {
        $query = HrDesignation::query()
            ->leftJoin('users', 'hr_designations.created_by', '=', 'users.id')
            ->select([
                'hr_designations.id',
                'hr_designations.designation_name',
                'hr_designations.created_at',
                'users.name as creator_name',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('designation_name', fn ($d) => e($d->designation_name))
            ->addColumn('creator', fn ($d) => '<span class="creator-badge">' . e($d->creator_name ?: 'Admin') . '</span>')
            ->addColumn('action', fn ($d) => '<div class="row-acts">'
                . '<button type="button" class="ic-btn edit-ds" data-id="' . $d->id
                . '" data-name="' . e($d->designation_name) . '" title="Edit">'
                . '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>'
                . '<button type="button" class="ic-btn danger del-ds" data-id="' . $d->id
                . '" data-name="' . e($d->designation_name) . '" title="Delete">'
                . '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
                . '</div>')
            ->rawColumns(['creator', 'action'])
            ->make(true);
    }

    /** Create a new designation. */
    public function designationStore(Request $request)
    {
        $data = $request->validate([
            'designation_name' => ['required', 'string', 'max:191', 'unique:hr_designations,designation_name'],
        ]);

        HrDesignation::create([
            'designation_name' => $data['designation_name'],
            'status'           => 1,
            'created_by'       => Auth::guard('staff')->id(),
        ]);

        return response()->json(['status' => true, 'msg' => 'Designation added.']);
    }

    /** Rename an existing designation. */
    public function designationUpdate(Request $request, int $id)
    {
        $desig = HrDesignation::find($id);
        if (! $desig) {
            return response()->json(['status' => false, 'msg' => 'Designation not found.'], 404);
        }

        $data = $request->validate([
            'designation_name' => ['required', 'string', 'max:191', 'unique:hr_designations,designation_name,' . $id],
        ]);

        $desig->update(['designation_name' => $data['designation_name']]);

        return response()->json(['status' => true, 'msg' => 'Designation updated.']);
    }

    /** Delete a designation (soft delete). */
    public function designationDestroy(int $id)
    {
        $desig = HrDesignation::find($id);
        if (! $desig) {
            return response()->json(['status' => false, 'msg' => 'Designation not found.'], 404);
        }

        $desig->delete();

        return response()->json(['status' => true, 'msg' => 'Designation deleted.']);
    }

    // ── Leave Settings ─────────────────────────────────────────────

    /** All leave types + those still available to configure (dropdown state). */
    public function leaveSettingTypes()
    {
        $all  = HrLeaveSetting::getLeaveTypes();
        $used = HrLeaveSetting::whereNull('deleted_at')->pluck('leave_type')->all();

        return response()->json([
            'status'    => true,
            'all'       => $all,
            'available' => array_values(array_diff($all, $used)),
        ]);
    }

    /** Server-side DataTables feed for leave settings. */
    public function leaveSettingData(Request $request)
    {
        $query = HrLeaveSetting::query()
            ->leftJoin('users', 'hr_leave_settings.created_by', '=', 'users.id')
            ->select([
                'hr_leave_settings.id',
                'hr_leave_settings.leave_type',
                'hr_leave_settings.no_of_days',
                'hr_leave_settings.created_at',
                'users.name as creator_name',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('leave_type', fn ($l) => e($l->leave_type))
            ->editColumn('no_of_days', fn ($l) => (int) $l->no_of_days)
            ->addColumn('creator', fn ($l) => '<span class="creator-badge">' . e($l->creator_name ?: 'Admin') . '</span>')
            ->addColumn('action', fn ($l) => '<div class="row-acts">'
                . '<button type="button" class="ic-btn edit-ls" data-id="' . $l->id
                . '" data-type="' . e($l->leave_type) . '" data-days="' . (int) $l->no_of_days . '" title="Edit">'
                . '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>'
                . '<button type="button" class="ic-btn danger del-ls" data-id="' . $l->id
                . '" data-name="' . e($l->leave_type) . '" title="Delete">'
                . '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
                . '</div>')
            ->rawColumns(['creator', 'action'])
            ->make(true);
    }

    /** Create a new leave setting (each leave type only once). */
    public function leaveSettingStore(Request $request)
    {
        $data = $request->validate([
            'leave_type' => [
                'required',
                Rule::in(HrLeaveSetting::getLeaveTypes()),
                Rule::unique('hr_leave_settings', 'leave_type')->whereNull('deleted_at'),
            ],
            'no_of_days' => ['required', 'integer', 'min:0'],
        ], ['leave_type.unique' => 'This leave type is already configured.']);

        // The DB has a unique index on leave_type that ignores soft-deletes, so a
        // previously-deleted type must be restored/updated rather than re-inserted.
        $existing = HrLeaveSetting::withTrashed()->where('leave_type', $data['leave_type'])->first();

        if ($existing) {
            $existing->restore();
            $existing->update([
                'no_of_days' => $data['no_of_days'],
                'status'     => 1,
                'created_by' => Auth::guard('staff')->id(),
            ]);
        } else {
            HrLeaveSetting::create([
                'leave_type' => $data['leave_type'],
                'no_of_days' => $data['no_of_days'],
                'status'     => 1,
                'created_by' => Auth::guard('staff')->id(),
            ]);
        }

        return response()->json(['status' => true, 'msg' => 'Leave setting added.']);
    }

    /** Update a leave setting's allocated days. */
    public function leaveSettingUpdate(Request $request, int $id)
    {
        $setting = HrLeaveSetting::find($id);
        if (! $setting) {
            return response()->json(['status' => false, 'msg' => 'Leave setting not found.'], 404);
        }

        $data = $request->validate([
            'no_of_days' => ['required', 'integer', 'min:0'],
        ]);

        $setting->update(['no_of_days' => $data['no_of_days']]);

        return response()->json(['status' => true, 'msg' => 'Leave setting updated.']);
    }

    /** Delete a leave setting (soft delete). */
    public function leaveSettingDestroy(int $id)
    {
        $setting = HrLeaveSetting::find($id);
        if (! $setting) {
            return response()->json(['status' => false, 'msg' => 'Leave setting not found.'], 404);
        }

        $setting->delete();

        return response()->json(['status' => true, 'msg' => 'Leave setting deleted.']);
    }

    // ── Allowances ─────────────────────────────────────────────────

    /** Server-side DataTables feed for allowances. */
    public function allowanceData(Request $request)
    {
        $query = HrAllowance::query()
            ->leftJoin('users', 'hr_allowances.created_by', '=', 'users.id')
            ->select([
                'hr_allowances.id',
                'hr_allowances.allowance_type',
                'hr_allowances.percentage',
                'hr_allowances.created_at',
                'users.name as creator_name',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('allowance_type', fn ($a) => e($a->allowance_type))
            ->editColumn('percentage', fn ($a) => number_format((float) $a->percentage, 2) . '%')
            ->addColumn('creator', fn ($a) => '<span class="creator-badge">' . e($a->creator_name ?: 'Admin') . '</span>')
            ->addColumn('action', fn ($a) => '<div class="row-acts">'
                . '<button type="button" class="ic-btn edit-al" data-id="' . $a->id
                . '" data-type="' . e($a->allowance_type) . '" data-pct="' . (float) $a->percentage . '" title="Edit">'
                . '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>'
                . '<button type="button" class="ic-btn danger del-al" data-id="' . $a->id
                . '" data-name="' . e($a->allowance_type) . '" title="Delete">'
                . '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
                . '</div>')
            ->rawColumns(['creator', 'action'])
            ->make(true);
    }

    /** Create a new allowance. */
    public function allowanceStore(Request $request)
    {
        $data = $request->validate([
            'allowance_type' => [
                'required', 'string', 'max:191',
                Rule::unique('hr_allowances', 'allowance_type')->whereNull('deleted_at'),
            ],
            'percentage'     => ['required', 'numeric', 'min:0', 'max:100'],
        ], ['allowance_type.unique' => 'This allowance type is already configured.']);

        // The DB has a unique index on allowance_type that ignores soft-deletes, so a
        // previously-deleted type must be restored/updated rather than re-inserted.
        $existing = HrAllowance::withTrashed()->where('allowance_type', $data['allowance_type'])->first();

        if ($existing) {
            $existing->restore();
            $existing->update([
                'percentage' => $data['percentage'],
                'status'     => 1,
                'created_by' => Auth::guard('staff')->id(),
            ]);
        } else {
            HrAllowance::create([
                'allowance_type' => $data['allowance_type'],
                'percentage'     => $data['percentage'],
                'status'         => 1,
                'created_by'     => Auth::guard('staff')->id(),
            ]);
        }

        return response()->json(['status' => true, 'msg' => 'Allowance added.']);
    }

    /** Update an allowance's type + percentage. */
    public function allowanceUpdate(Request $request, int $id)
    {
        $allowance = HrAllowance::find($id);
        if (! $allowance) {
            return response()->json(['status' => false, 'msg' => 'Allowance not found.'], 404);
        }

        $data = $request->validate([
            'allowance_type' => ['required', 'string', 'max:191', 'unique:hr_allowances,allowance_type,' . $id],
            'percentage'     => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $allowance->update([
            'allowance_type' => $data['allowance_type'],
            'percentage'     => $data['percentage'],
        ]);

        return response()->json(['status' => true, 'msg' => 'Allowance updated.']);
    }

    /** Delete an allowance (soft delete). */
    public function allowanceDestroy(int $id)
    {
        $allowance = HrAllowance::find($id);
        if (! $allowance) {
            return response()->json(['status' => false, 'msg' => 'Allowance not found.'], 404);
        }

        $allowance->delete();

        return response()->json(['status' => true, 'msg' => 'Allowance deleted.']);
    }
}
