<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\LoginHistory;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiStaffController extends Controller
{
    const ROLE_LABELS = [
        'admin'     => 'Admin',
        'secretary' => 'Secretary',
        'sales_rep' => 'Sales Rep',
        'support'   => 'Support',
        'hr'        => 'HR',
        'finance'   => 'Finance',
        'developer' => 'Developer',
        'tester'    => 'Tester',
    ];

    private function isAdmin($staff): bool
    {
        return in_array($staff->role, ['admin', 'secretary']);
    }

    private function normalizeMobile(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile);
        return substr($digits, -10);
    }

    // GET /api/staff
    public function staffList(Request $request)
    {
        $staff = Staff::where('active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [
                'id'          => $s->id,
                'name'        => $s->name,
                'role'        => $s->role,
                'mobile'      => $s->mobile,
                'telegram_id' => $s->telegram_id,
                'active'      => $s->active ? 1 : 0,
                'role_label'  => self::ROLE_LABELS[$s->role] ?? $s->role,
            ]);

        return response()->json(['staff' => $staff]);
    }

    // GET /api/team
    public function teamList(Request $request)
    {
        $user = $request->user();
        if (!$this->isAdmin($user)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $today   = now()->toDateString();
        $members = Staff::orderBy('name')->get()->map(function ($s) use ($today) {
            $initials = collect(explode(' ', $s->name))
                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                ->take(2)
                ->implode('');

            return [
                'id'               => $s->id,
                'name'             => $s->name,
                'role'             => $s->role,
                'mobile'           => $s->mobile,
                'telegram_id'      => $s->telegram_id,
                'active'           => $s->active ? 1 : 0,
                'role_label'       => self::ROLE_LABELS[$s->role] ?? $s->role,
                'active_tasks'     => Task::where('assigned_to', $s->id)->whereIn('status', ['pending', 'in_progress', 'blocked'])->count(),
                'last_report_date' => DailyReport::where('staff_id', $s->id)->orderByDesc('report_date')->value('report_date'),
                'last_login'       => LoginHistory::where('staff_id', $s->id)->orderByDesc('created_at')->value('created_at'),
                'initials'         => $initials,
            ];
        });

        return response()->json(['staff' => $members]);
    }

    // POST /api/team
    public function teamAdd(Request $request)
    {
        $user = $request->user();
        if (!$this->isAdmin($user)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $name   = trim($request->input('name', ''));
        $role   = $request->input('role', '');
        $pin    = $request->input('pin', '');
        $mobile = $request->input('mobile', '');

        if (!$name || !$role || !$pin || !$mobile) {
            return response()->json(['ok' => false, 'error' => 'Name, role, mobile and PIN are required'], 422);
        }

        $normalized = $this->normalizeMobile($mobile);

        if (Staff::where('mobile', $normalized)->exists()) {
            return response()->json(['ok' => false, 'error' => 'Mobile number already registered'], 422);
        }

        $staff = Staff::create([
            'name'        => $name,
            'role'        => $role,
            'mobile'      => $normalized,
            'telegram_id' => $request->input('telegram_id', ''),
            'pin'         => Hash::make($pin),
            'active'      => true,
        ]);

        return response()->json(['ok' => true, 'id' => $staff->id]);
    }

    // PUT /api/team/{id}
    public function teamUpdate(Request $request, int $id)
    {
        $user = $request->user();
        if (!$this->isAdmin($user)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        $staff = Staff::findOrFail($id);

        if ($request->filled('name'))     $staff->name        = $request->input('name');
        if ($request->filled('role'))     $staff->role        = $request->input('role');
        if ($request->filled('mobile'))   $staff->mobile      = $this->normalizeMobile($request->input('mobile'));
        if ($request->has('telegram_id')) $staff->telegram_id = $request->input('telegram_id');
        if ($request->filled('pin'))      $staff->pin         = Hash::make($request->input('pin'));

        $staff->save();

        return response()->json(['ok' => true]);
    }

    // PATCH /api/team/{id}/toggle
    public function teamToggle(Request $request, int $id)
    {
        $user = $request->user();
        if (!$this->isAdmin($user)) {
            return response()->json(['ok' => false, 'error' => 'Admin only'], 403);
        }

        if ($id === $user->id) {
            return response()->json(['ok' => false, 'error' => 'Cannot disable your own account'], 422);
        }

        $staff = Staff::findOrFail($id);
        $staff->active = !$staff->active;
        $staff->save();

        return response()->json(['ok' => true, 'active' => $staff->active]);
    }
}
