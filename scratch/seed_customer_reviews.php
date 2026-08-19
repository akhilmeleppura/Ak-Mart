<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Review;
use App\Models\Product;
use App\Models\User;

echo "=== Seeding Verified Customer Reviews ===\n\n";

$products = Product::where('is_active', true)->take(5)->get();
$user = User::first();

if ($products->isNotEmpty() && $user) {
    foreach ($products as $p) {
        Review::updateOrCreate(
            ['product_id' => $p->id, 'user_id' => $user->id],
            [
                'rating'               => 5,
                'title'                => 'Superb freshness and excellent quality!',
                'comment'              => 'Arrived within 30 minutes in pristine packaging. Supermarket freshness guaranteed. Will definitely order again!',
                'status'               => 'approved',
                'is_verified_purchase' => true,
            ]
        );
        echo " + Added Verified 5-Star Review for '{$p->name}'\n";
    }
}

echo "\nTotal Approved Reviews in DB: " . Review::where('status', 'approved')->count() . " ✓\n";
