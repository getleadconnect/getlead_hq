<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    const ROLES = [
        'admin', 'secretary', 'sales_rep', 'support',
        'hr', 'finance', 'developer', 'tester',
    ];

    const ROLE_LABELS = [
        'admin'      => 'Admin',
        'secretary'  => 'Secretary',
        'sales_rep'  => 'Sales Rep',
        'support'    => 'Support',
        'hr'         => 'HR',
        'finance'    => 'Finance',
        'developer'  => 'Developer',
        'tester'     => 'Tester',
    ];

    private function isAdmin(): bool
    {
        return in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary']);
    }

    public function index()
    {
        if (!$this->isAdmin()) abort(403);
        $roles = self::ROLES;
        return view('team.index', compact('roles'));
    }

    public function apiList()
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);

        $staff = Staff::orderByRaw("active DESC, name ASC")->get();
        $ids   = $staff->pluck('id');

        $activeCounts = Task::whereIn('assigned_to', $ids)
            ->whereNotIn('status', ['done'])
            ->select('assigned_to', DB::raw('COUNT(*) as cnt'))
            ->groupBy('assigned_to')
            ->pluck('cnt', 'assigned_to');

        $totalCounts = Task::whereIn('assigned_to', $ids)
            ->select('assigned_to', DB::raw('COUNT(*) as cnt'))
            ->groupBy('assigned_to')
            ->pluck('cnt', 'assigned_to');

        $lastLogins = LoginHistory::whereIn('staff_id', $ids)
            ->select('staff_id', DB::raw('MAX(created_at) as last_login'))
            ->groupBy('staff_id')
            ->pluck('last_login', 'staff_id');

        $list = $staff->map(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'role'        => $s->role,
            'role_label'  => self::ROLE_LABELS[$s->role] ?? ucfirst($s->role),
            'mobile'      => $s->mobile,
            'telegram_id' => $s->telegram_id,
            'active'      => $s->active,
            'active_tasks'=> $activeCounts[$s->id] ?? 0,
            'total_tasks' => $totalCounts[$s->id] ?? 0,
            'last_login'  => $lastLogins[$s->id] ?? null,
        ]);

        return response()->json(['ok' => true, 'staff' => $list]);
    }

    public function apiSave(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);

        $data = $request->validate([
            'id'          => 'nullable|integer',
            'name'        => 'required|string|max:100',
            'role'        => 'required|in:' . implode(',', self::ROLES),
            'mobile'      => 'required|string|max:20',
            'pin'         => 'nullable|string|size:4|regex:/^\d{4}$/',
            'telegram_id' => 'nullable|string|max:100',
        ]);

        $mobile = preg_replace('/[\s\-]/', '', $data['mobile']);
        if (strlen($mobile) > 10) $mobile = substr($mobile, -10);

        $id = $data['id'] ?? null;

        if ($id) {
            $staff = Staff::find($id);
            if (!$staff) return response()->json(['ok' => false, 'error' => 'Staff not found']);

            $fields = [
                'name'        => $data['name'],
                'role'        => $data['role'],
                'mobile'      => $mobile,
                'telegram_id' => $data['telegram_id'] ?? null,
            ];
            if (!empty($data['pin'])) {
                $fields['pin'] = Hash::make($data['pin']);
            }
            $staff->update($fields);
        } else {
            if (empty($data['pin'])) {
                return response()->json(['ok' => false, 'error' => 'PIN required for new member']);
            }
            Staff::create([
                'name'        => $data['name'],
                'role'        => $data['role'],
                'mobile'      => $mobile,
                'telegram_id' => $data['telegram_id'] ?? null,
                'pin'         => Hash::make($data['pin']),
                'active'      => true,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function apiToggle(Request $request)
    {
        if (!$this->isAdmin()) return response()->json(['ok' => false], 403);

        $staff = Staff::find($request->id);
        if (!$staff) return response()->json(['ok' => false, 'error' => 'Not found']);

        // Prevent disabling own account
        if ($staff->id === Auth::guard('staff')->id()) {
            return response()->json(['ok' => false, 'error' => 'Cannot disable your own account']);
        }

        $staff->update(['active' => !$staff->active]);
        return response()->json(['ok' => true, 'active' => $staff->active]);
    }
}
