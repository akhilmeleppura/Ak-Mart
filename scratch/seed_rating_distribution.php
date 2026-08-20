<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Review;
use App\Models\User;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

$sampleReviews = [
    ['rating' => 5, 'title' => 'Exceptional Quality!', 'comment' => 'Fresh, aromatic and delivered right on time. Will definitely buy regularly!'],
    ['rating' => 5, 'title' => 'Best in Class', 'comment' => 'Very high quality packaging and authentic taste. Family loved it.'],
    ['rating' => 4, 'title' => 'Very Good Value', 'comment' => 'Great product quality for the price. Delivered in 25 minutes.'],
    ['rating' => 5, 'title' => 'Super Fresh', 'comment' => 'Crisp, organic and super fresh. 5 stars!'],
    ['rating' => 4, 'title' => 'Satisfied Purchase', 'comment' => 'Good grocery essential. Consistent quality.'],
    ['rating' => 3, 'title' => 'Decent Item', 'comment' => 'Good product, delivery was quick.'],
];

$products = Product::take(10)->get();
foreach ($products as $product) {
    // Add 3-5 reviews for top products if they have less than 3
    if ($product->reviews()->count() < 3) {
        foreach (array_slice($sampleReviews, 0, rand(3, 5)) as $rev) {
            Review::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'rating' => $rev['rating'],
                'title' => $rev['title'],
                'comment' => $rev['comment'],
                'status' => 'Published',
                'is_verified_purchase' => true,
            ]);
        }
    }
    // Update rating_cache
    $avg = $product->reviews()->avg('rating') ?: 5.0;
    $product->rating_cache = round($avg, 1);
    $product->save();
}

echo "Reviews and ratings populated successfully.\n";
