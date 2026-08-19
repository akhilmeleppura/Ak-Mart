@extends('layouts/layoutMaster')

@section('title', __('Security Center & Policies') . ' — AK-Mart')

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
            <i class="bx bx-shield-quarter text-danger fs-4"></i>
            <span>{{ __('Security Center & Access Control Policies') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure password strength policies, account lockout thresholds, session timeouts, and audit trails.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'security') }}">
          @csrf

          <!-- Password Policy -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-key text-primary"></i>
            <span>{{ __('Password Strength & Expiration Rules') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Minimum Password Length') }}</label>
              <select name="security_min_password_length" class="form-select">
                <option value="8" {{ ($settings['security_min_password_length'] ?? '8') === '8' ? 'selected' : '' }}>8 {{ __('Characters') }}</option>
                <option value="10" {{ ($settings['security_min_password_length'] ?? '') === '10' ? 'selected' : '' }}>10 {{ __('Characters') }}</option>
                <option value="12" {{ ($settings['security_min_password_length'] ?? '') === '12' ? 'selected' : '' }}>12 {{ __('Characters (High Security)') }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Password Expiration (Days)') }}</label>
              <select name="security_password_expiration_days" class="form-select">
                <option value="0" {{ ($settings['security_password_expiration_days'] ?? '0') === '0' ? 'selected' : '' }}>{{ __('Never Expire') }}</option>
                <option value="90" {{ ($settings['security_password_expiration_days'] ?? '') === '90' ? 'selected' : '' }}>90 {{ __('Days') }}</option>
                <option value="180" {{ ($settings['security_password_expiration_days'] ?? '') === '180' ? 'selected' : '' }}>180 {{ __('Days') }}</option>
              </select>
            </div>
          </div>

          <!-- Lockout & Session -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-lock-alt text-primary"></i>
            <span>{{ __('Brute Force Protection & Session Limits') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Max Failed Login Attempts') }}</label>
              <input type="number" name="security_max_login_attempts" class="form-control" value="{{ $settings['security_max_login_attempts'] ?? '5' }}" />
              <small class="text-muted">{{ __('Locks account temporarily.') }}</small>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Lockout Duration (Minutes)') }}</label>
              <input type="number" name="security_lockout_duration_minutes" class="form-control" value="{{ $settings['security_lockout_duration_minutes'] ?? '15' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Idle Session Timeout (Minutes)') }}</label>
              <input type="number" name="security_session_timeout_minutes" class="form-control" value="{{ $settings['security_session_timeout_minutes'] ?? '120' }}" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Security Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Recent Security Audit Activity -->
    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="bx bx-shield-check text-success fs-4"></i>
          <span>{{ __('Security Audit Activity') }}</span>
        </h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Event') }}</th>
              <th>{{ __('IP Address') }}</th>
              <th>{{ __('User') }}</th>
              <th>{{ __('Timestamp') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($auditLogs as $log)
            <tr>
              <td>
                <span class="badge bg-label-primary">{{ $log->event }}</span>
              </td>
              <td>
                <code>{{ $log->ip_address ?: '127.0.0.1' }}</code>
              </td>
              <td>
                <span class="fw-semibold text-heading">{{ $log->user ? $log->user->name : 'System' }}</span>
              </td>
              <td>
                <small class="text-muted">{{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}</small>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">
                {{ __('No recent security events recorded.') }}
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
