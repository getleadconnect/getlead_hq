<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrJobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * HR › Job Openings.
 *
 * Manage the job categories/roles applicants can apply for (the same list that
 * powers the public application form's "Applied For" dropdown), each shown with
 * how many applications it has received.
 */
class JobOpeningController extends Controller
{
    public function index()
    {
        $openings = HrJobCategory::select('hr_job_category.*', DB::raw('COUNT(hr_applications.id) as applications_count'))
            ->leftJoin('hr_applications', function ($join) {
                $join->on('hr_applications.job_category_id', '=', 'hr_job_category.id')
                     ->whereNull('hr_applications.deleted_at');
            })
            ->groupBy('hr_job_category.id', 'hr_job_category.category_name', 'hr_job_category.status', 'hr_job_category.deleted_at', 'hr_job_category.created_at', 'hr_job_category.updated_at')
            ->orderBy('hr_job_category.category_name')
            ->get();

        return view('Hr.job-openings.index', compact('openings'));
    }

    /** Create a new job opening (category). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:hr_job_category,category_name'],
        ]);

        HrJobCategory::create(['category_name' => $data['category_name'], 'status' => 1]);

        return redirect()->route('hr.job-openings')->with('success', 'Job opening added.');
    }

    /** Toggle a job opening active/closed. */
    public function toggle(int $id)
    {
        $cat = HrJobCategory::find($id);
        if (! $cat) {
            return response()->json(['status' => false, 'msg' => 'Not found.'], 404);
        }

        $cat->status = $cat->status ? 0 : 1;
        $cat->save();

        return response()->json(['status' => true, 'active' => (bool) $cat->status]);
    }
}
