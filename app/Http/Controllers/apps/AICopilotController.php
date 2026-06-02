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

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.'
            ], 400);
        }

        // 2. Fetch real-time branch and dashboard metrics
        $branchName = \App\Models\Branch\Branch::find(session('branch_id'))?->name ?? 'Global/HQ';
        $totalSales = \App\Models\Order::sum('total_amount');
        $totalOrders = \App\Models\Order::count();
        $lowStock = \App\Models\Product::where('qty', '<', 10)->where('qty', '>', 0)->count();
        $outOfStock = \App\Models\Product::where('qty', '<=', 0)->count();
        $recentOrders = \App\Models\Order::latest()->take(5)->get()->map(function($o) {
            return "#{$o->order_number} (${$o->total_amount}, {$o->order_status})";
        })->implode(', ');

        // 3. Construct System Context/Prompt
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

        // 4. Format chat history for Gemini API
        $formattedContents = [];
        foreach ($request->messages as $index => $msg) {
            $text = $msg['content'];
            
            // Prepend system prompt to the first user message
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
                'message' => 'AI Service encountered an error. Please try again later.'
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
