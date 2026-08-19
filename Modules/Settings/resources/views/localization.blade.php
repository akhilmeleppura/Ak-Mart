@extends('layouts/layoutMaster')

@section('title', __('Localization & RTL Settings') . ' — AK-Mart')

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
            <i class="bx bx-globe text-success fs-4"></i>
            <span>{{ __('System Localization, Languages & RTL') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure system default language, right-to-left layout for Arabic, and regional calendar preferences.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'localization') }}">
          @csrf

          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-world text-primary"></i>
            <span>{{ __('Language & Direction Preferences') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default System Language') }}</label>
              <select name="default_locale" class="form-select">
                <option value="en" {{ ($settings['default_locale'] ?? 'en') === 'en' ? 'selected' : '' }}>English (US / Global)</option>
                <option value="ml" {{ ($settings['default_locale'] ?? '') === 'ml' ? 'selected' : '' }}>Malayalam (മലയാളം)</option>
                <option value="hi" {{ ($settings['default_locale'] ?? '') === 'hi' ? 'selected' : '' }}>Hindi (हिन्दी)</option>
                <option value="ar" {{ ($settings['default_locale'] ?? '') === 'ar' ? 'selected' : '' }}>Arabic (العربية - RTL)</option>
                <option value="fr" {{ ($settings['default_locale'] ?? '') === 'fr' ? 'selected' : '' }}>French (Français)</option>
                <option value="de" {{ ($settings['default_locale'] ?? '') === 'de' ? 'selected' : '' }}>German (Deutsch)</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('First Day of the Week') }}</label>
              <select name="first_day_of_week" class="form-select">
                <option value="monday" {{ ($settings['first_day_of_week'] ?? 'monday') === 'monday' ? 'selected' : '' }}>{{ __('Monday (Standard ISO)') }}</option>
                <option value="sunday" {{ ($settings['first_day_of_week'] ?? '') === 'sunday' ? 'selected' : '' }}>{{ __('Sunday') }}</option>
                <option value="saturday" {{ ($settings['first_day_of_week'] ?? '') === 'saturday' ? 'selected' : '' }}>{{ __('Saturday (Middle East)') }}</option>
              </select>
            </div>

            <div class="col-12">
              <div class="p-3 bg-label-info rounded">
                <i class="bx bx-info-circle me-1"></i>
                {{ __('AK-Mart automatically supports dynamic on-the-fly language switching via the navbar language switcher with instant RTL adaptation for Arabic.') }}
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Localization Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
