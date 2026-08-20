<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Brand;

$brandsCount = class_exists(Brand::class) ? Brand::count() : 0;
echo "Brand model count: " . $brandsCount . "\n";

$productBrands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand');
echo "Distinct product brands: " . json_encode($productBrands) . "\n";

$priceMin = Product::min('price');
$priceMax = Product::max('price');
echo "Price range: \${$priceMin} - \${$priceMax}\n";
