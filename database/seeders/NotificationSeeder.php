<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        if ($users->isEmpty()) return;

        $notifications = [
            [
                'title' => 'Low Stock Warning',
                'body' => 'Organic Whole Farm Milk 1L is running low (3 units remaining). Reorder recommended.',
                'type' => 'stock_alert',
                'icon' => 'bx-error-circle',
            ],
            [
                'title' => 'New Order Received',
                'body' => 'Order #ORD-AK9921 from Alexander Wright for $142.50 has been placed.',
                'type' => 'new_order',
                'icon' => 'bx-cart',
            ],
            [
                'title' => 'Purchase Order Delivered',
                'body' => 'Inbound shipment for PO-2026-001 (Nestle Distribution) received at Central Warehouse.',
                'type' => 'purchase_received',
                'icon' => 'bx-package',
            ],
            [
                'title' => 'Daily Sales Target Reached',
                'body' => 'Main Flagship Branch exceeded daily sales milestone with $4,850 in revenue.',
                'type' => 'sales_milestone',
                'icon' => 'bx-trophy',
            ],
            [
                'title' => 'Payment Settled',
                'body' => 'Stripe daily payout of $3,210.40 successfully credited to business account.',
                'type' => 'payment_settled',
                'icon' => 'bx-credit-card',
            ],
        ];

        foreach ($users as $user) {
            foreach ($notifications as $n) {
                $deterministicId = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(md5("notif-user-{$user->id}-{$n['type']}"), 4));

                DB::table('notifications')->updateOrInsert(
                    [
                        'id' => $deterministicId,
                    ],
                    [
                        'type' => 'App\\Notifications\\' . Str::studly($n['type']),
                        'notifiable_type' => 'App\\Models\\User',
                        'notifiable_id' => $user->id,
                        'data' => json_encode([
                            'title' => $n['title'],
                            'message' => $n['body'],
                            'icon' => $n['icon'],
                        ]),
                        'read_at' => null,
                        'created_at' => now()->subHours(12),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
