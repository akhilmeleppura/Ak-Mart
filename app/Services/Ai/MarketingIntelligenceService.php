<?php

namespace App\Services\Ai;

use App\Models\Product;
use Illuminate\Support\Str;

class MarketingIntelligenceService
{
    /**
     * 1. Multi-Format AI Product Content Generator
     */
    public function generateProductContent(Product $product, string $tone = 'professional', string $locale = 'en'): array
    {
        $name = $product->name;
        $brand = $product->brand ?: 'AKMart';
        $category = $product->category?->name ?? 'General';
        $price = '$' . number_format($product->price, 2);

        $tonePrefix = match ($tone) {
            'premium'     => "Experience unmatched luxury with {$name}.",
            'promotional' => "Limited Time Offer! Grab {$name} at {$price} today.",
            'friendly'    => "Meet {$name}, your new favorite companion.",
            default       => "Discover {$name} by {$brand}, engineered for superior performance.",
        };

        $shortDesc = "{$tonePrefix} Premium {$category} designed for excellence.";
        $longDesc = "<p>{$tonePrefix}</p><p>Featuring genuine {$brand} craftsmanship, {$name} offers outstanding reliability, modern design, and exceptional value at {$price}. Perfect for everyday use.</p>";

        $highlights = [
            "Authentic {$brand} guarantee",
            "Premium {$category} design",
            "Comprehensive 1-year warranty",
            "Express delivery & easy 7-day returns",
        ];

        $seoTitle = substr("{$name} | Best Price {$price} at AKMart", 0, 60);
        $metaDesc = substr("Buy authentic {$name} from {$brand} at AKMart for just {$price}. Enjoy secure checkout, fast shipping, and easy returns.", 0, 160);
        $socialCaption = "Upgrade your setup with the all-new {$name} by {$brand}! ✨ Available now at AKMart for {$price}. Shop now! 🛒 #AKMart #{$brand} #ShopOnline";
        $whatsappCopy = "Hello! 👋 The {$name} is now in stock at AKMart for {$price}. Check it out here: " . url("/store/product/{$product->slug}");
        $emailCopy = [
            'subject' => "Special Spotlight: {$name} is Now Available at AKMart!",
            'body'    => "Hi there,\n\nWe are excited to present the {$name} by {$brand}. Engineered for quality, available now at {$price}.\n\nExplore specifications and order with fast shipping today!",
            'cta'     => 'Shop Now',
        ];

        return [
            'title'             => $name,
            'short_description' => $shortDesc,
            'long_description'  => $longDesc,
            'highlights'        => $highlights,
            'seo_title'         => $seoTitle,
            'meta_description'  => $metaDesc,
            'social_caption'    => $socialCaption,
            'whatsapp_copy'     => $whatsappCopy,
            'email_copy'        => $emailCopy,
            'tone'              => $tone,
            'locale'            => $locale,
        ];
    }

