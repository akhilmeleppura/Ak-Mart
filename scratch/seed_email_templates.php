<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmailTemplate;

echo "=== Seeding Production Email Notification Templates ===\n\n";

$templates = [
    [
        'key'       => 'order_confirmed',
        'name'      => 'Order Confirmation Receipt',
        'subject'   => 'Your AK-Mart Order #{{order_number}} is Confirmed! 🛒',
        'body'      => "Hello {{customer_name}},\n\nThank you for shopping with AK-Mart! We have received your order #{{order_number}} for a total of \${{order_total}}.\n\nOur team is currently preparing your fresh items for dispatch.\n\nTrack your order status: {{tracking_url}}\n\nWarm regards,\n{{store_name}} Team",
        'variables' => ['customer_name', 'order_number', 'order_total', 'tracking_url', 'store_name'],
        'is_active' => true,
    ],
    [
        'key'       => 'order_shipped',
        'name'      => 'Order Out For Delivery',
        'subject'   => 'Your Order #{{order_number}} is Out for Delivery! 🚚',
        'body'      => "Hello {{customer_name}},\n\nGreat news! Your package is with our delivery executive and will arrive in approximately 30 minutes.\n\nLive tracking: {{tracking_url}}\n\nThank you for choosing {{store_name}}.",
        'variables' => ['customer_name', 'order_number', 'tracking_url', 'store_name'],
        'is_active' => true,
    ],
    [
        'key'       => 'welcome',
        'name'      => 'Customer Welcome & Perks',
        'subject'   => 'Welcome to AK-Mart — Enjoy 10% off your first order! 🎉',
        'body'      => "Hi {{customer_name}},\n\nWelcome to AK-Mart Supermarket! Use coupon code FIRST10 at checkout to claim 10% off your first grocery order.\n\nShop now: {{store_url}}",
        'variables' => ['customer_name', 'store_url', 'store_name'],
        'is_active' => true,
    ],
    [
        'key'       => 'abandoned_cart',
        'name'      => 'Abandoned Cart Reminder',
        'subject'   => 'Did you forget your fresh groceries in your cart? 🛒',
        'body'      => "Hello {{customer_name}},\n\nYour selected pantry items are still reserved in your cart. Complete your checkout today and enjoy express 30-min doorstep delivery.\n\nResume shopping: {{cart_url}}",
        'variables' => ['customer_name', 'cart_url', 'store_name'],
        'is_active' => true,
    ],
];

foreach ($templates as $t) {
    $tpl = EmailTemplate::updateOrCreate(['key' => $t['key']], $t);
    echo " + Seeded Template: {$tpl->name} (Key: {$tpl->key})\n";
}

echo "\nTotal Email Templates in DB: " . EmailTemplate::count() . " ✓\n";
