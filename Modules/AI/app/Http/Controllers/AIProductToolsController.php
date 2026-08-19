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
        $locale = $request->input('locale') ?: app()->getLocale() ?: 'en';

        if (empty($title)) {
            return response()->json(['success' => false, 'message' => 'Product title is required.'], 422);
        }

        $apiKey = StoreSetting::get('gemini_api_key') ?: env('GEMINI_API_KEY');
        $aiMode = StoreSetting::get('ai_mode', 'gemini');

        // Deterministic Multilingual Fallback Engine
        if ($locale === 'ml') {
            $generated = [
                'description'       => "പുതിയ {$title} മികച്ച ഗുണമേന്മയോടും അത്യാധുനിക സവിശേഷതകളോടും കൂടി നിങ്ങളുടെ ദൈനംദിന ആവശ്യങ്ങൾ നിറവേറ്റുന്നു. ദീർഘകാല ഈടുനിൽപ്പും പ്രീമിയം ഡിസൈനും ഇതിന്റെ പ്രത്യേകതയാണ്.",
                'short_description' => "പ്രീമിയം ഗുണനിലവാരമുള്ള {$title}, മികച്ച പ്രകടനവും ഈടുനിൽപ്പും നൽകുന്നു.",
                'bullet_points'     => [
                    "✓ ഉയർന്ന നിലവാരമുള്ളതും ദീർഘകാലം ഈടുനിൽക്കുന്നതുമായ നിർമ്മാണം",
                    "✓ എളുപ്പമുള്ള ഉപയോഗവും ഉയർന്ന കാര്യക്ഷമതയും",
                    "✓ കമ്പനി വാറന്റിയും 24/7 സപ്പോർട്ടും",
                    "✓ വേഗത്തിലുള്ള ഡെലിവറിയും 30 ദിവസത്തെ റിട്ടേൺ പോളിസിയും",
                ],
                'meta_title'        => "{$title} — മികച്ച വിലയിൽ ഓൺലൈനായി വാങ്ങൂ | AK-Mart",
                'meta_description'  => "{$title} മികച്ച ഓഫറുകളോടും ഫാസ്റ്റ് ഡെലിവറിയോടും കൂടി AK-Mart-ൽ നിന്ന് ഓൺലൈനായി വാങ്ങൂ.",
                'tags'              => array_values(array_filter([
                    Str::slug($title),
                    $brand ? Str::slug($brand) : null,
                    $category ? Str::slug($category) : null,
                    'bestseller',
                    'ecommerce',
                    'new-arrival'
                ])),
                'alt_text'          => "{$title} ഉൽപ്പന്ന ചിത്രം - AK-Mart",
            ];
        } elseif ($locale === 'hi') {
            $generated = [
                'description'       => "नया {$title} अपनी उत्कृष्ट गुणवत्ता और आधुनिक डिज़ाइन के साथ आपके अनुभव को बेहतर बनाता है। यह लंबे समय तक चलने वाला और विश्वसनीय है।",
                'short_description' => "प्रीमियम {$title} उच्च गुणवत्ता और आधुनिक डिज़ाइन के साथ।",
                'bullet_points'     => [
                    "✓ लंबे समय तक चलने वाली टिकाऊ प्रीमियम बनावट",
                    "✓ आसान उपयोग और अधिकतम दक्षता",
                    "✓ निर्माता वारंटी और 24/7 ग्राहक सहायता",
                    "✓ तेज़ डिलीवरी और 30 दिनों की आसान वापसी नीति",
                ],
                'meta_title'        => "{$title} — सर्वोत्तम मूल्य पर ऑनलाइन खरीदें | AK-Mart",
                'meta_description'  => "{$title} विशेष छूट और तेज़ होम डिलीवरी के साथ AK-Mart पर ऑनलाइन खरीदें।",
                'tags'              => array_values(array_filter([
                    Str::slug($title),
                    $brand ? Str::slug($brand) : null,
                    $category ? Str::slug($category) : null,
                    'bestseller',
                    'ecommerce',
                    'new-arrival'
                ])),
                'alt_text'          => "{$title} उत्पाद चित्र - AK-Mart",
            ];
        } elseif ($locale === 'ar') {
            $generated = [
                'description'       => "تم تصميم {$title} الجديد بدقة عالية وجودة فائقة ليوفر لك تجربة استثنائية وأداءً يدوم طويلاً.",
                'short_description' => "{$title} الفاخر بتصميم عصري وأداء متميز ومتانة تدوم طويلاً.",
                'bullet_points'     => [
                    "✓ جودة تصنيع ممتازة مصممة للاستخدام الطويل",
                    "✓ سهولة في الاستخدام وكفاءة فائقة",
                    "✓ ضمان شامل من الشركة المصنعة مع دعم على مدار الساعة",
                    "✓ شحن سريع وسياسة إرجاع سهلة خلال 30 يوماً",
                ],
                'meta_title'        => "{$title} — اشترِ الآن بأفضل سعر أونلاين | AK-Mart",
                'meta_description'  => "تسوق {$title} عبر الإنترنت مع خصومات حصرية وجودة مضمونة وتوصيل سريع من AK-Mart.",
                'tags'              => array_values(array_filter([
                    Str::slug($title),
                    $brand ? Str::slug($brand) : null,
                    $category ? Str::slug($category) : null,
                    'bestseller',
                    'ecommerce',
                    'new-arrival'
                ])),
                'alt_text'          => "صورة منتج {$title} - متجر AK-Mart",
            ];
        } elseif ($locale === 'fr') {
            $generated = [
                'description'       => "Découvrez des performances exceptionnelles et un design raffiné avec le tout nouveau {$title}. Conçu avec précision pour sublimer votre quotidien.",
                'short_description' => "{$title} haut de gamme alliant durabilité, esthétique moderne et performances supérieures.",
                'bullet_points'     => [
                    "✓ Qualité de fabrication supérieure pour une durabilité maximale",
                    "✓ Conçu pour une utilisation intuitive et une grande efficacité",
                    "✓ Garantie complète du fabricant avec assistance dédiée 24/7",
                    "✓ Livraison rapide et politique de retour sous 30 jours",
                ],
                'meta_title'        => "{$title} — Acheter en ligne au meilleur prix | AK-Mart",
                'meta_description'  => "Achetez {$title} en ligne avec des réductions exclusives et une livraison rapide chez AK-Mart.",
                'tags'              => array_values(array_filter([
                    Str::slug($title),
                    $brand ? Str::slug($brand) : null,
                    $category ? Str::slug($category) : null,
                    'bestseller',
                    'ecommerce',
                    'new-arrival'
                ])),
                'alt_text'          => "Photo du produit {$title} - AK-Mart",
            ];
        } elseif ($locale === 'de') {
            $generated = [
                'description'       => "Erleben Sie herausragende Leistung und edles Design mit dem neuen {$title}. Präzise gefertigt für höchste Ansprüche im Alltag.",
                'short_description' => "Hochwertiges {$title} mit modernem Design, langer Lebensdauer und überlegener Leistung.",
                'bullet_points'     => [
                    "✓ Erstklassige Verarbeitungsqualität für lange Haltbarkeit",
                    "✓ Entwickelt für nahtlose Bedienung und maximale Effizienz",
                    "✓ Volle Herstellergarantie mit 24/7-Kundendienst",
                    "✓ Schneller Versand und 30 Tage Rückgaberecht",
                ],
                'meta_title'        => "{$title} — Online zum besten Preis kaufen | AK-Mart",
                'meta_description'  => "Kaufen Sie {$title} online mit exklusiven Rabatten und schneller Lieferung bei AK-Mart.",
                'tags'              => array_values(array_filter([
                    Str::slug($title),
                    $brand ? Str::slug($brand) : null,
                    $category ? Str::slug($category) : null,
                    'bestseller',
                    'ecommerce',
                    'new-arrival'
                ])),
                'alt_text'          => "Produktbild {$title} - AK-Mart",
            ];
        } else {
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
        }

        // Live Gemini API call if key is present
        if ($apiKey && $aiMode !== 'manual') {
            try {
                $langPrompt = match($locale) {
                    'ml' => 'Write all text in Malayalam language (മലയാളത്തിൽ എഴുതുക).',
                    'hi' => 'Write all text in Hindi language (हिन्दी में लिखें).',
                    'ar' => 'Write all text in Arabic language (اكتب باللغة العربية).',
                    'fr' => 'Write all text in French language (Rédigez en français).',
                    'de' => 'Write all text in German language (Schreiben Sie auf Deutsch).',
                    default => 'Write in English.',
                };

                $prompt = "You are a professional e-commerce copywriter. {$langPrompt} Generate structured product content for:\n"
                    . "Title: {$title}\nCategory: {$category}\nBrand: {$brand}\n\n"
                    . "Return ONLY valid JSON with the exact keys: description, short_description, bullet_points (array of strings), meta_title, meta_description, tags (array of strings), alt_text.";

                $res = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
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

    /**
     * Multilingual AI Copilot Chat Endpoint.
     */
    public function copilotChat(Request $request)
    {
        $prompt = trim($request->input('prompt', ''));
        $locale = $request->input('locale') ?: app()->getLocale() ?: 'en';
        app()->setLocale($locale);

        if (empty($prompt)) {
            return response()->json(['success' => false, 'message' => 'Prompt cannot be empty.'], 422);
        }

        $apiKey = StoreSetting::get('gemini_api_key') ?: env('GEMINI_API_KEY');
        $aiMode = StoreSetting::get('ai_mode', 'gemini');
        $assistantName = StoreSetting::get('assistant_name', 'Ak-Mart AI');

        // Check if Gemini API is live
        if ($apiKey && $aiMode !== 'manual') {
            try {
                $langInstruction = match($locale) {
                    'ml' => 'Always answer in Malayalam language (മലയാളത്തിൽ മറുപടി നൽകുക).',
                    'hi' => 'Always answer in Hindi language (हिन्दी में उत्तर दें).',
                    'ar' => 'Always answer in Arabic language (يرجى الرد باللغة العربية).',
                    'fr' => 'Always answer in French language (Répondez en français).',
                    'de' => 'Always answer in German language (Antworten Sie auf Deutsch).',
                    default => 'Answer in English.',
                };

                $systemContext = "You are {$assistantName}, the AI assistant and e-commerce business advisor for AK-Mart. {$langInstruction} Provide concise, actionable, professional store management insights.";

                $res = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => "{$systemContext}\n\nUser Question: {$prompt}"]]]
                    ],
                    'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 512]
                ]);

                if ($res->successful()) {
                    $aiText = $res->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($aiText) {
                        return response()->json(['success' => true, 'response' => trim($aiText)]);
                    }
                }
            } catch (\Exception $e) {
                // Fallback to intelligent localized responder
            }
        }

        // Multilingual Deterministic E-Commerce Assistant
        $lowerPrompt = strtolower($prompt);
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        if ($locale === 'ml') {
            if (str_contains($lowerPrompt, 'stock') || str_contains($lowerPrompt, 'inventory') || str_contains($lowerPrompt, 'സ്റ്റോക്ക്')) {
                $response = "📊 **സ്റ്റോക്ക് റിപ്പോർട്ട്**: നിങ്ങളുടെ സ്റ്റോറിൽ ആകെ **{$totalProducts}** ഉൽപ്പന്നങ്ങളുണ്ട്. കുറഞ്ഞ സ്റ്റോക്കുള്ള ഉൽപ്പന്നങ്ങൾ പരിശോധിക്കാൻ **സ്റ്റോക്കും ഇൻവെന്ററിയും** സന്ദർശിക്കുക.";
            } elseif (str_contains($lowerPrompt, 'order') || str_contains($lowerPrompt, 'sales') || str_contains($lowerPrompt, 'ഓർഡർ') || str_contains($lowerPrompt, 'വിൽപ്പന')) {
                $response = "🛒 **വിൽപ്പന വിവരങ്ങൾ**: നിങ്ങളുടെ ഓർഡറുകളും ഫുൾഫിൽമെന്റും പരിശോധിക്കാൻ **ഓർഡർ പട്ടിക** അല്ലെങ്കിൽ **ഇ-കൊമേഴ്‌സ് അനലിറ്റിക്സ്** ഡാഷ്‌ബോർഡ് പരിശോധിക്കുക.";
            } elseif (str_contains($lowerPrompt, 'category') || str_contains($lowerPrompt, 'വിഭാഗം')) {
                $response = "🗂️ **വിഭാഗങ്ങൾ**: നിങ്ങളുടെ സ്റ്റോറിൽ നിലവിൽ **{$totalCategories}** വിഭാഗങ്ങളുണ്ട്. പുതിയ വിഭാഗങ്ങൾ ചേർക്കാൻ **വിഭാഗങ്ങളുടെ പട്ടിക** സന്ദർശിക്കുക.";
            } else {
                $response = "നമസ്കാരം! നിങ്ങളുടെ സ്റ്റോർ മാനേജ്മെന്റ്, ഉൽപ്പന്നങ്ങൾ, കാറ്റലോഗ്, അല്ലെങ്കിൽ ഓർഡർ വിശകലനം എന്നിവയിൽ എന്നോട് എന്ത് വേണമെങ്കിലും ചോദിക്കാം.";
            }
        } elseif ($locale === 'hi') {
            if (str_contains($lowerPrompt, 'stock') || str_contains($lowerPrompt, 'inventory')) {
                $response = "📊 **स्टॉक रिपोर्ट**: आपके स्टोर में कुल **{$totalProducts}** उत्पाद सक्रिय हैं। कम स्टॉक जांचने के लिए **स्टॉक और इन्वेंटरी** मेनू देखें।";
            } else {
                $response = "नमस्ते! मैं आपका AK-Mart AI सहायक हूँ। स्टोर प्रबंधन, उत्पाद या ऑर्डर के बारे में आप कुछ भी पूछ सकते हैं।";
            }
        } elseif ($locale === 'ar') {
            $response = "مرحباً! أنا مساعد AK-Mart الذكي. كيف يمكنني مساعدتك في إدارة متجرك الإلكتروني وتحليل المنتجات اليوم؟";
        } elseif ($locale === 'fr') {
            $response = "Bonjour ! Je suis votre assistant AK-Mart AI. Comment puis-je vous aider à gérer et optimiser votre boutique aujourd'hui ?";
        } elseif ($locale === 'de') {
            $response = "Hallo! Ich bin Ihr AK-Mart AI-Assistent. Wie kann ich Ihnen heute bei der Verwaltung und Optimierung Ihres Online-Shops helfen?";
        } else {
            if (str_contains($lowerPrompt, 'stock') || str_contains($lowerPrompt, 'inventory')) {
                $response = "📊 **Stock Report**: Your store currently tracks **{$totalProducts}** catalog products across **{$totalCategories}** categories. You can review low stock alerts in the **Inventory & Stock** module.";
            } elseif (str_contains($lowerPrompt, 'order') || str_contains($lowerPrompt, 'sales')) {
                $response = "🛒 **Sales Overview**: Recent orders and shipments are synchronized. Head over to **Order List** or **eCommerce Analytics** for real-time order breakdowns.";
            } else {
                $response = "Hello! I am **{$assistantName}**. You can ask me about product optimization, warehouse inventory, order trends, marketing campaigns, or catalog health diagnostics.";
            }
        }

        return response()->json([
            'success'  => true,
            'response' => $response,
        ]);
    }
}
