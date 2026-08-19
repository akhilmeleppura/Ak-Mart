<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "=== Linking Multi-Product Bundles & Recommendations ===\n\n";

$bread = Product::where('sku', 'BKY-SRD-007')->first();
$butter = Product::where('sku', 'DRY-BTR-006')->first();
$eggs = Product::where('sku', 'DRY-EGG-004')->first();

if ($bread && $butter && $eggs) {
    DB::table('product_relations')->updateOrInsert(
        ['product_id' => $bread->id, 'related_id' => $butter->id, 'type' => 'suggested'],
        ['sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]
    );
    DB::table('product_relations')->updateOrInsert(
        ['product_id' => $bread->id, 'related_id' => $eggs->id, 'type' => 'suggested'],
        ['sort_order' => 2, 'created_at' => now(), 'updated_at' => now()]
    );
    echo " + Linked Breakfast Bundle for '{$bread->name}': Bread + Butter + Eggs ✓\n";
}

$apples = Product::where('sku', 'FRT-APP-001')->first();
$juice = Product::where('sku', 'BEV-ORJ-099')->first();
if ($apples && $juice) {
    DB::table('product_relations')->updateOrInsert(
        ['product_id' => $apples->id, 'related_id' => $juice->id, 'type' => 'suggested'],
        ['sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]
    );
    echo " + Linked Fresh Fruit & Juice Bundle for '{$apples->name}' ✓\n";
}

echo "\nTotal Product Relations in Database: " . DB::table('product_relations')->count() . " ✓\n";
