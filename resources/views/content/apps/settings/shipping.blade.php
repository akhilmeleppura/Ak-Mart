@extends('layouts/layoutMaster')

@section('title', __('Shipping & Delivery Settings') . ' — AK-Mart')

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
            <i class="bx bx-package text-warning fs-4"></i>
            <span>{{ __('Shipping, Delivery Rates & Zones') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure fulfillment rules, flat rates, free shipping thresholds, and estimated transit times.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'shipping') }}">
          @csrf

          <!-- Shipping Toggles & Rates -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-truck text-primary"></i>
            <span>{{ __('Standard Shipping Calculation') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-4">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Store Shipping Calculations') }}</h6>
                <small class="text-muted">{{ __('Calculate shipping charges dynamically during checkout.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="shipping_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="shipping_enabled" value="1" {{ ($settings['shipping_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Flat Rate Shipping Fee ($)') }}</label>
              <input type="number" step="0.01" name="shipping_flat_rate" class="form-control" value="{{ $settings['shipping_flat_rate'] ?? '5.00' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Free Shipping Order Threshold ($)') }}</label>
              <input type="number" step="0.01" name="shipping_free_threshold" class="form-control" value="{{ $settings['shipping_free_threshold'] ?? '50.00' }}" />
              <small class="text-muted">{{ __('Orders above this subtotal get free shipping.') }}</small>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Estimated Standard Delivery') }}</label>
              <input type="text" name="shipping_estimated_days" class="form-control" value="{{ $settings['shipping_estimated_days'] ?? '2-4 Business Days' }}" />
            </div>
          </div>

          <!-- Express Delivery -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-bolt-circle text-primary"></i>
            <span>{{ __('Express / Same-Day Delivery Option') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-4">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Offer Express Priority Delivery at Checkout') }}</h6>
                <small class="text-muted">{{ __('Allow customers to pay a rush surcharge for expedited dispatch.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="shipping_express_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="shipping_express_enabled" value="1" {{ ($settings['shipping_express_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Express Surcharge Amount ($)') }}</label>
              <input type="number" step="0.01" name="shipping_express_fee" class="form-control" value="{{ $settings['shipping_express_fee'] ?? '12.00' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Estimated Express Delivery') }}</label>
              <input type="text" name="shipping_express_days" class="form-control" value="{{ $settings['shipping_express_days'] ?? 'Same Day / Next Day' }}" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Shipping Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
