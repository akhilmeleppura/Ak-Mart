@extends('layouts/layoutMaster')

@section('title', __('Notification Preferences') . ' — AK-Mart')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-profile-user') }}"><i class="icon-base bx bx-user icon-sm me-1_5"></i>{{ __('My Profile') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-account') }}"><i class="icon-base bx bx-cog icon-sm me-1_5"></i>{{ __('Account Settings') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-security') }}"><i class="icon-base bx bx-lock-alt icon-sm me-1_5"></i>{{ __('Security & Password') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('app-saas-billing') }}"><i class="icon-base bx bx-credit-card icon-sm me-1_5"></i>{{ __('Billing & Plans') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);"><i class="icon-base bx bx-bell icon-sm me-1_5"></i>{{ __('Notifications') }}</a>
          </li>
        </ul>
      </div>

      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      <div class="card mb-6 shadow-sm border-0">
        <h5 class="card-header border-bottom d-flex align-items-center gap-2">
          <i class="bx bx-bell text-primary fs-4"></i>
          <span>{{ __('User Notification Preferences') }}</span>
        </h5>
        <div class="card-body pt-5">
          <form method="POST" action="{{ route('account-settings-notifications-update') }}">
            @csrf
            <h6 class="text-heading fw-bold mb-4">{{ __('Email Notifications & Alerts') }}</h6>
            
            <div class="list-group list-group-flush mb-5">
              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                <div>
                  <h6 class="mb-0 fw-semibold">{{ __('Order & Customer Activity') }}</h6>
                  <small class="text-muted">{{ __('Receive notifications when new orders are placed or return requests are submitted.') }}</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" checked disabled />
                </div>
              </div>

              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                <div>
                  <h6 class="mb-0 fw-semibold">{{ __('Low-Stock & Inventory Alerts') }}</h6>
                  <small class="text-muted">{{ __('Receive high-priority alerts when store products fall below threshold levels.') }}</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" checked disabled />
                </div>
              </div>

              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                <div>
                  <h6 class="mb-0 fw-semibold">{{ __('Marketing & Feature Updates') }}</h6>
                  <small class="text-muted">{{ __('Opt out of promotional broadcasts, marketing emails, and weekly tips.') }}</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="marketing_opt_out" name="marketing_opt_out" value="1" {{ $user->marketing_opt_out ? 'checked' : '' }} />
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary shadow-sm">
              <i class="bx bx-check me-1"></i>{{ __('Save Notification Preferences') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
