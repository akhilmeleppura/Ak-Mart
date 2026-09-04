<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Log;

class SendOrderNotificationListener
{
    public function __construct(
        protected CommunicationService $communicationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $order = $event->order ?? null;
        if (!$order) {
            return;
        }

        $eventType = class_basename($event);
        Log::info("SendOrderNotificationListener handled event: {$eventType} for Order #{$order->order_number}");

        $user = $order->user;
        $recipientEmail = $user?->email;
        $recipientPhone = $user?->phone;

        $trackingUrl = route('storefront.track', ['order_number' => $order->order_number]);
        $variables = [
            'customer_name' => $user?->name ?? 'Valued Customer',
            'order_number'  => $order->order_number,
            'order_total'   => number_format($order->total_amount, 2),
            'tracking_url'  => $trackingUrl,
            'store_name'    => config('app.name', 'AK-Mart'),
        ];

        if ($recipientEmail) {
            $this->communicationService->send('email', $recipientEmail, 'order_confirmation', $variables);
        }

        if ($recipientPhone) {
            $this->communicationService->send('whatsapp', $recipientPhone, 'order_confirmation', $variables);
        }
    }
}
