@extends('layouts/layoutMaster')

@section('title', __('Email Reminders & Scheduler') . ' — AK-Mart')

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
            <i class="bx bx-time-five text-warning fs-4"></i>
            <span>{{ __('Automated Reminders & Background Scheduler') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure automated reminder intervals, retry caps, stop conditions, and view background queue status.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'email-reminders') }}">
          @csrf

          <!-- Unpaid Order Reminder -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0 fw-bold">{{ __('Unpaid & Pending Order Reminder') }}</h6>
                <small class="text-muted">{{ __('Automatically email customers when an order is created but left unpaid.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="reminder_unpaid_order_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="reminder_unpaid_order_enabled" value="1" {{ ($settings['reminder_unpaid_order_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Initial Delay (Minutes)') }}</label>
                <input type="number" name="reminder_unpaid_order_delay_minutes" class="form-control" value="{{ $settings['reminder_unpaid_order_delay_minutes'] ?? '30' }}" />
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Follow-up Frequency (Hours)') }}</label>
                <input type="number" name="reminder_unpaid_order_cooldown_hours" class="form-control" value="{{ $settings['reminder_unpaid_order_cooldown_hours'] ?? '24' }}" />
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Maximum Reminder Attempts') }}</label>
                <input type="number" name="reminder_unpaid_order_max_attempts" class="form-control" value="{{ $settings['reminder_unpaid_order_max_attempts'] ?? '3' }}" />
              </div>
            </div>
            <div class="text-muted small mt-3">
              <i class="bx bx-check-shield text-success me-1"></i>
              {{ __('Guaranteed stop condition: Reminders immediately stop when order status changes to Paid or Cancelled.') }}
            </div>
          </div>

          <!-- Abandoned Cart Reminder -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0 fw-bold">{{ __('Abandoned Cart Recovery Reminder') }}</h6>
                <small class="text-muted">{{ __('Re-engage visitors who added products to cart but did not reach checkout.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="reminder_abandoned_cart_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="reminder_abandoned_cart_enabled" value="1" {{ ($settings['reminder_abandoned_cart_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Trigger Delay (Hours after abandonment)') }}</label>
                <input type="number" name="reminder_abandoned_cart_delay_hours" class="form-control" value="{{ $settings['reminder_abandoned_cart_delay_hours'] ?? '2' }}" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Optional Recovery Coupon Code') }}</label>
                <input type="text" name="reminder_abandoned_cart_coupon" class="form-control" value="{{ $settings['reminder_abandoned_cart_coupon'] ?? 'COMEBACK5' }}" />
              </div>
            </div>
          </div>

          <!-- Low Stock Admin Alert -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0 fw-bold">{{ __('Low Stock Administrator Warning Alert') }}</h6>
                <small class="text-muted">{{ __('Alert store manager and warehouse coordinators when stock drops below threshold.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="reminder_low_stock_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="reminder_low_stock_enabled" value="1" {{ ($settings['reminder_low_stock_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Reminder Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
