<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use App\Models\DeliverySlot;
use App\Models\StoreCredit;
use App\Models\LoyaltyTransaction;

echo "Has delivery_slots table: " . (Schema::hasTable('delivery_slots') ? 'YES' : 'NO') . "\n";
echo "DeliverySlot count: " . DeliverySlot::count() . "\n";

foreach (DeliverySlot::all() as $ds) {
    echo "- ID: {$ds->id}, Name: {$ds->name}, Time: {$ds->start_time} - {$ds->end_time}, Active: {$ds->is_active}\n";
}

echo "Has store_credits table: " . (Schema::hasTable('store_credits') ? 'YES' : 'NO') . "\n";
