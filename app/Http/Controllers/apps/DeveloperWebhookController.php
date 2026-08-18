<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebhookSubscription;
use App\Models\WebhookLog;
use App\Services\WebhookDispatcher;
use Illuminate\Support\Str;

class DeveloperWebhookController extends Controller
{
    public function index()
    {
        $subscriptions = WebhookSubscription::withCount('logs')->latest()->get();
        $recentLogs = WebhookLog::with('subscription')->latest()->take(20)->get();

        return view('content.apps.developer.webhooks', compact('subscriptions', 'recentLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'target_url' => 'required|url',
            'events'     => 'required|array|min:1',
        ]);

        WebhookSubscription::create([
            'name'       => $request->name,
            'target_url' => $request->target_url,
            'secret'     => 'whsec_' . Str::random(32),
            'events'     => $request->events,
            'is_active'  => true,
        ]);

        return redirect()->route('app-developer-webhooks')->with('success', 'Webhook endpoint subscribed successfully!');
    }

    public function testPing(Request $request, WebhookSubscription $subscription, WebhookDispatcher $dispatcher)
    {
        $payload = [
            'test'      => true,
            'message'   => 'Ping test payload from AK-Mart Developer Hub',
            'timestamp' => now()->toIso8601String(),
        ];

        $dispatcher->dispatch('ping.test', $payload);

        return back()->with('success', 'Test ping event dispatched!');
    }

    public function toggle(WebhookSubscription $subscription)
    {
        $subscription->update(['is_active' => !$subscription->is_active]);
        return back()->with('success', 'Webhook status toggled!');
    }
}
