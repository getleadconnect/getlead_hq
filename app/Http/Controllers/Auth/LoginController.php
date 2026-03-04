<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('staff')->check()) {
            return $this->redirectByRole(Auth::guard('staff')->user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'string'],
            'pin'    => ['required', 'string', 'size:4'],
        ]);

        $mobile = $this->normalizeMobile($request->mobile);

        $staff = Staff::where('active', true)
            ->where(function ($query) use ($mobile) {
                $query->where('mobile', $mobile)
                      ->orWhere('mobile', '+91' . $mobile)
                      ->orWhere('mobile', 'like', '%' . $mobile);
            })
            ->first();

        if (! $staff || ! Hash::check($request->pin, $staff->pin)) {
            return back()
                ->withInput($request->only('mobile'))
                ->withErrors(['mobile' => 'Invalid credentials. Please check your PIN.']);
        }

        Auth::guard('staff')->login($staff);

        LoginHistory::create([
            'staff_id'   => $staff->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $request->session()->regenerate();

        return $this->redirectByRole($staff);
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function normalizeMobile(string $mobile): string
    {
        $mobile = preg_replace('/[\s\-]/', '', $mobile);

        if (strlen($mobile) > 10) {
            $mobile = substr($mobile, -10);
        }

        return $mobile;
    }

    private function redirectByRole(Staff $staff)
    {
        $adminRoles = ['admin', 'secretary'];

        return in_array($staff->role, $adminRoles)
            ? redirect()->route('dashboard')
            : redirect()->route('tasks');
    }
}
