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
                            <h5 class="card-title m-0">AI Engine & Mode</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4 g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="ai_enabled">Enable AI Assistant</label>
                                    @php $aiEnabled = $settings['ai_enabled'] ?? $settings['enabled'] ?? '1'; @endphp
                                    <select id="ai_enabled" name="enabled" class="select2 form-select">
                                        <option value="1" {{ $aiEnabled == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ $aiEnabled == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="ai_mode">AI Engine Provider / Mode</label>
                                    @php $aiMode = $settings['ai_mode'] ?? $settings['provider'] ?? 'gemini'; @endphp
                                    <select id="ai_mode" name="ai_mode" class="select2 form-select">
                                        <option value="gemini" {{ $aiMode == 'gemini' ? 'selected' : '' }}>Google Gemini Generative AI (Live)</option>
                                        <option value="manual" {{ $aiMode == 'manual' ? 'selected' : '' }}>Manual Rule-Based Engine (Offline / No Key)</option>
                                    </select>
                                    <span class="form-text text-muted small">Choose 'Manual' if you don't have an API key.</span>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label class="form-label mb-1" for="ai_all_pages">Display Scope Across App</label>
                                    @php $aiAllPages = $settings['ai_all_pages'] ?? '1'; @endphp
                                    <select id="ai_all_pages" name="ai_all_pages" class="select2 form-select">
                                        <option value="1" {{ $aiAllPages == '1' ? 'selected' : '' }}>Show Floating Icon Across All Pages (Global)</option>
                                        <option value="0" {{ $aiAllPages == '0' ? 'selected' : '' }}>Show on Dashboard Only</option>
                                    </select>
                                    <span class="form-text text-muted small">Controls whether the floating AI Copilot button appears globally on all pages.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gemini Credentials -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">Google Gemini API Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label mb-1" for="gemini_api_key">Gemini API Key</label>
                                    <input type="password" class="form-control" id="gemini_api_key" name="gemini_api_key" value="{{ $settings['gemini_api_key'] ?? ($settings['gemini']['api_key'] ?? '') }}" placeholder="AIzaSy..." />
                                    <span class="form-text text-muted">Enter your Gemini API key from Google AI Studio. Leave empty to use Manual Rule-Based Engine.</span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="gemini_model">Gemini Model</label>
                                    <input type="text" class="form-control" id="gemini_model" name="gemini_model" value="{{ $settings['gemini_model'] ?? ($settings['gemini']['model'] ?? 'gemini-1.5-flash') }}" placeholder="gemini-1.5-flash" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="assistant_name">Copilot Name</label>
                                    <input type="text" class="form-control" id="assistant_name" name="assistant_name" value="{{ $settings['assistant_name'] ?? 'Ak-Mart AI' }}" placeholder="Ak-Mart Copilot" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1" for="assistant_prompt">Custom System Instructions / Prompt</label>
                                    <textarea class="form-control" id="assistant_prompt" name="assistant_prompt" rows="3" placeholder="Custom rules or persona instructions for the assistant">{{ $settings['assistant_prompt'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="d-flex justify-content-end gap-4">
                <button type="reset" class="btn btn-label-secondary">Discard</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</form>
@endsection
