<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Str;

echo "=== Seeding Rich Storefront Supermarket Catalog Products (Safe Slugs) ===\n\n";

$newProducts = [
    // Beverages
    [
        'category_id'      => 2,
        'name'             => 'Fresh Pressed Valencia Orange Juice 1L',
        'slug'             => 'fresh-pressed-valencia-orange-juice-1l',
        'description'      => 'Never from concentrate. 100% pure squeezed sweet Valencia oranges with light natural pulp.',
        'price'            => 4.79,
        'compare_at_price' => 5.99,
        'qty'              => 40,
        'sku'              => 'BEV-ORJ-099',
        'barcode'          => '8901234567899',
        'is_featured'      => true,
        'is_trending'      => true,
        'deal_of_the_day'  => false,
    ],
    [
        'category_id'      => 2,
        'name'             => 'Sparkling Natural Alpine Mineral Water 750ml',
        'slug'             => 'sparkling-natural-alpine-mineral-water-750ml',
        'description'      => 'Naturally carbonated pristine alpine spring water with balanced minerals and crisp finish.',
        'price'            => 2.49,
        'compare_at_price' => 3.20,
        'qty'              => 80,
        'sku'              => 'BEV-WTR-010',
        'barcode'          => '8901234567810',
        'is_featured'      => false,
        'is_best_seller'   => true,
        'deal_of_the_day'  => false,
    ],
    // Snacks
    [
        'category_id'      => 5,
        'name'             => 'Himalayan Pink Salt Kettle Potato Chips 150g',
        'slug'             => 'himalayan-pink-salt-kettle-potato-chips-150g',
        'description'      => 'Thick-cut batch cooked crunchy kettle chips seasoned with pure mineral Himalayan pink rock salt.',
        'price'            => 2.99,
        'compare_at_price' => 3.99,
        'qty'              => 75,
        'sku'              => 'SNK-CHP-011',
        'barcode'          => '8901234567811',
        'is_featured'      => true,
        'is_trending'      => true,
        'deal_of_the_day'  => true,
    ],
    [
        'category_id'      => 5,
        'name'             => 'Single-Origin Belgian Dark Chocolate 72% 100g',
        'slug'             => 'single-origin-belgian-dark-chocolate-72-100g',
        'description'      => 'Artisan gourmet dark chocolate bar crafted with single-origin Trinitario cocoa beans.',
        'price'            => 3.49,
        'compare_at_price' => 4.50,
        'qty'              => 50,
        'sku'              => 'SNK-DKC-012',
        'barcode'          => '8901234567812',
        'is_featured'      => false,
        'is_best_seller'   => true,
        'deal_of_the_day'  => false,
    ],
    // Personal Care & Household
    [
        'category_id'      => 6,
        'name'             => 'Organic Tea Tree & Mint Purifying Shampoo 400ml',
        'slug'             => 'organic-tea-tree-mint-purifying-shampoo-400ml',
        'description'      => 'Sulfate-free invigorating botanical shampoo with natural Australian tea tree oil and peppermint.',
        'price'            => 8.99,
        'compare_at_price' => 11.50,
        'qty'              => 35,
        'sku'              => 'PRC-SHM-013',
        'barcode'          => '8901234567813',
        'is_featured'      => true,
        'is_trending'      => false,
        'deal_of_the_day'  => false,
    ],
    [
        'category_id'      => 7,
        'name'             => 'Eco-Friendly Plant Based Dishwashing Liquid 750ml',
        'slug'             => 'eco-friendly-plant-based-dishwashing-liquid-750ml',
        'description'      => 'Tough on grease, gentle on hands. Biodegradable citrus extract formula.',
        'price'            => 3.99,
        'compare_at_price' => 5.00,
        'qty'              => 65,
        'sku'              => 'HSH-DSH-014',
        'barcode'          => '8901234567814',
        'is_featured'      => false,
        'is_best_seller'   => true,
        'deal_of_the_day'  => false,
    ],
];

foreach ($newProducts as $data) {
    $existing = Product::where('sku', $data['sku'])->orWhere('slug', $data['slug'])->first();
    if (!$existing) {
        $product = Product::create(array_merge($data, [
            'is_active'   => true,
            'branch_id'   => 1,
            'min_stock'   => 5,
            'max_stock'   => 200,
        ]));

        StockMovement::record(
            $product->id,
            $product->qty,
            'initial_stock',
            "Initial Supermarket Catalog Launch: {$product->name}",
            null,
            1,
            Product::class,
            $product->id
        );
        echo " + Created Product: {$product->name} (SKU: {$product->sku}, Price: \${$product->price})\n";
    } else {
        $existing->update($data);
        echo " * Updated Product: {$existing->name}\n";
    }
}

echo "\nTotal Active Products in Catalog: " . Product::where('is_active', true)->count() . " ✓\n";
