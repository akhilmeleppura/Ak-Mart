<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AISettingsController extends Controller
{
    /**
     * Show the AI settings page.
     */
    public function index()
    {
        // Load settings from config and DB (if any)
        $settings = Config::get('ai');
        $db = DB::table('ai_settings')->first();
        if ($db) {
            $settings = array_merge($settings, (array) $db);
        }
        return view('content.apps.app-ai-settings', ['settings' => $settings]);
    }

    /**
     * Store the AI settings.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'default_provider' => 'required|string|in:gemini,openai,claude',
            'assistant_name' => 'required|string',
            'assistant_prompt' => 'required|string',
            'gemini.api_key' => 'nullable|string',
            'gemini.model' => 'nullable|string',
            'gemini.temperature' => 'nullable|numeric',
            'gemini.max_tokens' => 'nullable|integer',
            'openai.api_key' => 'nullable|string',
            'openai.model' => 'nullable|string',
            'openai.temperature' => 'nullable|numeric',
            'openai.max_tokens' => 'nullable|integer',
            'claude.api_key' => 'nullable|string',
            'claude.model' => 'nullable|string',
        ]);

        // Upsert into ai_settings table
        DB::table('ai_settings')->updateOrInsert(
            ['id' => 1], // singleton row
            $validated
        );

        return redirect()->back()->with('success', 'AI settings saved successfully.');
    }

    /**
     * Optional: Test connection to the selected provider.
     */
    public function testConnection()
    {
        // Placeholder – real implementation would call the provider's API.
        return response()->json(['status' => 'ok']);
    }
}
