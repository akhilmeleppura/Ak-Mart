<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DunningLog;
use App\Models\TenantSubscription;
use App\Services\DunningService;

class DunningController extends Controller
{
    /**
     * Display a listing of dunning logs and past-due subscriptions.
     */
    public function index()
    {
        $pastDueSubscriptions = TenantSubscription::where('status', 'past_due')
            ->with(['branch', 'plan'])
            ->latest()
            ->get();

        $dunningLogs = DunningLog::with(['branch', 'subscription'])
            ->latest()
            ->paginate(20);

        return view('content.apps.saas.dunning', compact('pastDueSubscriptions', 'dunningLogs'));
    }

    /**
     * Manually trigger the dunning process for testing or immediate action.
     */
    public function trigger(DunningService $dunningService)
    {
        $dunningService->process();
        return redirect()->back()->with('success', 'Dunning process triggered successfully.');
    }
}
