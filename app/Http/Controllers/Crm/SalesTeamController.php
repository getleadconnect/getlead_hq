<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CRM › Sales Team.
 *
 * Adapted from crmdemo/admin/users.php. In getlead_hq the "sales users" ARE
 * staff rows (this is the user/login table), so creating a sales-team member
 * inserts a `staff` row with role = 'sales_rep'. Auth follows the getlead_hq
 * convention: mobile normalised to the last 10 digits + a 4-digit bcrypt PIN
 * (hashed by the Staff model's `pin` cast).
 *
 * Staff writes go through the Staff model (the existing user table); CRM
 * engagement metrics are read with the query builder against the crm_* tables.
 */
class SalesTeamController extends Controller
{
    /** Role assigned to members created here. */
    private const SALES_ROLE = 'sales_rep';

    /** Roles counted as "sales team" when listing/aggregating. */
    private const SALES_ROLES = ['sales_rep', 'sales'];

    private function isAdmin(): bool
    {
        return in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary'], true);
    }

    /** Roster of sales staff, each enriched with their CRM performance. */
    public function index()
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        $reps = DB::table('staff as s')
            ->leftJoin('crm_customers as c', 'c.created_by', '=', 's.id')
            ->whereIn('s.role', self::SALES_ROLES)
            ->groupBy('s.id', 's.name', 's.role', 's.mobile', 's.active', 's.created_at')
            ->select(
                's.id', 's.name', 's.role', 's.mobile', 's.active', 's.created_at',
                DB::raw('COUNT(DISTINCT c.id) as customers_count'),
                DB::raw('COALESCE(SUM(c.views_count), 0) as total_views'),
                DB::raw('MAX(c.last_viewed_at) as last_activity')
            )
            ->orderByDesc(DB::raw('COUNT(DISTINCT c.id)'))
            ->orderBy('s.name')
            ->get();

        // Completed demos attributed to each rep (via customer.created_by).
        $completions = DB::table('crm_watch_sessions as ws')
            ->join('crm_customer_views as cv', 'ws.view_id', '=', 'cv.id')
            ->join('crm_customers as c', 'cv.customer_id', '=', 'c.id')
            ->where('ws.completed', 1)
            ->groupBy('c.created_by')
            ->select('c.created_by', DB::raw('COUNT(*) as cnt'))
            ->pluck('cnt', 'created_by');

        foreach ($reps as $rep) {
            $rep->completions = (int) ($completions[$rep->id] ?? 0);
        }

        $summary = [
            'team_size'       => $reps->count(),
            'active'          => $reps->where('active', 1)->count(),
            'total_customers' => (int) $reps->sum('customers_count'),
            'total_views'     => (int) $reps->sum('total_views'),
        ];

        return view('crm.sales-team.index', compact('reps', 'summary'));
    }

    /** Show the "add sales user" form. */
    public function create()
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        return view('crm.sales-team.form', ['member' => null]);
    }

    /** Create a new sales-team member in the staff table. */
    public function store(Request $request)
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'mobile'      => ['required', 'string', 'max:20'],
            'pin'         => ['required', 'regex:/^\d{4}$/'],
            'telegram_id' => ['nullable', 'string', 'max:100'],
        ], [], ['pin' => 'PIN']);

        $mobile = $this->normaliseMobile($data['mobile']);

        if (Staff::where('mobile', $mobile)->exists()) {
            return back()->withInput()->withErrors(['mobile' => 'A staff member with this mobile already exists.']);
        }

        Staff::create([
            'name'        => $data['name'],
            'role'        => self::SALES_ROLE,
            'mobile'      => $mobile,
            'telegram_id' => $data['telegram_id'] ?? null,
            'pin'         => $data['pin'],   // hashed by the model's `pin` cast
            'active'      => true,
        ]);

        return redirect()->route('crm.sales-team')->with('success', "Sales user created: {$data['name']}");
    }

    /** Show the edit form for a sales-team member. */
    public function edit(int $member)
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        $member = Staff::whereIn('role', self::SALES_ROLES)->find($member);
        if (! $member) {
            return redirect()->route('crm.sales-team')->with('success', 'Sales user not found.');
        }

        return view('crm.sales-team.form', compact('member'));
    }

    /** Update a sales-team member. */
    public function update(Request $request, int $member)
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        $staff = Staff::whereIn('role', self::SALES_ROLES)->find($member);
        if (! $staff) {
            return redirect()->route('crm.sales-team')->with('success', 'Sales user not found.');
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'mobile'      => ['required', 'string', 'max:20'],
            'pin'         => ['nullable', 'regex:/^\d{4}$/'],
            'telegram_id' => ['nullable', 'string', 'max:100'],
            'active'      => ['nullable'],
        ], [], ['pin' => 'PIN']);

        $mobile = $this->normaliseMobile($data['mobile']);

        if (Staff::where('mobile', $mobile)->where('id', '!=', $staff->id)->exists()) {
            return back()->withInput()->withErrors(['mobile' => 'Another staff member already uses this mobile.']);
        }

        $fields = [
            'name'        => $data['name'],
            'mobile'      => $mobile,
            'telegram_id' => $data['telegram_id'] ?? null,
            'active'      => $request->boolean('active'),
        ];
        if (! empty($data['pin'])) {
            $fields['pin'] = $data['pin']; // re-hashed by cast
        }
        $staff->update($fields);

        return redirect()->route('crm.sales-team')->with('success', 'Sales user updated.');
    }

    /** Delete a sales-team member (cannot delete your own account). */
    public function destroy(int $member)
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        if ($member === (int) Auth::guard('staff')->id()) {
            return redirect()->route('crm.sales-team')->with('success', 'You cannot delete your own account.');
        }

        Staff::whereIn('role', self::SALES_ROLES)->where('id', $member)->delete();

        return redirect()->route('crm.sales-team')->with('success', 'Sales user deleted.');
    }

    /** Normalise a mobile number to its last 10 digits (getlead_hq convention). */
    private function normaliseMobile(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile);
        return strlen($digits) > 10 ? substr($digits, -10) : $digits;
    }
}
