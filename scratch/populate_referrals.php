<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Str;

$users = User::whereNull('referral_code')->get();
foreach ($users as $u) {
    $code = 'AK-' . strtoupper(Str::random(6));
    while (User::where('referral_code', $code)->exists()) {
        $code = 'AK-' . strtoupper(Str::random(6));
    }
    $u->referral_code = $code;
    $u->save();
}

echo "Populated referral codes for {$users->count()} users.\n";
