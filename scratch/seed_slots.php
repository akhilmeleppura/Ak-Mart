<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DeliverySlot;

$slots = [
    [
        'name' => 'Morning Fresh Slot (08:00 AM - 11:30 AM)',
        'start_time' => '08:00:00',
        'end_time' => '11:30:00',
        'max_orders' => 50,
        'days_available' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'is_active' => true,
    ],
    [
        'name' => 'Afternoon Express Slot (12:30 PM - 04:00 PM)',
        'start_time' => '12:30:00',
        'end_time' => '16:00:00',
        'max_orders' => 50,
        'days_available' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'is_active' => true,
    ],
    [
        'name' => 'Evening Dinner Rush (05:00 PM - 08:30 PM)',
        'start_time' => '17:00:00',
        'end_time' => '20:30:00',
        'max_orders' => 60,
        'days_available' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'is_active' => true,
    ],
    [
        'name' => 'ASAP 30-Minute Doorstep Express',
        'start_time' => '00:00:00',
        'end_time' => '23:59:59',
        'max_orders' => 100,
        'days_available' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'is_active' => true,
    ],
];

foreach ($slots as $slot) {
    DeliverySlot::updateOrCreate(['name' => $slot['name']], $slot);
}

echo "Seeded delivery slots successfully. Total: " . DeliverySlot::count() . "\n";
