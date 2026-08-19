<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    /**
     * Display a listing of system audit logs.
     */
    public function index()
    {
        $logs = AuditLog::with(['user'])
            ->latest()
            ->paginate(30);

        return view('content.apps.saas.audit-logs', compact('logs'));
    }
}
