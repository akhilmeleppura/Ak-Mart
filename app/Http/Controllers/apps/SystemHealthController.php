<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SystemHealthService;

class SystemHealthController extends Controller
{
    public function index(SystemHealthService $healthService)
    {
        $diagnostics = $healthService->runDiagnostics();
        return view('content.apps.system.health', compact('diagnostics'));
    }
}
