@extends('layouts/layoutMaster')

@section('title', __('API & Webhooks Settings') . ' — AK-Mart')

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
            <i class="bx bx-code-block text-primary fs-4"></i>
            <span>{{ __('REST API & Outbound Webhooks') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Manage developer API keys, secure signature secrets, and outbound event subscriptions.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'api-webhooks') }}">
          @csrf

          <!-- API Control -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-key text-primary"></i>
            <span>{{ __('API Access & Credentials') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-4">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable REST API Endpoints') }}</h6>
                <small class="text-muted">{{ __('Allow external integrations and mobile apps to interact with AK-Mart APIs.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="api_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="api_enabled" value="1" {{ ($settings['api_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="row g-4 mb-5">
            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Store API Base URL') }}</label>
              <div class="input-group">
                <input type="text" class="form-control bg-light" readonly value="{{ url('api/v1') }}" />
                <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ url('api/v1') }}')">
                  <i class="bx bx-copy me-1"></i>{{ __('Copy') }}
                </button>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Primary API Key') }}</label>
              <input type="text" name="api_primary_key" class="form-control font-monospace" value="{{ $settings['api_primary_key'] ?? 'akm_live_sec_' . substr(md5(url('/')), 0, 16) }}" readonly />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Webhook Signing Secret (Encrypted)') }}</label>
              <input type="password" name="webhook_secret" class="form-control" placeholder="whsec_••••••••••••••••" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save API Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
