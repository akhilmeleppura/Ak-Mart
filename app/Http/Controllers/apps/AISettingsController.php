<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AISettingsController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::allAsArray();
        try {
            $dbAi = DB::table('ai_settings')->first();
            if ($dbAi) {
                foreach ((array) $dbAi as $k => $v) {
                    if (!isset($settings[$k])) {
                        $settings[$k] = $v;
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore fallback
        }
        return view('content.apps.app-ai-settings', compact('settings'));
    }

    public function store(Request $request)
    {
        $aiMode = $request->input('ai_mode', 'gemini');
        $geminiKey = trim($request->input('gemini_api_key', ''));
        $enabled = $request->input('enabled', '1');
        $aiAllPages = $request->input('ai_all_pages', '1');
        $assistantName = $request->input('assistant_name', 'Ak-Mart AI');
        $assistantPrompt = $request->input('assistant_prompt', '');
        $geminiModel = $request->input('gemini_model', 'gemini-1.5-flash');

        StoreSetting::set('ai_enabled', $enabled);
        StoreSetting::set('ai_all_pages', $aiAllPages);
        StoreSetting::set('ai_mode', $aiMode);
        StoreSetting::set('gemini_api_key', $geminiKey);
        StoreSetting::set('gemini_model', $geminiModel);
        StoreSetting::set('assistant_name', $assistantName);
        StoreSetting::set('assistant_prompt', $assistantPrompt);

        try {
            DB::table('ai_settings')->updateOrInsert(
                ['id' => 1],
                [
                    'enabled'          => $enabled == '1',
                    'provider'         => $aiMode === 'manual' ? 'gemini' : $aiMode,
                    'assistant_name'   => $assistantName,
                    'assistant_prompt' => $assistantPrompt,
                    'gemini_api_key'   => $geminiKey,
                    'gemini_model'     => $geminiModel,
                    'updated_at'       => now(),
                ]
            );
        } catch (\Exception $e) {
            // ignore
        }

        // Storefront AI Shopping Assistant Bot Settings
        $storeAiEnabled = $request->input('store_ai_chatbot_enabled', '1');
        $storeAiName = $request->input('store_ai_chatbot_name', 'AK-Mart Assistant');
        $storeAiGreeting = $request->input('store_ai_chatbot_greeting', "👋 Hi! I am your AK-Mart Shopping Assistant. I can help you search products, track orders, find discount coupons, and check delivery options. What can I do for you?");
        $storeAiQuickPrompts = $request->input('store_ai_chatbot_quick_prompts', 'Track Order, Available Coupons, Trending Products, Delivery Pincode');
        $storeAiPosition = $request->input('store_ai_chatbot_position', 'bottom-right');
        $storeAiPersona = $request->input('store_ai_chatbot_persona', 'friendly_assistant');

        StoreSetting::set('store_ai_chatbot_enabled', $storeAiEnabled);
        StoreSetting::set('store_ai_chatbot_name', $storeAiName);
        StoreSetting::set('store_ai_chatbot_greeting', $storeAiGreeting);
        StoreSetting::set('store_ai_chatbot_quick_prompts', $storeAiQuickPrompts);
        StoreSetting::set('store_ai_chatbot_position', $storeAiPosition);
        StoreSetting::set('store_ai_chatbot_persona', $storeAiPersona);

        return redirect()->back()->with('success', 'AI & Copilot Settings saved successfully!');
    }
}
