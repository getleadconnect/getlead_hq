<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function normalizeMobile(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile);
        return substr($digits, -10);
    }

    // POST /api/auth/login
    public function login(Request $request)
    {
        $mobile = $request->input('mobile', '');
        $pin    = $request->input('pin', '');

        if (!$mobile || !$pin) {
            return response()->json(['ok' => false, 'error' => 'Mobile and PIN required'], 422);
        }

        $normalized = $this->normalizeMobile($mobile);

        $staff = Staff::where('mobile', $normalized)
            ->where('active', true)
            ->first();

        if (!$staff || !Hash::check($pin, $staff->pin)) {
            return response()->json(['ok' => false, 'error' => 'Invalid credentials'], 401);
        }

        // Record login history
        LoginHistory::create([
            'staff_id'   => $staff->id,
            'ip_address' => $request->ip(),
        ]);

        // Create Sanctum token (expires 30 days)
        $token = $staff->createToken('mobile-app', ['*'], now()->addDays(30));

        return response()->json([
            'ok'    => true,
            'token' => $token->plainTextToken,
            'staff' => [
                'id'   => $staff->id,
                'name' => $staff->name,
                'role' => $staff->role,
            ],
        ]);
    }

    // POST /api/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['ok' => true]);
    }

    // GET /api/auth/me
    public function me(Request $request)
    {
        $staff = $request->user();
        return response()->json([
            'ok'    => true,
            'staff' => [
                'id'   => $staff->id,
                'name' => $staff->name,
                'role' => $staff->role,
            ],
        ]);
    }
}