    /**
     * 2. Deterministic SEO Quality Scoring (0–100)
     */
    public function scoreSeoQuality(Product $product): array
    {
        $score = 0;
        $issues = [];
        $recommendations = [];

        // Title checks (30 pts)
        if (!empty($product->name)) {
            $len = strlen($product->name);
            if ($len >= 20 && $len <= 70) {
                $score += 30;
            } else {
                $score += 15;
                $issues[] = "Title length ({$len} chars) is outside optimal 20–70 character range.";
                $recommendations[] = "Optimize title length to between 20 and 70 characters.";
            }
        } else {
            $issues[] = "Product title is missing.";
            $recommendations[] = "Add a descriptive product title.";
        }

        // Description / Meta checks (30 pts)
        if (!empty($product->description)) {
            $score += 30;
        } else {
            $issues[] = "Product description is missing.";
            $recommendations[] = "Provide a comprehensive product description.";
        }

        // Category & Brand checks (20 pts)
        if ($product->category_id) {
            $score += 10;
        } else {
            $issues[] = "Category is unassigned.";
            $recommendations[] = "Assign product to a relevant category.";
        }

        if (!empty($product->brand)) {
            $score += 10;
        } else {
            $issues[] = "Brand attribute is missing.";
            $recommendations[] = "Specify brand name for brand-affinity search.";
        }

        // SKU & Pricing checks (20 pts)
        if (!empty($product->sku) && $product->price > 0) {
            $score += 20;
        } else {
            $issues[] = "SKU or valid pricing is missing.";
            $recommendations[] = "Ensure unique SKU and non-zero price.";
        }

        return [
            'score'           => $score,
            'status'          => $score >= 80 ? 'Good' : ($score >= 50 ? 'Needs Improvement' : 'Poor'),
            'issues'          => $issues,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * 3. Structured Attribute Extraction
     */
    public function extractAttributesFromText(string $text): array
    {
        $attributes = [];

        // Display (e.g. 6.6", 14-inch, 15.6 inch)
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:-?\s*inch|"|\'\'|\s*in\b)/i', $text, $m)) {
            $attributes['display'] = "{$m[1]} inch";
        }

        // RAM (e.g. 8GB RAM, 16 GB)
        if (preg_match('/(\d+)\s*GB\s*RAM/i', $text, $m) || preg_match('/(\d+)\s*GB\s*(?:LPDDR|DDR)/i', $text, $m)) {
            $attributes['ram'] = "{$m[1]}GB";
        }

        // Storage (e.g. 128GB Storage, 256GB SSD, 1TB)
        if (preg_match('/(\d+)\s*(?:GB|TB)\s*(?:storage|SSD|ROM|NVMe)/i', $text, $m)) {
            $attributes['storage'] = strtoupper($m[0]);
        }

        // Battery (e.g. 5000mAh, 4500 mAh)
        if (preg_match('/(\d+)\s*mAh/i', $text, $m)) {
            $attributes['battery'] = "{$m[1]}mAh";
        }

        // Connectivity (e.g. 5G, Wi-Fi 6, Bluetooth 5.3)
        if (preg_match('/\b(5G|4G LTE|Wi-Fi 6|Bluetooth 5\.\d)\b/i', $text, $m)) {
            $attributes['connectivity'] = strtoupper($m[1]);
        }

        return $attributes;
    }

    /**
     * 4. Duplicate Product Detection
     */
    public function detectDuplicateProducts(Product $product): array
    {
        $duplicates = [];

        $candidates = Product::where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->sku) {
                    $q->where('sku', $product->sku);
                }
                if ($product->brand) {
                    $q->orWhere('brand', $product->brand);
                }
            })
            ->take(10)
            ->get();

        foreach ($candidates as $cand) {
            similar_text(strtolower($product->name), strtolower($cand->name), $percent);
            if ($percent >= 75 || ($product->sku && $cand->sku === $product->sku)) {
                $duplicates[] = [
                    'product_id' => $cand->id,
                    'name'       => $cand->name,
                    'sku'        => $cand->sku,
                    'similarity' => round($percent, 1) . '%',
                    'match_type' => $cand->sku === $product->sku ? 'Exact SKU Match' : 'High Title Similarity',
                ];
            }
        }

        return [
            'product_id'       => $product->id,
            'duplicates_found' => count($duplicates),
            'candidates'       => $duplicates,
        ];
    }

    /**
     * 5. Catalog Quality Health Scanner
     */
    public function scanCatalogQuality(): array
    {
        $total = Product::count();
        if ($total === 0) {
            return [
                'total_products'   => 0,
                'health_score'     => 100,
                'missing_desc'     => 0,
                'missing_sku'      => 0,
                'missing_category' => 0,
            ];
        }

        $missingDesc = Product::whereNull('description')->orWhere('description', '')->count();
        $missingSku = Product::whereNull('sku')->orWhere('sku', '')->count();
        $missingCat = Product::whereNull('category_id')->count();

        $penalty = ($missingDesc * 10) + ($missingSku * 15) + ($missingCat * 10);
        $score = max(0, min(100, round(100 - ($penalty / $total))));

        return [
            'total_products'   => $total,
            'health_score'     => $score,
            'missing_desc'     => $missingDesc,
            'missing_sku'      => $missingSku,
            'missing_category' => $missingCat,
            'status'           => $score >= 85 ? 'Healthy' : 'Needs Attention',
        ];
    }

    /**
     * 6. Multi-Channel Campaign Draft Generator
     */
    public function generateCampaignDraft(string $prompt, string $objective = 'win_back'): array
    {
        $name = "Campaign: " . Str::title(str_replace('_', ' ', $objective));

        return [
            'campaign_name'   => $name,
            'objective'       => $objective,
            'target_audience' => 'Customers Inactive for 90+ Days',
            'suggested_offer' => '15% Off Your Next Order + Free Express Delivery',
            'email' => [
                'subject' => "We Miss You! Enjoy 15% Off Your Next AKMart Order 🎁",
                'body'    => "Hello,\n\nIt's been a while since your last visit. We've added exciting new arrivals in your favorite categories!\n\nUse code **WELCOMEBACK15** at checkout for 15% off.\n\nSee you soon!",
                'cta'     => 'Claim My 15% Off',
            ],
            'whatsapp' => [
                'message' => "Hello! 👋 We miss you at AKMart. Use promo code *WELCOMEBACK15* to get 15% OFF your next order today: " . url('/store'),
                'cta'     => 'Shop Now',
            ],
            'sms' => [
                'message' => "AKMart: We miss you! Enjoy 15% OFF today with code WELCOMEBACK15 at " . url('/store'),
            ],
            'push' => [
                'title' => "Special Gift Just For You! 🎁",
                'body'  => "Get 15% off today with code WELCOMEBACK15. Tap to shop!",
                'cta'   => 'Open App',
            ],
            'status' => 'draft_pending_human_approval',
        ];
    }

    /**
     * 7. Contextual Customer Review Reply Generator
     */
    public function generateReviewReply(int $rating, string $customerComment, string $productName): array
    {
        if ($rating >= 4) {
            $draft = "Thank you so much for your wonderful {$rating}-star review of the {$productName}! We are thrilled to hear you're enjoying your purchase. We look forward to serving you again at AKMart!";
        } else {
            $draft = "Thank you for sharing your feedback regarding the {$productName}. We sincerely apologize that your experience did not meet expectations. Our support team is here to assist you with any questions or hassle-free returns.";
        }

        return [
            'rating'          => $rating,
            'product_name'    => $productName,
            'customer_comment'=> $customerComment,
            'suggested_reply' => $draft,
            'status'          => 'draft_pending_human_approval',
        ];
    }

    /**
     * 8. Google / Meta Omnichannel Feed Readiness Validator
     */
    public function validateProductFeedReadiness(Product $product): array
    {
        $valid = true;
        $errors = [];

        if (empty($product->name)) {
            $valid = false;
            $errors[] = 'Title is required for product feeds.';
        }
        if (empty($product->sku)) {
            $valid = false;
            $errors[] = 'SKU / Identifier is required for product feeds.';
        }
        if ($product->price <= 0) {
            $valid = false;
            $errors[] = 'Valid price is required for product feeds.';
        }
        if (!$product->category_id) {
            $valid = false;
            $errors[] = 'Google/Meta taxonomy requires an assigned category.';
        }

        return [
            'product_id'     => $product->id,
            'feed_ready'     => $valid,
            'errors'         => $errors,
            'feed_channel'   => 'Google Shopping & Meta Commerce',
        ];
    }
}
