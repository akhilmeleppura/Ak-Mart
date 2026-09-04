<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SecurityCenterController extends Controller
{
    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized access to security center.');
        }
        $roles = method_exists($user, 'roles') ? $user->roles->pluck('name')->map('strtolower')->toArray() : [];
        $role = strtolower($user->role ?? '');
        $userType = strtolower($user->user_type ?? '');
        $isSuper = !empty($user->is_supreme_admin) || !empty($user->is_super_admin);

        $hasAccess = $isSuper
            || in_array('admin', $roles) || in_array('super_admin', $roles)
            || in_array($role, ['admin', 'super_admin', 'super admin'])
            || in_array($userType, ['admin', 'super_admin', 'super admin']);

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to security center.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(30)->get();
        $totalUsers = User::count();
        $supremeAdminsCount = User::where('is_supreme_admin', true)->orWhere('is_super_admin', true)->count();
        $twoFactorEnabledCount = User::whereNotNull('two_factor_secret')->count();

        return view('content.apps.system.security-center', compact(
            'recentAuditLogs',
            'totalUsers',
            'supremeAdminsCount',
            'twoFactorEnabledCount'
        ));
    }
}
