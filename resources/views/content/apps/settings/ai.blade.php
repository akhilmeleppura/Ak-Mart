@extends('layouts/layoutMaster')

@section('title', __('AI & Copilot Settings') . ' — AK-Mart')

@section('content')
<div class="row g-6">
  <div class="col-12 col-lg-4 col-xl-3">
    @include('content.apps._settings-sidebar')
  </div>

  <div class="col-12 col-lg-8 col-xl-9">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-bot text-primary fs-4"></i>
            <span>{{ __('Google Gemini AI & Smart Copilot Configuration') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure neural language models, API credentials, and autonomous smart catalog generation tools.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'ai') }}">
          @csrf

          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable AI Copilot & Smart Automation Engine') }}</h6>
                <small class="text-muted">{{ __('Enables interactive sidebar assistant, product generator, and SEO tools.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="ai_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="ai_enabled" value="1" {{ ($settings['ai_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-cog text-primary"></i>
            <span>{{ __('Model Configuration & API Key') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('AI Provider') }}</label>
              <select name="ai_provider" class="form-select">
                <option value="gemini" {{ ($settings['ai_provider'] ?? 'gemini') === 'gemini' ? 'selected' : '' }}>Google Gemini AI</option>
                <option value="openai" {{ ($settings['ai_provider'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI GPT-4o</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Gemini Model Name') }}</label>
              <select name="gemini_model" class="form-select">
                <option value="gemini-2.5-flash" {{ ($settings['gemini_model'] ?? 'gemini-2.5-flash') === 'gemini-2.5-flash' ? 'selected' : '' }}>gemini-2.5-flash (Fast & Accurate)</option>
                <option value="gemini-1.5-pro" {{ ($settings['gemini_model'] ?? '') === 'gemini-1.5-pro' ? 'selected' : '' }}>gemini-1.5-pro (Deep Analysis)</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Google Gemini API Key (Encrypted in Database)') }}</label>
              <input type="password" name="gemini_api_key" class="form-control" placeholder="AIzaSy••••••••••••••••••••••••" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Creativity Temperature (0.0 to 1.0)') }}</label>
              <input type="number" step="0.1" min="0" max="1" name="ai_temperature" class="form-control" value="{{ $settings['ai_temperature'] ?? '0.7' }}" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Maximum Output Tokens') }}</label>
              <input type="number" name="ai_max_tokens" class="form-control" value="{{ $settings['ai_max_tokens'] ?? '2048' }}" />
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('System Persona & Prompt Instruction') }}</label>
              <textarea name="ai_system_prompt" class="form-control" rows="3">{{ $settings['ai_system_prompt'] ?? 'You are the AK-Mart AI Copilot, a senior e-commerce and retail management specialist. Assist the store manager with catalog management, pricing insights, inventory optimization, and customer retention in the user’s selected language.' }}</textarea>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save AI Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
