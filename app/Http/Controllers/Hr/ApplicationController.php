<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrApplication;
use App\Models\HrJobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use App\Services\TelegramService;
use App\Models\HrNotificationSetting;
/**
 * HR › Applications.
 *
 * Server-side (Yajra) DataTable of submitted applications, a full-page details
 * view, inline status changes, and delete. Adapted from hr_portal's
 * Admin\ApplicationController into the getlead_hq design system.
 */
class ApplicationController extends Controller
{
    /** List page (the table is loaded via the data() endpoint). */
    public function index()
    {
        $categories = HrJobCategory::orderBy('category_name')->get();
        $statuses   = HrApplication::$statuses;

        return view('Hr.applications.index', compact('categories', 'statuses'));
    }

    /** Yajra DataTables JSON feed. */
    public function data(Request $request)
    {
        $query = HrApplication::query()
            ->select('hr_applications.*', 'hr_job_category.category_name')
            ->leftJoin('hr_job_category', 'hr_applications.job_category_id', '=', 'hr_job_category.id')
            ->when($request->filled('job_category_id'), fn ($q) => $q->where('hr_applications.job_category_id', $request->job_category_id))
            ->when($request->filled('status'), fn ($q) => $q->where('hr_applications.status', $request->status))
            ->orderByDesc('hr_applications.id');

        $statuses = HrApplication::$statuses;

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('dated', fn ($r) => $r->created_at ? $r->created_at->format('d-m-Y') : '—')
            ->addColumn('name', function ($r) {
                $name = $r->name ? e(ucwords($r->name)) : '--Nil--';
                return '<a href="' . route('hr.applications.show', $r->id) . '" class="link-name">' . $name . '</a>';
            })
            ->addColumn('photo', function ($r) {
                if (! $r->photo) {
                    return '<span class="text-muted">--</span>';
                }
                $src = $this->fileUrl($r->photo);
                return '<img src="' . e($src) . '" class="app-photo" onerror="this.style.display=\'none\'">';
            })
            ->addColumn('mobile', fn ($r) => e(($r->countrycode ?? '') . $r->mobile))
            ->addColumn('job_cat', fn ($r) => e($r->category_name ?? '—'))
            ->addColumn('salary', fn ($r) => 'Last: ' . e($r->last_drawn_salary ?: 'N/A') . '<br>Expected: ' . e($r->expected_salary ?: 'N/A'))
            ->addColumn('cv_file', function ($r) {
                return $r->cv_file
                    ? '<a href="' . e($this->fileUrl($r->cv_file)) . '" target="_blank" class="link-name">View CV</a>'
                    : '<span class="text-muted">N/A</span>';
            })
            ->addColumn('status', function ($r) use ($statuses) {
                $opts = '';
                foreach ($statuses as $s) {
                    $opts .= '<option value="' . e($s) . '"' . ($r->status === $s ? ' selected' : '') . '>' . e($s) . '</option>';
                }
                $cls = 'st-' . \Illuminate\Support\Str::slug($r->status ?: 'new');
                return '<select class="status-select ' . $cls . '" data-id="' . $r->id . '">' . $opts . '</select>';
            })
            ->addColumn('reason', fn ($r) => $r->rejection_reason ? e($r->rejection_reason) : '<span class="text-muted">-</span>')
            ->addColumn('action', function ($r) {
                return '<button type="button" class="menu-btn" data-id="' . $r->id . '" aria-label="Actions">&#8942;</button>';
            })
            ->rawColumns(['name', 'photo', 'salary', 'cv_file', 'status', 'reason', 'action'])
            ->make(true);
    }

    /** Full-page application details. */
    public function show(int $id)
    {
        $app = HrApplication::select('hr_applications.*', 'hr_job_category.category_name')
            ->leftJoin('hr_job_category', 'hr_applications.job_category_id', '=', 'hr_job_category.id')
            ->where('hr_applications.id', $id)
            ->first();

        if (! $app) {
            return redirect()->route('hr.applications')->with('error', 'Application not found.');
        }

        $app->photo_url = $app->photo ? $this->fileUrl($app->photo) : '';
        $app->cv_url    = $app->cv_file ? $this->fileUrl($app->cv_file) : '';
        $statuses       = HrApplication::$statuses;

        return view('Hr.applications.details', compact('app', 'statuses'));
    }

    /** Inline status change (+ optional rejection reason). */
    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status'           => ['required', 'in:' . implode(',', HrApplication::$statuses)],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $app = HrApplication::find($id);
        if (! $app) {
            return response()->json(['status' => false, 'msg' => 'Application not found.'], 404);
        }

        $app->status = $data['status'];
        // "New" is the default/reset state — it carries no reason. Every other
        // status change records the reason entered in the modal.
        if ($data['status'] === HrApplication::STATUS_NEW) {
            $app->rejection_reason = null;
        } else {
            $app->rejection_reason = $data['rejection_reason'] ?? $app->rejection_reason;
        }
        $app->save();


            try{
                // Send Telegram notification (except for "New" status) if enabled
                if ($request->status !== 'New' && HrNotificationSetting::isStatusChangeNotificationEnabled()) {
                    $applicationWithCategory = HrApplication::select('hr_applications.*', 'hr_job_category.category_name')
                        ->leftJoin('hr_job_category', 'hr_applications.job_category_id', '=', 'hr_job_category.id')
                        ->where('hr_applications.id', $id)
                        ->first();

                    $telegramService = new TelegramService();
                    $telegramService->sendStatusChangeNotification($applicationWithCategory, $request->status, $request->rejection_reason);
                }
            }
            catch(\Exception $e)
            {
                \Log::info($e->getMessage());
            }


        return response()->json(['status' => true, 'msg' => 'Status updated.', 'app_status' => $app->status, 'reason' => $app->rejection_reason]);
    }

    /** Delete an application (soft delete). */
    public function destroy(int $id)
    {
        $app = HrApplication::find($id);
        if (! $app) {
            return response()->json(['status' => false, 'msg' => 'Application not found.'], 404);
        }

        $app->delete();

        return response()->json(['status' => true, 'msg' => 'Application successfully removed.']);
    }

    /** Resolve a stored file path to a browser URL (full URL as-is, else public asset). */
    private function fileUrl(string $path): string
    {
        //$path="/uploads/user_files/".$path;
        //return preg_match('#^https?://#i', $path) ? $path : asset(ltrim($path, '/'));
        $path=config('constants.file_path').$path;
        return $path;
    }
}
