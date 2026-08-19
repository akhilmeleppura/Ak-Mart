@extends('layouts/layoutMaster')

@section('title', __('Email & SMTP Hub') . ' — AK-Mart')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

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

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-x-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-envelope text-primary fs-4"></i>
            <span>{{ __('Email & SMTP Server Configuration') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure transactional mail delivery, credentials, sender identity, and test live SMTP handshake.') }}</p>
        </div>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTestSmtp">
          <i class="bx bx-paper-plane me-1"></i>{{ __('Test SMTP Connection') }}
        </button>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'email') }}">
          @csrf

          <!-- Mail Driver -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-server text-primary"></i>
            <span>{{ __('Mail Protocol & Server') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Mail Driver') }}</label>
              <select name="mail_mailer" class="form-select">
                <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP (Recommended)</option>
                <option value="sendmail" {{ ($settings['mail_mailer'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                <option value="log" {{ ($settings['mail_mailer'] ?? '') === 'log' ? 'selected' : '' }}>Log (Local Debugging)</option>
              </select>
            </div>

            <div class="col-md-5">
              <label class="form-label fw-medium">{{ __('SMTP Host') }}</label>
              <input type="text" name="smtp_host" id="smtp_host_field" class="form-control" value="{{ $settings['smtp_host'] ?? 'smtp.mailtrap.io' }}" placeholder="e.g. smtp.gmail.com" />
            </div>

            <div class="col-md-3">
              <label class="form-label fw-medium">{{ __('SMTP Port') }}</label>
              <input type="number" name="smtp_port" id="smtp_port_field" class="form-control" value="{{ $settings['smtp_port'] ?? '587' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Encryption Type') }}</label>
              <select name="smtp_encryption" id="smtp_encryption_field" class="form-select">
                <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (STARTTLS)</option>
                <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                <option value="none" {{ ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('SMTP Username') }}</label>
              <input type="text" name="smtp_username" id="smtp_username_field" class="form-control" value="{{ $settings['smtp_username'] ?? '' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('SMTP Password (Encrypted)') }}</label>
              <input type="password" name="smtp_password" id="smtp_password_field" class="form-control" placeholder="••••••••••••••••" />
            </div>
          </div>

          <!-- Sender Identity -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-user-voice text-primary"></i>
            <span>{{ __('Sender Identity & Reply-To') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('From Email Address') }}</label>
              <input type="email" name="mail_from_address" id="mail_from_address_field" class="form-control" value="{{ $settings['mail_from_address'] ?? 'noreply@ak-mart.com' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('From Sender Name') }}</label>
              <input type="text" name="mail_from_name" id="mail_from_name_field" class="form-control" value="{{ $settings['mail_from_name'] ?? 'AK-Mart Store' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Reply-To Email Address') }}</label>
              <input type="email" name="mail_reply_to" class="form-control" value="{{ $settings['mail_reply_to'] ?? 'support@ak-mart.com' }}" />
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center border-top pt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalTestSmtp">
              <i class="bx bx-paper-plane me-1"></i>{{ __('Send Test Email') }}
            </button>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Email Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Test SMTP Connection -->
<div class="modal fade" id="modalTestSmtp" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bx bx-paper-plane text-primary fs-4"></i>
          <span>{{ __('Test SMTP Server Connection') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formTestSmtp" method="POST" action="{{ route('settings.email.test-smtp') }}">
        @csrf
        <div class="modal-body p-4">
          <p class="text-muted small mb-3">
            {{ __('Enter an email address to dispatch an immediate verified test message. This tests SMTP authentication, port connectivity, and SSL/TLS certificates.') }}
          </p>
          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Recipient Test Email') }} <span class="text-danger">*</span></label>
            <input type="email" name="test_email" class="form-control" value="{{ auth()->user()->email ?? 'admin@ak-mart.com' }}" required />
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary" id="btnDispatchTestEmail">
            <i class="bx bx-send me-1"></i>{{ __('Send Test Message') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
