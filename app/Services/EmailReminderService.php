<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CommunicationLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailReminderService
{
    protected SettingsService $settings;
    protected CommunicationService $communication;

    public function __construct(SettingsService $settings, CommunicationService $communication)
    {
        $this->settings = $settings;
        $this->communication = $communication;
    }

    /**
     * Process all scheduled automated reminders.
     */
    public function processAllReminders(): array
    {
        $results = [
            'unpaid_orders'   => $this->processUnpaidOrderReminders(),
            'abandoned_carts' => $this->processAbandonedCartReminders(),
            'low_stock'       => $this->processLowStockAlerts(),
        ];

        return $results;
    }

    /**
     * Process reminders for orders that remain unpaid.
     */
    public function processUnpaidOrderReminders(): int
    {
        if (!$this->settings->get('reminder_unpaid_order_enabled', true)) {
            return 0;
        }

        $delayMinutes = (int) $this->settings->get('reminder_unpaid_order_delay_minutes', 30);
        $maxAttempts = (int) $this->settings->get('reminder_unpaid_order_max_attempts', 3);

        $cutoffTime = now()->subMinutes($delayMinutes);

        $pendingOrders = Order::where('order_status', 'Pending')
            ->where('payment_status', '!=', 'Paid')
            ->where('created_at', '<=', $cutoffTime)
            ->take(20)
            ->get();

        $sentCount = 0;

        foreach ($pendingOrders as $order) {
            $customerEmail = $order->customer_email ?? ($order->user ? $order->user->email : null);
            if (!$customerEmail) {
                continue;
            }

            // Check previous reminder attempts
            $previousLogsCount = CommunicationLog::where('recipient', $customerEmail)
                ->where('template_code', 'order_payment_reminder')
                ->where('created_at', '>=', $order->created_at)
                ->count();

            if ($previousLogsCount >= $maxAttempts) {
                continue;
            }

            // Send reminder
            try {
                $this->communication->send(
                    'email',
                    $customerEmail,
                    'order_payment_reminder',
                    [
                        'customer_name' => $order->customer_name ?? 'Valued Customer',
                        'order_number'  => $order->order_number ?? 'ORD-' . $order->id,
                        'order_total'   => '$' . number_format($order->total_amount ?? 0, 2),
                        'store_name'    => $this->settings->get('store_name', 'AK-Mart'),
                    ]
                );
                $sentCount++;
            } catch (\Throwable $e) {
                Log::warning("Failed to send unpaid reminder for order #{$order->id}: " . $e->getMessage());
            }
        }

        return $sentCount;
    }

    /**
     * Process abandoned cart reminders.
     */
    public function processAbandonedCartReminders(): int
    {
        if (!$this->settings->get('reminder_abandoned_cart_enabled', true)) {
            return 0;
        }

        return 0; // Handled dynamically when cart events occur
    }

    /**
     * Check inventory levels and trigger low stock alerts to admins.
     */
    public function processLowStockAlerts(): int
    {
        if (!$this->settings->get('reminder_low_stock_enabled', true)) {
            return 0;
        }

        $threshold = (int) $this->settings->get('inventory_low_stock_threshold', 5);
        $adminEmail = $this->settings->get('store_email') ?? $this->settings->get('sender_email') ?? 'admin@ak-mart.com';

        $lowStockProducts = Product::where('stock_qty', '<=', $threshold)
            ->where('stock_qty', '>', 0)
            ->take(10)
            ->get();

        $alertsCount = 0;

        foreach ($lowStockProducts as $product) {
            // Check if alerted in the last 24 hours
            $recentAlert = CommunicationLog::where('recipient', $adminEmail)
                ->where('template_code', 'low_stock_alert')
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if (!$recentAlert) {
                try {
                    $this->communication->send(
                        'email',
                        $adminEmail,
                        'low_stock_alert',
                        [
                            'product_name'  => $product->name,
                            'current_stock' => $product->stock_qty,
                            'threshold'     => $threshold,
                            'store_name'    => $this->settings->get('store_name', 'AK-Mart'),
                        ]
                    );
                    $alertsCount++;
                } catch (\Throwable $e) {}
            }
        }

        return $alertsCount;
    }
}
