<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "=== Seeding Product Recommendations & Frequently Bought Together Bundles ===\n\n";

DB::table('product_relations')->truncate();

$products = Product::where('is_active', true)->take(10)->get();

if ($products->count() >= 3) {
    $main = $products[0];
    $item1 = $products[1];
    $item2 = $products[2];

    // Link Suggested Bundles to Main Product
    DB::table('product_relations')->insert([
        [
            'product_id' => $main->id,
            'related_id' => $item1->id,
            'type'       => 'suggested',
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'product_id' => $main->id,
            'related_id' => $item2->id,
            'type'       => 'suggested',
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    // Link Related Products
    if ($products->count() >= 4) {
        DB::table('product_relations')->insert([
            [
                'product_id' => $main->id,
                'related_id' => $products[3]->id,
                'type'       => 'related',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    echo " + Linked Suggested Bundle Items to '{$main->name}':\n";
    echo "   - '{$item1->name}'\n";
    echo "   - '{$item2->name}'\n";
}

echo "\nTotal Product Relations in DB: " . DB::table('product_relations')->count() . " ✓\n";
