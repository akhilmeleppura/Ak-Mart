@extends('layouts/layoutMaster')

@section('title', __('Payment Gateways Settings') . ' — AK-Mart')

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
            <i class="bx bx-credit-card text-success fs-4"></i>
            <span>{{ __('Payment Gateways & Merchant Processing') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure supported payment methods, API credentials, and live / sandbox environment modes.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'payments') }}">
          @csrf

          <!-- Cash on Delivery (COD) -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-success rounded p-2">
                  <i class="bx bx-money fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ __('Cash on Delivery (COD)') }}</h6>
                  <small class="text-muted">{{ __('Allow customers to pay in cash upon doorstep delivery.') }}</small>
                </div>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="payment_cod_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="payment_cod_enabled" value="1" {{ ($settings['payment_cod_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Maximum Order Amount for COD ($)') }}</label>
                <input type="number" name="payment_cod_max_limit" class="form-control" value="{{ $settings['payment_cod_max_limit'] ?? '500' }}" />
              </div>
            </div>
          </div>

          <!-- Stripe -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-primary rounded p-2">
                  <i class="bx bxl-stripe fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ __('Stripe Credit Cards & Apple/Google Pay') }}</h6>
                  <small class="text-muted">{{ __('Direct encrypted online payments via Stripe Elements.') }}</small>
                </div>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="payment_stripe_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="payment_stripe_enabled" value="1" {{ ($settings['payment_stripe_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Environment Mode') }}</label>
                <select name="stripe_mode" class="form-select">
                  <option value="test" {{ ($settings['stripe_mode'] ?? 'test') === 'test' ? 'selected' : '' }}>{{ __('Test / Sandbox') }}</option>
                  <option value="live" {{ ($settings['stripe_mode'] ?? '') === 'live' ? 'selected' : '' }}>{{ __('Live Production') }}</option>
                </select>
              </div>
              <div class="col-md-8">
                <label class="form-label fw-medium">{{ __('Stripe Publishable Key') }}</label>
                <input type="text" name="stripe_key" class="form-control" value="{{ $settings['stripe_key'] ?? 'pk_test_sample_key_12345' }}" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Stripe Secret Key (Encrypted in Database)') }}</label>
                <input type="password" name="stripe_secret" class="form-control" placeholder="••••••••••••••••••••••••" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Stripe Webhook Signing Secret') }}</label>
                <input type="password" name="stripe_webhook_secret" class="form-control" placeholder="whsec_••••••••••••••••" />
              </div>
            </div>
          </div>

          <!-- PayPal -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-info rounded p-2">
                  <i class="bx bxl-paypal fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ __('PayPal Express & Smart Buttons') }}</h6>
                  <small class="text-muted">{{ __('Seamless checkout for international PayPal account holders.') }}</small>
                </div>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="payment_paypal_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="payment_paypal_enabled" value="1" {{ ($settings['payment_paypal_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Environment Mode') }}</label>
                <select name="paypal_mode" class="form-select">
                  <option value="sandbox" {{ ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>{{ __('Sandbox / Test') }}</option>
                  <option value="live" {{ ($settings['paypal_mode'] ?? '') === 'live' ? 'selected' : '' }}>{{ __('Live Production') }}</option>
                </select>
              </div>
              <div class="col-md-8">
                <label class="form-label fw-medium">{{ __('PayPal Client ID') }}</label>
                <input type="text" name="paypal_client_id" class="form-control" value="{{ $settings['paypal_client_id'] ?? '' }}" placeholder="AYSq3RDG..." />
              </div>
              <div class="col-md-12">
                <label class="form-label fw-medium">{{ __('PayPal Secret Key (Encrypted in Database)') }}</label>
                <input type="password" name="paypal_secret" class="form-control" placeholder="••••••••••••••••••••••••" />
              </div>
            </div>
          </div>

          <!-- PhonePe / UPI -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-warning rounded p-2">
                  <i class="bx bx-qr-scan fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ __('PhonePe / UPI / QR Payments') }}</h6>
                  <small class="text-muted">{{ __('Direct instant UPI transfers for Indian customer transactions.') }}</small>
                </div>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="payment_phonepe_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="payment_phonepe_enabled" value="1" {{ ($settings['payment_phonepe_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Merchant ID') }}</label>
                <input type="text" name="phonepe_merchant_id" class="form-control" value="{{ $settings['phonepe_merchant_id'] ?? 'M2200TEST' }}" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Salt Key (Encrypted in Database)') }}</label>
                <input type="password" name="phonepe_salt_key" class="form-control" placeholder="••••••••••••••••" />
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Payment Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
