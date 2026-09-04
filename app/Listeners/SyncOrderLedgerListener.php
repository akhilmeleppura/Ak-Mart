<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Events\OrderCancelled;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Log;

class SyncOrderLedgerListener
{
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
        Log::info("SyncOrderLedgerListener handled event: {$eventType} for Order #{$order->order_number}");

        if ($event instanceof OrderPaid) {
            AuditLogService::log(
                'order.paid',
                'Order',
                $order->id,
                ['payment_status' => 'pending'],
                ['payment_status' => 'paid', 'total_amount' => $order->total_amount],
                $order->user_id,
                'customer'
            );
        } elseif ($event instanceof OrderCancelled) {
            AuditLogService::log(
                'order.cancelled',
                'Order',
                $order->id,
                ['order_status' => 'processing'],
                ['order_status' => 'cancelled', 'reason' => $order->cancellation_reason_code],
                $order->user_id,
                'staff'
            );
        }
    }
}
