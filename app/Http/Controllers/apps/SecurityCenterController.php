<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SecurityCenterController extends Controller
{
    public function index()
    {
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
