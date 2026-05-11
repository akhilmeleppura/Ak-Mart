<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $title = $request->title;
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.'
            ], 400);
        }

        $prompt = "You are an expert e-commerce copywriter and SEO specialist. Generate product content for a product titled: \"$title\".\n";
        $prompt .= "You MUST return your response strictly as a valid JSON object (without Markdown wrappers like ```json) with the following exact keys:\n";
        $prompt .= "- \"description\": A 2-paragraph compelling product description.\n";
        $prompt .= "- \"meta_title\": An SEO-optimized title (under 60 characters).\n";
        $prompt .= "- \"meta_description\": An SEO-optimized description (under 160 characters).";

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
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
                    $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Clean up potential markdown formatting from the response
                    $generatedText = trim(str_replace(['```json', '```'], '', $generatedText));
                    
                    $data = json_decode($generatedText, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($data['description'])) {
                        return response()->json([
                            'success' => true,
                            'data' => $data
                        ]);
                    }
                }
                
                Log::error('Gemini AI Parse Error: ' . json_encode($result));
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse AI response. Ensure the API key is valid.'
                ], 500);
            }

            Log::error('Gemini API Error: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'AI Service error. Please check your API key or try again later.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('AIGenerator Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
