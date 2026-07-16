<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrDesignation;
use App\Models\HrJobCategory;
use App\Models\HrJobOpening;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * HR › Job Openings.
 *
 * CRUD for the job openings/positions advertised by the company — each opening
 * carries a title, category, position (designation), location, closing date and
 * rich-text description/details. Adapted from hr_portal's admin openings module.
 */
class JobOpeningController extends Controller
{
    /** Job openings list page. */
    public function index()
    {
        return view('Hr.job-openings.index', $this->lookups());
    }

    /** Server-side DataTables feed for the list. */
    public function data(Request $request)
    {
        $query = HrJobOpening::query()
            ->leftJoin('hr_job_category', 'hr_job_openings.job_category_id', '=', 'hr_job_category.id')
            ->leftJoin('hr_designations', 'hr_job_openings.job_designation_id', '=', 'hr_designations.id')
            ->select([
                'hr_job_openings.*',
                'hr_job_category.category_name',
                'hr_designations.designation_name',
            ]);

        // Posted-date range.
        if ($from = $request->input('from_date')) {
            $query->whereDate('hr_job_openings.created_at', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('hr_job_openings.created_at', '<=', $to);
        }
        if ($cat = $request->input('job_category_id')) {
            $query->where('hr_job_openings.job_category_id', $cat);
        }
        if ($desig = $request->input('job_designation_id')) {
            $query->where('hr_job_openings.job_designation_id', $desig);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', fn ($o) => $o->created_at ? $o->created_at->format('d-m-Y') : '—')
            ->addColumn('title', fn ($o) => '<a href="' . route('hr.job-openings.edit', $o->id) . '" class="link-name">'
                . e($o->job_title ?: '—') . '</a>')
            ->addColumn('category', fn ($o) => e($o->category_name ?: '—'))
            ->addColumn('position', fn ($o) => e($o->designation_name ?: '—'))
            ->editColumn('job_location', fn ($o) => e($o->job_location ?: '—'))
            ->addColumn('closing', fn ($o) => $o->job_closing_date ? $o->job_closing_date->format('d-m-Y') : '—')
            ->addColumn('status_badge', fn ($o) => (int) $o->status === HrJobOpening::ACTIVE
                ? '<span class="badge b-open js-badge">Active</span>'
                : '<span class="badge b-closed js-badge">Closed</span>')
            ->addColumn('action', fn ($o) => '<button type="button" class="menu-btn" data-id="' . $o->id
                . '" data-active="' . ((int) $o->status === HrJobOpening::ACTIVE ? 1 : 0) . '">&#8942;</button>')
            ->rawColumns(['title', 'status_badge', 'action'])
            ->make(true);
    }

    /** Add New Job Opening form. */
    public function create()
    {
        return view('Hr.job-openings.form', $this->lookups() + [
            'opening' => new HrJobOpening(),
            'isEdit'  => false,
        ]);
    }

    /** Persist a new job opening. */
    public function store(Request $request)
    {
        HrJobOpening::create($this->validateOpening($request) + ['status' => HrJobOpening::ACTIVE]);

        return redirect()->route('hr.job-openings')->with('success', 'Job opening created successfully.');
    }

    /** Edit Job Opening form. */
    public function edit(int $id)
    {
        $opening = HrJobOpening::findOrFail($id);

        return view('Hr.job-openings.form', $this->lookups() + [
            'opening' => $opening,
            'isEdit'  => true,
        ]);
    }

    /** Update an existing job opening. */
    public function update(Request $request, int $id)
    {
        $opening = HrJobOpening::findOrFail($id);
        $opening->update($this->validateOpening($request));

        return redirect()->route('hr.job-openings')->with('success', 'Job opening updated successfully.');
    }

    /** Delete a job opening. */
    public function destroy(int $id)
    {
        $opening = HrJobOpening::find($id);
        if (! $opening) {
            return response()->json(['status' => false, 'msg' => 'Job opening not found.'], 404);
        }

        $opening->delete();

        return response()->json(['status' => true, 'msg' => 'Job opening deleted.']);
    }

    /** Toggle a job opening active/closed. */
    public function toggle(int $id)
    {
        $opening = HrJobOpening::find($id);
        if (! $opening) {
            return response()->json(['status' => false, 'msg' => 'Job opening not found.'], 404);
        }

        $opening->status = $opening->isActive() ? HrJobOpening::INACTIVE : HrJobOpening::ACTIVE;
        $opening->save();

        return response()->json(['status' => true, 'active' => $opening->isActive(), 'msg' => 'Status updated.']);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** Category + position dropdown options shared by the list and form. */
    private function lookups(): array
    {
        return [
            'categories'   => HrJobCategory::whereNull('deleted_at')->orderBy('category_name')->get(),
            'designations' => HrDesignation::whereNull('deleted_at')->orderBy('designation_name')->get(),
        ];
    }

    /** Validation shared by store/update. */
    private function validateOpening(Request $request): array
    {
        return $request->validate([
            'job_title'          => ['required', 'string', 'max:200'],
            'job_category_id'    => ['nullable', 'integer', 'exists:hr_job_category,id'],
            'job_designation_id' => ['nullable', 'integer', 'exists:hr_designations,id'],
            'job_location'       => ['nullable', 'string', 'max:100'],
            'job_closing_date'   => ['nullable', 'date'],
            'job_description'    => ['nullable', 'string'],
            'job_details'        => ['nullable', 'string'],
        ]);
    }
}
