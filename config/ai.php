<?php

return [
    // General AI Settings
    'enabled' => env('AI_ENABLED', false),
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'gemini'), // gemini, openai, claude
    'assistant_name' => env('AI_ASSISTANT_NAME', 'AkMart Assistant'),
    'assistant_prompt' => env('AI_ASSISTANT_PROMPT', 'You are a helpful e-commerce assistant.'),

    // Gemini Configuration
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 2048),
    ],

    // OpenAI Configuration
    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 4096),
    ],

    // Claude Configuration
    'claude' => [
        'api_key' => env('CLAUDE_API_KEY', ''),
        'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
    ],

    // AI Features toggles
    'features' => [
        'product_description_generator' => env('AI_FEATURE_PRODUCT_DESC', true),
        'seo_generator' => env('AI_FEATURE_SEO', true),
        'blog_generator' => env('AI_FEATURE_BLOG', true),
        'customer_support' => env('AI_FEATURE_SUPPORT', true),
        'dashboard_assistant' => env('AI_FEATURE_DASHBOARD', true),
        'sales_analysis' => env('AI_FEATURE_SALES', true),
        'inventory_forecast' => env('AI_FEATURE_INVENTORY', true),
    ],
];
