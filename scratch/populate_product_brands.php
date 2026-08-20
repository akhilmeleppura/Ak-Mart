<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$brandMap = [
    'Rice' => 'Royal Heritage',
    'Basmati' => 'Royal Heritage',
    'Apple' => 'Organic Valley',
    'Avocado' => 'Farm Fresh',
    'Egg' => 'Farm Fresh',
    'Milk' => 'Dairy Pure',
    'Butter' => 'Dairy Pure',
    'Bread' => 'Artisan Bakers',
    'Sourdough' => 'Artisan Bakers',
    'Juice' => 'Tropicana Harvest',
    'Orange' => 'Tropicana Harvest',
    'Mineral' => 'Alpine Springs',
    'Water' => 'Alpine Springs',
    'Chips' => 'Kettle Crafters',
    'Potato' => 'Kettle Crafters',
    'Chocolate' => 'Belgian Delights',
    'Flour' => 'Royal Heritage',
    'Oil' => 'Nature Fresh',
    'Olive' => 'Nature Fresh',
    'Tomato' => 'Farm Fresh',
    'Cheese' => 'Dairy Pure',
    'Yogurt' => 'Dairy Pure',
    'Coffee' => 'Artisan Roast',
    'Tea' => 'Himalayan Brew',
    'Biscuit' => 'Britannia Gold',
    'Pasta' => 'Barilla Italy',
];

$products = Product::all();
$updated = 0;
foreach ($products as $p) {
    if (empty($p->brand) || $p->brand === 'General') {
        $assignedBrand = 'Nature Fresh';
        foreach ($brandMap as $keyword => $brand) {
            if (stripos($p->name, $keyword) !== false) {
                $assignedBrand = $brand;
                break;
            }
        }
        $p->brand = $assignedBrand;
        $p->save();
        $updated++;
    }
}

echo "Updated brands for {$updated} products.\n";
$distinctBrands = Product::whereNotNull('brand')->distinct()->pluck('brand');
echo "Active Brands: " . json_encode($distinctBrands) . "\n";
