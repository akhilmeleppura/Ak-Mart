<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiToolManager;
use App\Services\Ai\PromptSecurityGuard;
use App\Services\Ai\SemanticSearchService;
use Illuminate\Http\Request;

class StorefrontAiAssistantController extends Controller
{
    public function chat(Request $request, AiToolManager $toolManager, SemanticSearchService $searchService)
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

        // 3. Store Policy Intents
        if (str_contains($lower, 'return') || str_contains($lower, 'refund')) {
            return response()->json(['success' => true, 'reply' => $toolManager->getStorePolicy('returns')]);
        }
        if (str_contains($lower, 'shipping') || str_contains($lower, 'delivery')) {
            return response()->json(['success' => true, 'reply' => $toolManager->getStorePolicy('shipping')]);
        }
        if (str_contains($lower, 'payment') || str_contains($lower, 'cod') || str_contains($lower, 'upi')) {
            return response()->json(['success' => true, 'reply' => $toolManager->getStorePolicy('payment')]);
        }

        // 4. Product Search & Shopping Recommendations
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

        // 5. Default Helpful Assistant Fallback
        return response()->json([
            'success' => true,
            'reply'   => "👋 **Hello! I am your AKMart Shopping Assistant**.\n\nI can help you with:\n• Finding products (e.g. *'Mobile phones under \$500'*)\n• Tracking orders (e.g. *'Track order #10045'*)\n• Return, Shipping, and Payment policies\n\nHow can I help you today?",
        ]);
    }
}
