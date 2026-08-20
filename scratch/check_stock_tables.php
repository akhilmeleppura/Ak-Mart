<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Has stock_notifications table: " . (Schema::hasTable('stock_notifications') ? 'YES' : 'NO') . "\n";
echo "Has stock_alerts table: " . (Schema::hasTable('stock_alerts') ? 'YES' : 'NO') . "\n";
