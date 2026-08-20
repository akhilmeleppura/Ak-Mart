<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Has price_alerts: " . (Schema::hasTable('price_alerts') ? 'YES' : 'NO') . "\n";
echo "users has referral_code: " . (Schema::hasColumn('users', 'referral_code') ? 'YES' : 'NO') . "\n";
echo "users has referred_by: " . (Schema::hasColumn('users', 'referred_by') ? 'YES' : 'NO') . "\n";
