<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\User;

$user = User::first();
$adminUser = $user;

$qaSamples = [
    [
        'question' => 'Is this product 100% certified organic and GMO-free?',
        'answer' => 'Yes, all our grocery batches in this category are certified organic, ethically harvested, and tested for pure quality.',
    ],
    [
        'question' => 'What is the shelf life / expiry date for online deliveries?',
        'answer' => 'We dispatch fresh stock with a minimum guaranteed shelf life of 6 to 12 months from the packaging date.',
    ],
    [
        'question' => 'Does this item require special storage or refrigeration?',
        'answer' => 'Please store in a cool, dry place away from direct sunlight. Once opened, keep in an airtight container.',
    ],
];

$products = Product::take(10)->get();
foreach ($products as $p) {
    if (ProductQuestion::where('product_id', $p->id)->count() === 0) {
        foreach ($qaSamples as $qa) {
            ProductQuestion::create([
                'product_id'     => $p->id,
                'user_id'        => $user?->id,
                'question'       => $qa['question'],
                'answer'         => $qa['answer'],
                'answered_by_id' => $adminUser?->id,
                'answered_at'    => now()->subDays(rand(1, 10)),
                'is_published'   => true,
                'upvotes'        => rand(4, 25),
            ]);
        }
    }
}

echo "Seeded demo product Q&As successfully.\n";
