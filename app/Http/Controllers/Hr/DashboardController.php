<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrApplication;
use App\Models\HrEmployee;
use App\Models\HrAttendance;
use App\Models\HrLeaveRequest;

use Illuminate\Support\Facades\DB;

/**
 * HR › Dashboard.
 *
 * Overview of recruitment activity — application volume over time, status
 * breakdown, top applied-for roles, and the most recent applications.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $totals = [
            'all'          => HrApplication::totalCount(),
            'employees'    => HrEmployee::totalCount(),
            // Employee metrics (ported from hr_portal admin dashboard).
            'present_today'=> HrAttendance::whereDate('attendance_date', $today)
                ->where('status', 'present')
                ->count(),
            'on_leave_today' => HrLeaveRequest::whereNull('deleted_at')
                ->where('status', 'approved')
                ->whereDate('from_date', '<=', $today)
                ->whereDate('to_date', '>=', $today)
                ->count(),
            'pending_leaves' => HrLeaveRequest::whereNull('deleted_at')
                ->where('status', 'pending')
                ->count(),
        ];

        // Employees who are on leave today (for the "On Leave Today" card modal).
        $onLeaveList = HrLeaveRequest::query()
            ->from('hr_leave_requests')
            ->leftJoin('hr_employees', 'hr_leave_requests.employee_id', '=', 'hr_employees.id')
            ->whereNull('hr_leave_requests.deleted_at')
            ->where('hr_leave_requests.status', 'approved')
            ->whereDate('hr_leave_requests.from_date', '<=', $today)
            ->whereDate('hr_leave_requests.to_date', '>=', $today)
            ->select('hr_employees.full_name', 'hr_leave_requests.leave_type', 'hr_leave_requests.from_date', 'hr_leave_requests.to_date')
            ->orderBy('hr_employees.full_name')
            ->get();

        // Applications grouped by status (ordered by the model's canonical list).
        $statusRaw = HrApplication::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');
        $statusCounts = collect(HrApplication::$statuses)
            ->mapWithKeys(fn ($s) => [$s => (int) ($statusRaw[$s] ?? 0)]);

        // Top applied-for roles.
        $topRoles = HrApplication::select('hr_job_category.category_name', DB::raw('COUNT(*) as c'))
            ->leftJoin('hr_job_category', 'hr_applications.job_category_id', '=', 'hr_job_category.id')
            ->groupBy('hr_job_category.category_name')
            ->orderByDesc('c')
            ->limit(6)
            ->get();

        // Most recent applications.
        $recent = HrApplication::select('hr_applications.*', 'hr_job_category.category_name')
            ->leftJoin('hr_job_category', 'hr_applications.job_category_id', '=', 'hr_job_category.id')
            ->orderByDesc('hr_applications.id')
            ->limit(8)
            ->get();

        return view('Hr.dashboard', compact('totals', 'statusCounts', 'topRoles', 'recent', 'onLeaveList'));
    }
}
