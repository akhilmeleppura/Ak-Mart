@extends('layouts/layoutMaster')

@section('title', __('Order Settings & Workflow') . ' — AK-Mart')

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
            <i class="bx bx-receipt text-info fs-4"></i>
            <span>{{ __('Order Number Formatting & Workflow') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure order numbering conventions, automated invoicing, customer cancellation windows, and return policies.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'orders') }}">
          @csrf

          <!-- Number Formatting -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-hash text-primary"></i>
            <span>{{ __('Order Number Format & Prefixes') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Order Number Prefix') }}</label>
              <input type="text" name="order_prefix" class="form-control" value="{{ $settings['order_prefix'] ?? 'ORD-' }}" placeholder="e.g. ORD-" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Order Number Format Pattern') }}</label>
              <select name="order_number_format" class="form-select">
                <option value="prefix_sequential" {{ ($settings['order_number_format'] ?? 'prefix_sequential') === 'prefix_sequential' ? 'selected' : '' }}>ORD-1001 (Sequential Number)</option>
                <option value="prefix_date_seq" {{ ($settings['order_number_format'] ?? '') === 'prefix_date_seq' ? 'selected' : '' }}>ORD-20260819-001 (Date + Sequential)</option>
                <option value="prefix_random" {{ ($settings['order_number_format'] ?? '') === 'prefix_random' ? 'selected' : '' }}>ORD-8X92K (Alphanumeric Token)</option>
              </select>
            </div>
          </div>

          <!-- Automations -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-cog text-primary"></i>
            <span>{{ __('Order Lifecycle Automations') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Auto-Generate Printable PDF Invoice') }}</h6>
                <small class="text-muted">{{ __('Automatically create digital tax invoice as soon as an order is placed.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="auto_generate_invoice" value="0">
                <input class="form-check-input" type="checkbox" name="auto_generate_invoice" value="1" {{ ($settings['auto_generate_invoice'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Send Instant Order Confirmation Email') }}</h6>
                <small class="text-muted">{{ __('Dispatches email receipt with order breakdown to the customer.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="auto_email_order_confirmation" value="0">
                <input class="form-check-input" type="checkbox" name="auto_email_order_confirmation" value="1" {{ ($settings['auto_email_order_confirmation'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Send Instant WhatsApp Order Confirmation') }}</h6>
                <small class="text-muted">{{ __('Dispatches WhatsApp notification with live order tracking link.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="auto_whatsapp_order_confirmation" value="0">
                <input class="form-check-input" type="checkbox" name="auto_whatsapp_order_confirmation" value="1" {{ ($settings['auto_whatsapp_order_confirmation'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <!-- Windows & Limits -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-time text-primary"></i>
            <span>{{ __('Customer Self-Service Windows') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Customer Cancellation Window (Hours)') }}</label>
              <input type="number" name="order_cancellation_window_hours" class="form-control" value="{{ $settings['order_cancellation_window_hours'] ?? '2' }}" />
              <small class="text-muted">{{ __('Time limit for customer to cancel an unfulfilled order (0 to disable).') }}</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Customer Return Request Window (Days)') }}</label>
              <input type="number" name="order_return_window_days" class="form-control" value="{{ $settings['order_return_window_days'] ?? '14' }}" />
              <small class="text-muted">{{ __('Days after delivery that returns are accepted.') }}</small>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Order Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
