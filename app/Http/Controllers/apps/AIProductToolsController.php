<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AIProductToolsController extends Controller
{
    /**
     * Generate AI Content for product (Description, SEO, Tags, Bullet Points).
     */
    public function generateContent(Request $request)
    {
        $title = $request->input('title') ?: $request->input('name', '');
        $category = $request->input('category', '');
        $brand = $request->input('brand', '');
        $type = $request->input('type', 'all'); // description, seo, bullet_points, tags, all

        if (empty($title)) {
            return response()->json(['success' => false, 'message' => 'Product title is required.'], 422);
        }

        $apiKey = StoreSetting::get('gemini_api_key') ?: env('GEMINI_API_KEY');
        $aiMode = StoreSetting::get('ai_mode', 'gemini');

        // Deterministic Fallback & Fast Assistant Engine
        $generated = [
            'description'       => "Experience exceptional performance and refined design with the all-new {$title}. Built with precision engineering, premium materials, and cutting-edge features to elevate your everyday experience.",
            'short_description' => "Premium {$title} featuring high-grade durability, sleek modern aesthetics, and superior performance.",
            'bullet_points'     => [
                "✓ Premium build quality engineered for long-lasting durability",
                "✓ Designed for seamless usability and maximum efficiency",
                "✓ Full manufacturer warranty with 24/7 dedicated support",
                "✓ Fast shipping & hassle-free 30-day return policy",
            ],
            'meta_title'        => "{$title} — Buy Online at Best Price | AK-Mart",
            'meta_description'  => "Shop {$title} online with exclusive discounts, guaranteed authentic quality, and fast home delivery from AK-Mart.",
            'tags'              => array_values(array_filter([
                Str::slug($title),
                $brand ? Str::slug($brand) : null,
                $category ? Str::slug($category) : null,
                'bestseller',
                'ecommerce',
                'new-arrival'
            ])),
            'alt_text'          => "{$title} product showcase image - AK-Mart Store",
        ];

        // Live Gemini API call if key is present
        if ($apiKey && $aiMode !== 'manual') {
            try {
                $prompt = "You are a professional e-commerce copywriter. Generate structured product content for:\n"
                    . "Title: {$title}\nCategory: {$category}\nBrand: {$brand}\n\n"
                    . "Return ONLY valid JSON with the exact keys: description, short_description, bullet_points (array of strings), meta_title, meta_description, tags (array of strings), alt_text.";

                $res = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 1024]
                ]);

                if ($res->successful()) {
                    $text = $res->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $cleanJson = trim(preg_replace('/```json|```/', '', $text));
                    $decoded = json_decode($cleanJson, true);
                    if ($decoded && isset($decoded['description'])) {
                        $generated = array_merge($generated, $decoded);
                    }
                }
            } catch (\Exception $e) {
                // Fallback to deterministic engine smoothly
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $generated,
            'mode'    => $apiKey ? 'generative_ai' : 'deterministic_engine'
        ]);
    }

    /**
     * AI Product Optimizer: Calculates score (0-100) and returns optimization suggestions.
     */
    public function optimizeProduct(Request $request)
    {
        $title = $request->input('title', '');
        $description = $request->input('description', '');
        $price = (float) $request->input('price', 0);
        $metaTitle = $request->input('meta_title', '');
        $metaDesc = $request->input('meta_description', '');
        $image = $request->input('image', '');
        $sku = $request->input('sku', '');
        $categoryId = $request->input('category_id', '');

        $score = 100;
        $issues = [];
        $suggestions = [];

        if (empty($title) || strlen($title) < 10) {
            $score -= 20;
            $issues[] = 'Title is too brief (recommend at least 10 characters)';
            $suggestions[] = 'Add brand and key specifications to the product title';
        }
        if (empty($description) || strlen(strip_tags($description)) < 50) {
            $score -= 20;
            $issues[] = 'Description is too short or missing';
            $suggestions[] = 'Use the AI Content Generator to create a comprehensive 3-paragraph product description';
        }
        if ($price <= 0) {
            $score -= 25;
            $issues[] = 'Price must be greater than zero';
            $suggestions[] = 'Specify standard retail price';
        }
        if (empty($metaTitle) || empty($metaDesc)) {
            $score -= 15;
            $issues[] = 'Missing SEO Meta Title and Meta Description';
            $suggestions[] = 'Generate SEO tags with target keywords to improve search engine rankings';
        }
        if (empty($image)) {
            $score -= 10;
            $issues[] = 'No high-resolution product image uploaded';
            $suggestions[] = 'Upload clear product images to increase conversion rates';
        }
        if (empty($sku)) {
            $score -= 10;
            $issues[] = 'Missing SKU for inventory tracking';
            $suggestions[] = 'Assign unique SKU code';
        }

        $finalScore = max(10, min(100, $score));

        return response()->json([
            'success'     => true,
            'score'       => $finalScore,
            'rating'      => $finalScore >= 85 ? 'Excellent' : ($finalScore >= 60 ? 'Good' : 'Needs Optimization'),
            'issues'      => $issues,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Automatic Attribute Extraction from raw text specs.
     */
    public function extractAttributes(Request $request)
    {
        $text = $request->input('text', '');
        if (empty($text)) {
            return response()->json(['success' => false, 'message' => 'Specification text is required.'], 422);
        }

        $extracted = [];

        // Common patterns
        if (preg_match('/(\d+\s*GB|\d+\s*TB)\s*(RAM|Memory)/i', $text, $m)) {
            $extracted['RAM'] = trim($m[1]);
        }
        if (preg_match('/(\d+\s*GB|\d+\s*TB|\d+\s*MB)\s*(Storage|ROM|Internal|SSD|HDD)/i', $text, $m)) {
            $extracted['Storage'] = trim($m[1]);
        }
        if (preg_match('/(Black|White|Silver|Blue|Red|Green|Gold|Grey|Purple|Titanium)/i', $text, $m)) {
            $extracted['Color'] = ucfirst(strtolower($m[1]));
        }
        if (preg_match('/(\d+(\.\d+)?\s*(kg|g|lbs|oz))/i', $text, $m)) {
            $extracted['Weight'] = trim($m[1]);
        }
        if (preg_match('/(Leather|Cotton|Steel|Aluminum|Plastic|Ceramic|Titanium|Wood)/i', $text, $m)) {
            $extracted['Material'] = ucfirst(strtolower($m[1]));
        }
        if (preg_match('/(5G|4G LTE|Wi-Fi 6|Bluetooth 5\.\d)/i', $text, $m)) {
            $extracted['Connectivity'] = trim($m[1]);
        }
        if (preg_match('/(S|M|L|XL|XXL|XXXL)/', $text, $m)) {
            $extracted['Size'] = $m[1];
        }

        return response()->json([
            'success'    => true,
            'attributes' => $extracted,
        ]);
    }

    /**
     * Automatic Category Suggester.
     */
    public function suggestCategory(Request $request)
    {
        $title = strtolower($request->input('title', ''));
        $categories = Category::all();

        $matchedCategory = null;
        $highestMatch = 0;

        foreach ($categories as $cat) {
            $catName = strtolower($cat->name);
            if (str_contains($title, $catName)) {
                $matchedCategory = $cat;
                break;
            }

            // Keyword mapping
            $keywords = explode(' ', $catName);
            $matches = 0;
            foreach ($keywords as $kw) {
                if (strlen($kw) > 3 && str_contains($title, $kw)) {
                    $matches++;
                }
            }
            if ($matches > $highestMatch) {
                $highestMatch = $matches;
                $matchedCategory = $cat;
            }
        }

        return response()->json([
            'success'  => true,
            'category' => $matchedCategory ? [
                'id'   => $matchedCategory->id,
                'name' => $matchedCategory->name,
            ] : ($categories->first() ? ['id' => $categories->first()->id, 'name' => $categories->first()->name] : null),
        ]);
    }
}
