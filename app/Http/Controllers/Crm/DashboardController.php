<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CRM › Dashboard.
 *
 * Ported from crmdemo/admin/dashboard.php. Uses the query builder only
 * (no Eloquent models) and the existing `staff` guard for auth. The
 * "Sales Team" figure counts sales staff, and customers link back to the
 * staff member who created them via crm_customers.created_by = staff.id.
 */
class DashboardController extends Controller
{
    /** Staff roles considered part of the sales team. */
    private const SALES_ROLES = ['sales_rep', 'sales'];

    public function index()
    {
        $totalCustomers   = $this->safe(fn () => DB::table('crm_customers')->count());
        $totalViews       = $this->safe(fn () => DB::table('crm_customer_views')->count());
        $totalCompletions = $this->safe(fn () => DB::table('crm_watch_sessions')->where('completed', 1)->count());
        $totalSales       = $this->safe(fn () => DB::table('staff')
            ->whereIn('role', self::SALES_ROLES)
            ->where('active', 1)
            ->count());

        $recentCustomers = $this->safe(fn () => DB::table('crm_customers as c')
            ->leftJoin('staff as u', 'c.created_by', '=', 'u.id')
            ->select('c.*', 'u.name as sales_name')
            ->orderByDesc('c.created_at')
            ->limit(10)
            ->get(), collect());

        return view('crm.dashboard', compact(
            'totalCustomers',
            'totalViews',
            'totalCompletions',
            'totalSales',
            'recentCustomers'
        ));
    }

    /** Run a query, swallowing errors so one missing table never 500s the page. */
    private function safe(callable $fn, mixed $default = 0): mixed
    {
        try {
            return $fn();
        } catch (\Throwable) {
            return $default;
        }
    }
}
