<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;

$coupons = Coupon::all();
echo "Coupons count: " . $coupons->count() . "\n";
foreach ($coupons as $c) {
    echo "- Code: {$c->code}, Type: {$c->type}, Value: {$c->value}, MinSpend: {$c->min_spend}, Active: {$c->is_active}\n";
}

if ($coupons->isEmpty()) {
    Coupon::create([
        'code' => 'WELCOME10',
        'type' => 'percentage',
        'value' => 10.00,
        'min_spend' => 20.00,
        'is_active' => true,
    ]);
    Coupon::create([
        'code' => 'SUPER5',
        'type' => 'fixed',
        'value' => 5.00,
        'min_spend' => 30.00,
        'is_active' => true,
    ]);
    echo "Seeded demo coupons WELCOME10 and SUPER5\n";
}
