@extends('layouts/layoutMaster')

@section('title', __('General Operations & Feature Toggles') . ' — AK-Mart')

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
            <i class="bx bx-slider text-secondary fs-4"></i>
            <span>{{ __('General Operations & Feature Toggles') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Enable or disable store modules and global customer-facing capabilities.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'general') }}">
          @csrf

          <!-- Operational State -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-power-off text-primary"></i>
            <span>{{ __('Store Status & Maintenance Mode') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Store Maintenance Mode') }}</h6>
                <small class="text-muted">{{ __('When enabled, customers see a maintenance banner and cannot place new orders.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="maintenance_mode" value="0">
                <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item px-0 py-3">
              <label class="form-label fw-medium">{{ __('Maintenance Mode Notice Message') }}</label>
              <input type="text" name="maintenance_notice" class="form-control" value="{{ $settings['maintenance_notice'] ?? 'Our store is undergoing scheduled upgrades. We will be back online shortly.' }}" />
            </div>
          </div>

          <!-- Feature Toggles -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-check-shield text-primary"></i>
            <span>{{ __('Customer & Commerce Feature Switches') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Allow Customer Self-Registration') }}</h6>
                <small class="text-muted">{{ __('Enable new visitors to create storefront customer accounts.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="allow_customer_registration" value="0">
                <input class="form-check-input" type="checkbox" name="allow_customer_registration" value="1" {{ ($settings['allow_customer_registration'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Allow Guest Checkout') }}</h6>
                <small class="text-muted">{{ __('Permit customers to place orders without creating or logging into an account.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="allow_guest_checkout" value="0">
                <input class="form-check-input" type="checkbox" name="allow_guest_checkout" value="1" {{ ($settings['allow_guest_checkout'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Product Reviews & Ratings') }}</h6>
                <small class="text-muted">{{ __('Allow customers to submit star ratings and detailed feedback.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="enable_product_reviews" value="0">
                <input class="form-check-input" type="checkbox" name="enable_product_reviews" value="1" {{ ($settings['enable_product_reviews'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Customer Wishlist') }}</h6>
                <small class="text-muted">{{ __('Allow shoppers to save favorite products for later purchase.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="enable_wishlist" value="0">
                <input class="form-check-input" type="checkbox" name="enable_wishlist" value="1" {{ ($settings['enable_wishlist'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Coupons & Promotional Discounts') }}</h6>
                <small class="text-muted">{{ __('Permit coupon redemption at checkout and cart pages.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="enable_coupons" value="0">
                <input class="form-check-input" type="checkbox" name="enable_coupons" value="1" {{ ($settings['enable_coupons'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Returns & Refund Requests') }}</h6>
                <small class="text-muted">{{ __('Allow customers to submit return requests within the configured policy window.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="enable_returns" value="0">
                <input class="form-check-input" type="checkbox" name="enable_returns" value="1" {{ ($settings['enable_returns'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Operational Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
