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

        return redirect()->back()->with('success', 'AI & Copilot Settings saved successfully!');
    }
}
