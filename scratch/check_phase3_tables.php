<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Has product_questions: " . (Schema::hasTable('product_questions') ? 'YES' : 'NO') . "\n";
echo "Has order_returns: " . (Schema::hasTable('order_returns') ? 'YES' : 'NO') . "\n";
echo "Has returns: " . (Schema::hasTable('returns') ? 'YES' : 'NO') . "\n";
