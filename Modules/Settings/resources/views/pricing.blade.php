@extends('layouts/layoutMaster')

@section('title', __('Pricing & Tax (GST) Settings') . ' — AK-Mart')

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
            <i class="bx bx-dollar-circle text-warning fs-4"></i>
            <span>{{ __('Currency, Pricing & Tax (GST) Rules') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure store currency display, symbol positions, tax inclusion, and Indian GST / VAT compliance.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'pricing') }}">
          @csrf

          <!-- Currency & Formatting -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-money text-primary"></i>
            <span>{{ __('Store Currency & Number Formatting') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Default Currency') }}</label>
              <select name="currency" class="form-select">
                <option value="USD" {{ ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD ($ - US Dollar)</option>
                <option value="INR" {{ ($settings['currency'] ?? '') === 'INR' ? 'selected' : '' }}>INR (₹ - Indian Rupee)</option>
                <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR (€ - Euro)</option>
                <option value="GBP" {{ ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP (£ - British Pound)</option>
                <option value="AED" {{ ($settings['currency'] ?? '') === 'AED' ? 'selected' : '' }}>AED (د.إ - UAE Dirham)</option>
                <option value="SAR" {{ ($settings['currency'] ?? '') === 'SAR' ? 'selected' : '' }}>SAR (﷼ - Saudi Riyal)</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Currency Symbol') }}</label>
              <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? '$' }}" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Symbol Position') }}</label>
              <select name="currency_position" class="form-select">
                <option value="left" {{ ($settings['currency_position'] ?? 'left') === 'left' ? 'selected' : '' }}>{{ __('Left ($100.00)') }}</option>
                <option value="right" {{ ($settings['currency_position'] ?? '') === 'right' ? 'selected' : '' }}>{{ __('Right (100.00$)') }}</option>
                <option value="left_space" {{ ($settings['currency_position'] ?? '') === 'left_space' ? 'selected' : '' }}>{{ __('Left with Space ($ 100.00)') }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Decimal Places') }}</label>
              <select name="currency_decimals" class="form-select">
                <option value="2" {{ ($settings['currency_decimals'] ?? '2') === '2' ? 'selected' : '' }}>2 (100.00)</option>
                <option value="0" {{ ($settings['currency_decimals'] ?? '') === '0' ? 'selected' : '' }}>0 (100)</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Price Display in Storefront') }}</label>
              <select name="tax_inclusive_pricing" class="form-select">
                <option value="inclusive" {{ ($settings['tax_inclusive_pricing'] ?? 'inclusive') === 'inclusive' ? 'selected' : '' }}>{{ __('Prices inclusive of tax (All-in pricing)') }}</option>
                <option value="exclusive" {{ ($settings['tax_inclusive_pricing'] ?? '') === 'exclusive' ? 'selected' : '' }}>{{ __('Prices exclusive of tax (Tax added at checkout)') }}</option>
              </select>
            </div>
          </div>

          <!-- Tax / GST Configuration -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-receipt text-primary"></i>
            <span>{{ __('Tax Rates & Indian GST Compliance') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Tax Rate (%)') }}</label>
              <input type="number" step="0.01" name="default_tax_rate" class="form-control" value="{{ $settings['default_tax_rate'] ?? '18.00' }}" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default HSN / SAC Code') }}</label>
              <input type="text" name="default_hsn_code" class="form-control" value="{{ $settings['default_hsn_code'] ?? '84713010' }}" />
            </div>

            <div class="col-12">
              <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                  <div>
                    <h6 class="mb-0 fw-semibold">{{ __('Enable Indian GST Split (CGST + SGST for Intra-State, IGST for Inter-State)') }}</h6>
                    <small class="text-muted">{{ __('Calculates state-wise tax breakdown on checkout and digital PDF invoices.') }}</small>
                  </div>
                  <div class="form-check form-switch">
                    <input type="hidden" name="enable_gst_split" value="0">
                    <input class="form-check-input" type="checkbox" name="enable_gst_split" value="1" {{ ($settings['enable_gst_split'] ?? '1') === '1' ? 'checked' : '' }}>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Pricing & Tax Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
