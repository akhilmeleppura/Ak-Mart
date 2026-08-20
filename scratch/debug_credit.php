<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\StoreCredit;
use App\Models\StoreCreditTransaction;

$referrer = User::first();
echo "Referrer ID: {$referrer->id}, Code: {$referrer->referral_code}\n";

$sc = StoreCredit::firstOrCreate(['user_id' => $referrer->id], ['balance' => 0]);
echo "Initial Balance: {$sc->balance}\n";

try {
    $tx = $sc->credit(10.00, 'referral', 1, "Test referral reward");
    echo "Credit successful! New balance: {$sc->balance}, Tx ID: {$tx->id}\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
