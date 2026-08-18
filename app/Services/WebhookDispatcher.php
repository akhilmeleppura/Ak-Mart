<?php

namespace App\Services;

use App\Models\WebhookSubscription;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    /**
     * Dispatch event payload to all matching active webhook subscriptions
     */
    public function dispatch(string $event, array $payload): int
    {
        $subscriptions = WebhookSubscription::where('is_active', true)->get();
        $dispatchedCount = 0;

        foreach ($subscriptions as $sub) {
            $events = is_array($sub->events) ? $sub->events : [];
            if (!in_array($event, $events) && !in_array('*', $events)) {
                continue;
            }

            $signature = $sub->secret ? hash_hmac('sha256', json_encode($payload), $sub->secret) : null;

            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'Content-Type'         => 'application/json',
                        'X-AKMart-Event'       => $event,
                        'X-AKMart-Signature'   => $signature,
                    ])
                    ->post($sub->target_url, [
                        'event'     => $event,
                        'timestamp' => now()->toIso8601String(),
                        'data'      => $payload,
                    ]);

                WebhookLog::create([
                    'webhook_subscription_id' => $sub->id,
                    'event'                   => $event,
                    'payload'                 => $payload,
                    'response_status'         => $response->status(),
                    'response_body'           => substr($response->body(), 0, 1000),
                    'attempts'                => 1,
                    'status'                  => $response->successful() ? 'delivered' : 'failed',
                ]);
            } catch (\Exception $e) {
                WebhookLog::create([
                    'webhook_subscription_id' => $sub->id,
                    'event'                   => $event,
                    'payload'                 => $payload,
                    'response_status'         => 500,
                    'response_body'           => $e->getMessage(),
                    'attempts'                => 1,
                    'status'                  => 'failed',
                ]);
            }

            $dispatchedCount++;
        }

        return $dispatchedCount;
    }
}
