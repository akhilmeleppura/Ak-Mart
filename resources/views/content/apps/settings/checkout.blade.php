@extends('layouts/layoutMaster')

@section('title', __('Checkout Settings & Policies') . ' — AK-Mart')

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
            <i class="bx bx-cart-alt text-primary fs-4"></i>
            <span>{{ __('Checkout Fields & Legal Policies') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure customer input requirements, terms agreements, privacy policies, and checkout notes.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'checkout') }}">
          @csrf

          <!-- Field Requirements -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-user-pin text-primary"></i>
            <span>{{ __('Customer Information Fields') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Customer Phone Number') }}</label>
              <select name="shipping_phone_requirement" class="form-select">
                <option value="required" {{ ($settings['shipping_phone_requirement'] ?? 'required') === 'required' ? 'selected' : '' }}>{{ __('Required for all orders') }}</option>
                <option value="optional" {{ ($settings['shipping_phone_requirement'] ?? '') === 'optional' ? 'selected' : '' }}>{{ __('Optional') }}</option>
                <option value="hidden" {{ ($settings['shipping_phone_requirement'] ?? '') === 'hidden' ? 'selected' : '' }}>{{ __('Hidden / Not asked') }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Company / Organization Name') }}</label>
              <select name="company_name_requirement" class="form-select">
                <option value="optional" {{ ($settings['company_name_requirement'] ?? 'optional') === 'optional' ? 'selected' : '' }}>{{ __('Optional') }}</option>
                <option value="required" {{ ($settings['company_name_requirement'] ?? '') === 'required' ? 'selected' : '' }}>{{ __('Required') }}</option>
                <option value="hidden" {{ ($settings['company_name_requirement'] ?? '') === 'hidden' ? 'selected' : '' }}>{{ __('Hidden') }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Address Line 2 (Apartment/Suite)') }}</label>
              <select name="address_line_2_requirement" class="form-select">
                <option value="optional" {{ ($settings['address_line_2_requirement'] ?? 'optional') === 'optional' ? 'selected' : '' }}>{{ __('Optional') }}</option>
                <option value="required" {{ ($settings['address_line_2_requirement'] ?? '') === 'required' ? 'selected' : '' }}>{{ __('Required') }}</option>
                <option value="hidden" {{ ($settings['address_line_2_requirement'] ?? '') === 'hidden' ? 'selected' : '' }}>{{ __('Hidden') }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Customer Order Notes / Special Instructions') }}</label>
              <select name="customer_order_notes" class="form-select">
                <option value="enabled" {{ ($settings['customer_order_notes'] ?? 'enabled') === 'enabled' ? 'selected' : '' }}>{{ __('Enabled (Optional note box)') }}</option>
                <option value="disabled" {{ ($settings['customer_order_notes'] ?? '') === 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
              </select>
            </div>
          </div>

          <!-- Legal Policies & Agreements -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-file text-primary"></i>
            <span>{{ __('Legal Terms & Checkout Policies') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-12">
              <div class="form-check form-switch mb-3">
                <input type="hidden" name="require_terms_acceptance" value="0">
                <input class="form-check-input" type="checkbox" name="require_terms_acceptance" value="1" {{ ($settings['require_terms_acceptance'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold">{{ __('Require customers to check "I agree to Terms & Conditions" before placing order') }}</label>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Terms & Conditions Summary / URL') }}</label>
              <textarea name="terms_and_conditions" class="form-control" rows="2">{{ $settings['terms_and_conditions'] ?? 'By placing an order on AK-Mart, you agree to our standard terms of purchase, warranty coverage, and shipment conditions.' }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Refund Policy Summary / URL') }}</label>
              <textarea name="refund_policy" class="form-control" rows="2">{{ $settings['refund_policy'] ?? 'Items can be returned within 14 days of delivery in original condition with tags intact.' }}</textarea>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Checkout Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
