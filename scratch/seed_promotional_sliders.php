<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CmsBanner;

echo "=== Seeding High-Converting Promotional Sliders & Posters ===\n\n";

CmsBanner::where('position', 'home_hero')->delete();

$posters = [
    [
        'title'       => 'Farm-Fresh Organic Harvest & Daily Produce',
        'subtitle'    => 'Hand-picked crisp greens, ripe orchard fruits, and certified organic vegetables delivered directly in 30 minutes.',
        'badge_text'  => '🌱 100% Organic & Farm Fresh',
        'button_text' => 'Shop Fresh Produce',
        'link_url'    => '/store/shop?category=1',
        'bg_color'    => 'linear-gradient(135deg, #065F46 0%, #059669 50%, #10B981 100%)',
        'position'    => 'home_hero',
        'is_active'   => true,
        'sort_order'  => 1,
    ],
    [
        'title'       => 'Express 30-Minute Supermarket Delivery',
        'subtitle'    => 'Stock up on essential spices, premium basmati rice, pantry staples, and cold beverages right to your doorstep.',
        'badge_text'  => '⚡ Superfast Local Dispatch',
        'button_text' => 'Order Essentials Now',
        'link_url'    => '/store/shop',
        'bg_color'    => 'linear-gradient(135deg, #1E1B4B 0%, #3730A3 50%, #4F46E5 100%)',
        'position'    => 'home_hero',
        'is_active'   => true,
        'sort_order'  => 2,
    ],
    [
        'title'       => 'Weekend Family Mega Pantry & Snack Fest',
        'subtitle'    => 'Enjoy up to 35% discount on household essentials, family snack packs, and gourmet beverages.',
        'badge_text'  => '🔥 Flat 35% Weekend Discount',
        'button_text' => 'Claim Pantry Deals',
        'link_url'    => '/store/shop?collection=deals',
        'bg_color'    => 'linear-gradient(135deg, #7C2D12 0%, #C2410C 50%, #EA580C 100%)',
        'position'    => 'home_hero',
        'is_active'   => true,
        'sort_order'  => 3,
    ],
    [
        'title'       => 'Daily Morning Dairy, Farm Eggs & Bakery',
        'subtitle'    => 'Pure whole milk, artisan butter, farm eggs, and fresh oven-baked bread ready for your family breakfast.',
        'badge_text'  => '🥛 Pure Morning Freshness',
        'button_text' => 'Explore Dairy Aisle',
        'link_url'    => '/store/shop?category=3',
        'bg_color'    => 'linear-gradient(135deg, #0F172A 0%, #0369A1 50%, #0284C7 100%)',
        'position'    => 'home_hero',
        'is_active'   => true,
        'sort_order'  => 4,
    ],
];

foreach ($posters as $poster) {
    $created = CmsBanner::create($poster);
    echo " + Created Slide #{$created->sort_order}: '{$created->title}'\n";
}

echo "\nTotal Active Sliders in Database: " . CmsBanner::where('position', 'home_hero')->count() . " ✓\n";
