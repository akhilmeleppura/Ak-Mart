<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\Ai\AiToolManager;
use App\Services\Ai\PromptSecurityGuard;
use App\Services\Ai\SemanticSearchService;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class StorefrontAiAssistantController extends Controller
{
    public function chat(Request $request, AiToolManager $toolManager, SemanticSearchService $searchService, ShippingService $shippingService)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = trim($request->input('message'));

        // 1. Anti-Prompt Injection & Security Guard
        $security = PromptSecurityGuard::inspect($message);
        if (!$security['safe']) {
            return response()->json([
                'success' => false,
                'reply'   => '⚠️ I cannot process this request due to safety and security policies.',
            ], 400);
        }

        $lower = strtolower($message);

        // 2. Order Tracking Intent (e.g. "Track order #10045", "Where is my order ORD-123?")
        if (preg_match('/(track|status|where is)\s+(?:order\s*)?(#?[A-Za-z0-9-]+)/i', $message, $m)) {
            $orderNum = str_replace('#', '', $m[2]);
            $orderData = $toolManager->getOrderDetails(
                $orderNum,
                auth()->id(),
                auth()->user()?->hasRole('super_admin') ?? false
            );

            if (!$orderData['found']) {
                $reply = "🔍 {$orderData['message']}";
            } else {
                $reply = "📦 **Order #{$orderData['order_number']}**\n\n"
                    . "• **Status**: {$orderData['status']}\n"
                    . "• **Payment**: {$orderData['payment_status']} ({$orderData['payment_method']})\n"
                    . "• **Total**: {$orderData['total_formatted']}\n"
                    . "• **Date**: {$orderData['created_at']}\n\n"
                    . "💡 You can also track your package in real-time at `/store/track`.";
            }

            return response()->json(['success' => true, 'reply' => $reply]);
        }

        // 3. Product Comparison Intent (e.g. "Compare iPhone and Samsung")
        if (str_contains($lower, 'compare') && str_contains($lower, 'and')) {
            $parts = preg_split('/compare\s+/i', $message);
            if (isset($parts[1])) {
                $items = preg_split('/\s+and\s+/i', $parts[1]);
                if (count($items) >= 2) {
                    $findProduct = function(string $term) {
                        $words = array_filter(explode(' ', trim($term)), fn($w) => strlen($w) >= 2);
                        if (empty($words)) return null;
                        return Product::where('is_active', true)->where(function($q) use ($words) {
                            foreach ($words as $w) {
                                $q->where(fn($sub) => $sub->where('name', 'LIKE', "%{$w}%")->orWhere('brand', 'LIKE', "%{$w}%"));
                            }
                        })->first();
                    };

                    $p1 = $findProduct($items[0]);
                    $p2 = $findProduct($items[1]);

                    if ($p1 && $p2) {
                        $comp = $searchService->compareProducts($p1, $p2);
                        $reply = "⚖️ **Product Comparison**:\n\n"
                            . "| Feature | {$comp['product_1']['name']} | {$comp['product_2']['name']} |\n"
                            . "| :--- | :--- | :--- |\n"
                            . "| **Price** | {$comp['product_1']['price']} | {$comp['product_2']['price']} |\n"
                            . "| **Stock** | {$comp['product_1']['stock_status']} | {$comp['product_2']['stock_status']} |\n"
                            . "| **Rating** | {$comp['product_1']['rating']} | {$comp['product_2']['rating']} |\n"
                            . "| **Category** | {$comp['product_1']['category']} | {$comp['product_2']['category']} |\n"
                            . "| **Warranty** | {$comp['product_1']['warranty']} | {$comp['product_2']['warranty']} |\n\n"
                            . "💡 Both items are authentic and covered by our 7-day return policy.";
                        return response()->json(['success' => true, 'reply' => $reply]);
                    }
                }
            }
        }

        // 4. Coupon Discovery Intent (e.g. "Do I have any coupon?", "discount codes")
        if (str_contains($lower, 'coupon') || str_contains($lower, 'promo code') || str_contains($lower, 'discount code')) {
            $coupons = Coupon::where('is_active', true)
                ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
                ->take(3)
                ->get();

            if ($coupons->count() > 0) {
                $lines = ["🎟️ **Available Store Coupons:**\n"];
                foreach ($coupons as $c) {
                    $val = $c->type === 'percentage' ? "{$c->value}% OFF" : "\${$c->value} OFF";
                    $min = $c->min_spend ? "(Min Spend: \${$c->min_spend})" : '';
                    $lines[] = "• **{$c->code}** — {$val} {$min}";
                }
                $lines[] = "\n💡 Apply during checkout to claim your savings!";
                return response()->json(['success' => true, 'reply' => implode("\n", $lines)]);
            }
        }

        // 5. Pincode Serviceability Check (e.g. "Delivery to 560001", "pincode 680001")
        if (preg_match('/(pincode|zipcode|delivery to|deliver to)\s*(\d{6})/i', $message, $pinMatch)) {
            $res = $shippingService->checkServiceability($pinMatch[2]);
            if ($res['serviceable']) {
                $reply = "🚚 **Delivery Available for Pincode {$res['pincode']}**:\n\n"
                    . "• **Carrier**: {$res['carrier']}\n"
                    . "• **Estimated Delivery**: {$res['estimated_days']} business days\n"
                    . "• **Cash on Delivery**: " . ($res['cod_available'] ? '✓ Available' : 'Not Available');
            } else {
                $reply = "❌ Delivery is currently unserviceable for pincode {$pinMatch[2]}.";
            }
            return response()->json(['success' => true, 'reply' => $reply]);
        }

        // 6. Store Policy Intents
        if (str_contains($lower, 'return') || str_contains($lower, 'refund')) {
            return response()->json(['success' => true, 'reply' => $toolManager->getStorePolicy('returns')]);
        }
        if (str_contains($lower, 'shipping') || str_contains($lower, 'delivery')) {
            return response()->json(['success' => true, 'reply' => $toolManager->getStorePolicy('shipping')]);
        }
        if (str_contains($lower, 'payment') || str_contains($lower, 'cod') || str_contains($lower, 'upi')) {
            return response()->json(['success' => true, 'reply' => $toolManager->getStorePolicy('payment')]);
        }

        // 7. Product Search & Shopping Recommendations
        $products = $searchService->search($message, 4);

        if ($products->count() > 0) {
            $lines = ["🛍️ **Here are the top matches I found for you:**\n"];
            foreach ($products as $p) {
                $stockStatus = $p->qty > 0 ? '✓ In Stock' : '❌ Out of Stock';
                $lines[] = "• **{$p->name}** — \${$p->price} ({$stockStatus}) [View Product](/store/product/{$p->slug})";
            }
            $lines[] = "\n💡 Let me know if you would like recommendations or budget alternatives!";
            return response()->json(['success' => true, 'reply' => implode("\n", $lines)]);
        }

        // 8. Default Helpful Assistant Fallback
        return response()->json([
            'success' => true,
            'reply'   => "👋 **Hello! I am your AKMart Shopping Assistant**.\n\nI can help you with:\n• Finding products (e.g. *'Mobile phones under \$500'*)\n• Comparing items (e.g. *'Compare iPhone and Samsung'*)\n• Tracking orders (e.g. *'Track order #10045'*)\n• Return, Shipping, and Payment policies\n\nHow can I help you today?",
        ]);
    }
}
