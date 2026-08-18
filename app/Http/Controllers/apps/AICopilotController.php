<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AICopilotController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Verify access
        if (!auth()->check() || !auth()->user()->can('access_ai_assistant')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only roles with AI Assistant permission can use this feature.'
            ], 403);
        }

        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|string|in:user,model',
            'messages.*.content' => 'required|string',
        ]);

        // 2. Fetch API Key and AI Mode from StoreSetting / DB / env
        $apiKey = \App\Models\StoreSetting::get('gemini_api_key') ?: env('GEMINI_API_KEY');
        if (!$apiKey) {
            try {
                $apiKey = \Illuminate\Support\Facades\DB::table('ai_settings')->value('gemini_api_key');
            } catch (\Exception $e) {}
        }
        $aiMode = \App\Models\StoreSetting::get('ai_mode', 'gemini');

        // 3. Fetch real-time branch and dashboard metrics
        $branchName = \App\Models\Branch\Branch::find(session('branch_id'))?->name ?? 'Global/HQ';
        $totalSales = \App\Models\Order::sum('total_amount');
        $totalOrders = \App\Models\Order::count();
        $lowStock = \App\Models\Product::where('qty', '<', 10)->where('qty', '>', 0)->count();
        $outOfStock = \App\Models\Product::where('qty', '<=', 0)->count();
        $recentOrders = \App\Models\Order::latest()->take(5)->get()->map(function($o) {
            return "#{$o->order_number} (${$o->total_amount}, {$o->order_status})";
        })->implode(', ');

        $lastMessage = strtolower(end($request->messages)['content'] ?? '');

        // 4. Handle Manual / Fallback Assistant Mode when no API key is provided or manual mode is active
        if ($aiMode === 'manual' || empty($apiKey)) {
            if (str_contains($lastMessage, 'marketing') || str_contains($lastMessage, 'promo') || str_contains($lastMessage, 'discount')) {
                $reply = "💡 **Marketing Recommendations ($branchName)**:\n\n" .
                         "✓ **Flash Sale**: Launch a 15% weekend discount on top-selling items.\n" .
                         "✓ **Free Shipping**: Offer free shipping for orders over $50 to increase average order value.\n" .
                         "✓ **Customer Retention**: Send personalized email offers to repeat customers.\n\n" .
                         "*(Operating in Manual Business Engine mode. Configure a Gemini API Key in Ecom Settings -> AI Settings for generative AI responses.)*";
            } elseif (str_contains($lastMessage, 'warning') || str_contains($lastMessage, 'stock') || str_contains($lastMessage, 'inventory')) {
                $reply = "⚠ **Inventory Status Warning ($branchName)**:\n\n" .
                         "- **Low Stock Products (< 10 units)**: $lowStock\n" .
                         "- **Out of Stock Products**: $outOfStock\n\n" .
                         "💡 **Action Required**: Please review inventory under Stock Management and reorder low-stock items.\n\n" .
                         "*(Operating in Manual Business Engine mode)*";
            } elseif (str_contains($lastMessage, 'insight') || str_contains($lastMessage, 'sales') || str_contains($lastMessage, 'revenue') || str_contains($lastMessage, 'growth')) {
                $reply = "✓ **Business Performance Insights ($branchName)**:\n\n" .
                         "📈 **Total Sales**: $" . number_format($totalSales, 2) . "\n" .
                         "📦 **Total Orders**: $totalOrders\n" .
                         "🛍️ **Recent Orders**: " . ($recentOrders ?: 'No recent orders') . "\n\n" .
                         "*(Operating in Manual Business Engine mode)*";
            } else {
                $reply = "👋 **Hello! I am Ak-Mart Business Copilot ($branchName)**.\n\n" .
                         "Here is your real-time store snapshot:\n" .
                         "✓ **Total Sales**: $" . number_format($totalSales, 2) . "\n" .
                         "✓ **Total Orders**: $totalOrders\n" .
                         "⚠ **Stock Alerts**: $lowStock low stock / $outOfStock out of stock\n\n" .
                         "How can I help you manage and optimize your store today?\n\n" .
                         "*(Note: You are currently using the Offline Business Engine. Enter a Gemini API Key in Ecom Settings -> AI Settings to enable live generative AI responses.)*";
            }

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);
        }

        // 5. Construct System Context/Prompt for live Gemini API
        $systemPrompt = "You are Ak-Mart AI, an advanced eCommerce Business Copilot integrated into the admin dashboard.\n";
        $systemPrompt .= "You have access to real-time business data for the current branch ($branchName):\n";
        $systemPrompt .= "- Current Branch: $branchName\n";
        $systemPrompt .= "- Total Sales (this branch): $" . number_format($totalSales, 2) . "\n";
        $systemPrompt .= "- Total Orders (this branch): $totalOrders\n";
        $systemPrompt .= "- Low Stock Products Count: $lowStock\n";
        $systemPrompt .= "- Out of Stock Products Count: $outOfStock\n";
        $systemPrompt .= "- Recent Orders: $recentOrders\n\n";
        $systemPrompt .= "Your primary goal is to help users manage, analyze, automate, and optimize every aspect of the business. You act as a world-class COO, CFO, Marketing Manager, Inventory Manager, Data Analyst, and Customer Success Manager.\n";
        $systemPrompt .= "Be professional, business-focused, data-driven, concise, and actionable.\n";
        $systemPrompt .= "Use these symbols to structure your recommendations when appropriate:\n";
        $systemPrompt .= "✓ Insights\n";
        $systemPrompt .= "⚠ Warnings\n";
        $systemPrompt .= "📈 Opportunities\n";
        $systemPrompt .= "💡 Recommendations\n\n";
        $systemPrompt .= "Never fabricate data. If you don't know or don't have access to certain details, say so.\n";

        // 6. Format chat history for Gemini API
        $formattedContents = [];
        foreach ($request->messages as $index => $msg) {
            $text = $msg['content'];
            
            if ($index === 0 && $msg['role'] === 'user') {
                $text = "[SYSTEM INSTRUCTION: " . $systemPrompt . "]\n\nUser: " . $text;
            }

            $formattedContents[] = [
                'role' => $msg['role'],
                'parts' => [
                    ['text' => $text]
                ]
            ];
        }

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => $formattedContents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = $result['candidates'][0]['content']['parts'][0]['text'];
                    return response()->json([
                        'success' => true,
                        'reply' => $reply
                    ]);
                }
            }

            Log::error('Copilot Gemini API Error: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'AI Service encountered an error. Please check your Gemini API key or switch to Manual AI mode in AI Settings.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Copilot Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
