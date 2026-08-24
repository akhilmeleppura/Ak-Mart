@extends('layouts/layoutMaster')

@section('title', 'AI & Copilot Settings - Apps')

@section('vendor-style')
@vite('resources/assets/vendor/libs/select2/select2.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/app-ecommerce-settings.js')
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<form method="POST" action="{{ route('app-ecommerce-settings-ai-save') }}">
    @csrf
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            @include('content.apps._settings-sidebar')
        </div>
        <!-- /Navigation -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="ai_general" role="tabpanel">
                    
                    <!-- AI Mode & Configuration -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">{{ __('AI Engine & Mode') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4 g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="ai_enabled">{{ __('Enable AI Assistant') }}</label>
                                    @php $aiEnabled = $settings['ai_enabled'] ?? $settings['enabled'] ?? '1'; @endphp
                                    <select id="ai_enabled" name="enabled" class="select2 form-select">
                                        <option value="1" {{ $aiEnabled == '1' ? 'selected' : '' }}>{{ __('Enabled') }}</option>
                                        <option value="0" {{ $aiEnabled == '0' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="ai_mode">{{ __('AI Engine Provider / Mode') }}</label>
                                    @php $aiMode = $settings['ai_mode'] ?? $settings['provider'] ?? 'gemini'; @endphp
                                    <select id="ai_mode" name="ai_mode" class="select2 form-select">
                                        <option value="gemini" {{ $aiMode == 'gemini' ? 'selected' : '' }}>Google Gemini Generative AI (Live)</option>
                                        <option value="manual" {{ $aiMode == 'manual' ? 'selected' : '' }}>{{ __('Manual Rule-Based Engine (Offline / No Key)') }}</option>
                                    </select>
                                    <span class="form-text text-muted small">{{ __("Choose 'Manual' if you don't have an API key.") }}</span>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label class="form-label mb-1" for="ai_all_pages">{{ __('Display Scope Across App') }}</label>
                                    @php $aiAllPages = $settings['ai_all_pages'] ?? '1'; @endphp
                                    <select id="ai_all_pages" name="ai_all_pages" class="select2 form-select">
                                        <option value="1" {{ $aiAllPages == '1' ? 'selected' : '' }}>{{ __('Show Floating Icon Across All Pages (Global)') }}</option>
                                        <option value="0" {{ $aiAllPages == '0' ? 'selected' : '' }}>{{ __('Show on Dashboard Only') }}</option>
                                    </select>
                                    <span class="form-text text-muted small">{{ __('Controls whether the floating AI Copilot button appears globally on all pages.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gemini Credentials -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">{{ __('Google Gemini API Configuration') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label mb-1" for="gemini_api_key">{{ __('Gemini API Key') }}</label>
                                    <input type="password" class="form-control" id="gemini_api_key" name="gemini_api_key" value="{{ $settings['gemini_api_key'] ?? ($settings['gemini']['api_key'] ?? '') }}" placeholder="AIzaSy..." />
                                    <span class="form-text text-muted">{{ __('Enter your Gemini API key from Google AI Studio. Leave empty to use Manual Rule-Based Engine.') }}</span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="gemini_model">{{ __('Gemini Model') }}</label>
                                    <input type="text" class="form-control" id="gemini_model" name="gemini_model" value="{{ $settings['gemini_model'] ?? ($settings['gemini']['model'] ?? 'gemini-1.5-flash') }}" placeholder="gemini-1.5-flash" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="assistant_name">{{ __('Copilot Name') }}</label>
                                    <input type="text" class="form-control" id="assistant_name" name="assistant_name" value="{{ $settings['assistant_name'] ?? 'Ak-Mart AI' }}" placeholder="Ak-Mart Copilot" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1" for="assistant_prompt">{{ __('Custom System Instructions / Prompt') }}</label>
                                    <textarea class="form-control" id="assistant_prompt" name="assistant_prompt" rows="3" placeholder="{{ __('Custom rules or persona instructions for the assistant') }}">{{ $settings['assistant_prompt'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Storefront AI Shopping Assistant Bot Settings -->
                    <div class="card mb-6 border-primary border-opacity-25 shadow-xs">
                        <div class="card-header d-flex justify-content-between align-items-center bg-primary bg-opacity-10 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="icon-base bx bx-bot fs-4 text-primary"></i>
                                <h5 class="card-title m-0 text-primary fw-bold">{{ __('Storefront AI Shopping Assistant (Customer Chatbot)') }}</h5>
                            </div>
                            <span class="badge bg-primary text-white">{{ __('Customer Facing') }}</span>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="store_ai_chatbot_enabled">{{ __('Storefront Chatbot Status') }}</label>
                                    @php $storeAiEnabled = $settings['store_ai_chatbot_enabled'] ?? '1'; @endphp
                                    <select id="store_ai_chatbot_enabled" name="store_ai_chatbot_enabled" class="select2 form-select">
                                        <option value="1" {{ $storeAiEnabled == '1' ? 'selected' : '' }}>{{ __('Active (Display Floating Chat Widget)') }}</option>
                                        <option value="0" {{ $storeAiEnabled == '0' ? 'selected' : '' }}>{{ __('Disabled (Hide Chat Widget)') }}</option>
                                    </select>
                                    <span class="form-text text-muted small">{{ __('Toggle to show or hide the AI assistant icon in the customer storefront.') }}</span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="store_ai_chatbot_name">{{ __('Bot Name in Storefront') }}</label>
                                    <input type="text" class="form-control" id="store_ai_chatbot_name" name="store_ai_chatbot_name" value="{{ $settings['store_ai_chatbot_name'] ?? 'AK-Mart Assistant' }}" placeholder="AK-Mart Assistant" />
                                    <span class="form-text text-muted small">{{ __('The name displayed in the chatbot header.') }}</span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="store_ai_chatbot_position">{{ __('Widget Screen Position') }}</label>
                                    @php $storeAiPos = $settings['store_ai_chatbot_position'] ?? 'bottom-right'; @endphp
                                    <select id="store_ai_chatbot_position" name="store_ai_chatbot_position" class="select2 form-select">
                                        <option value="bottom-right" {{ $storeAiPos == 'bottom-right' ? 'selected' : '' }}>{{ __('Bottom Right (Standard)') }}</option>
                                        <option value="bottom-left" {{ $storeAiPos == 'bottom-left' ? 'selected' : '' }}>{{ __('Bottom Left') }}</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="store_ai_chatbot_persona">{{ __('Assistant Personality Mode') }}</label>
                                    @php $storeAiPersona = $settings['store_ai_chatbot_persona'] ?? 'friendly_assistant'; @endphp
                                    <select id="store_ai_chatbot_persona" name="store_ai_chatbot_persona" class="select2 form-select">
                                        <option value="friendly_assistant" {{ $storeAiPersona == 'friendly_assistant' ? 'selected' : '' }}>{{ __('Friendly Supermarket Assistant (Balanced & Helpful)') }}</option>
                                        <option value="discount_finder" {{ $storeAiPersona == 'discount_finder' ? 'selected' : '' }}>{{ __('Bargain & Coupon Hunter (Proactive Deals)') }}</option>
                                        <option value="concise_support" {{ $storeAiPersona == 'concise_support' ? 'selected' : '' }}>{{ __('Fast & Concise Concierge') }}</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1" for="store_ai_chatbot_greeting">{{ __('Default Welcome & Greeting Message') }}</label>
                                    <textarea class="form-control" id="store_ai_chatbot_greeting" name="store_ai_chatbot_greeting" rows="2" placeholder="Greeting text when a customer opens the bot">{{ $settings['store_ai_chatbot_greeting'] ?? "👋 Hi! I am your AK-Mart Shopping Assistant. I can help you search products, track orders, find discount coupons, and check delivery options. What can I do for you?" }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1" for="store_ai_chatbot_quick_prompts">{{ __('Quick Action Prompt Chips (Comma-separated)') }}</label>
                                    <input type="text" class="form-control" id="store_ai_chatbot_quick_prompts" name="store_ai_chatbot_quick_prompts" value="{{ $settings['store_ai_chatbot_quick_prompts'] ?? 'Track Order, Available Coupons, Trending Products, Delivery Pincode' }}" placeholder="Track Order, Available Coupons, Trending Products, Delivery Pincode" />
                                    <span class="form-text text-muted small">{{ __('Clickable shortcut buttons presented to the customer above the input field.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="d-flex justify-content-end gap-4">
                <button type="reset" class="btn btn-label-secondary">{{ __('Discard') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection
