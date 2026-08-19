@extends('layouts/layoutMaster')

@section('title', __('Product Tools & Catalog Settings') . ' — AK-Mart')

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
            <i class="bx bx-box text-warning fs-4"></i>
            <span>{{ __('Products & Catalog Management Tools') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure SKU and barcode auto-generation, media upload limits, and AI product extraction.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'products') }}">
          @csrf

          <!-- Auto SKU & Barcode Generation -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-barcode text-primary"></i>
            <span>{{ __('SKU & Barcode Generation Rules') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Auto-Generate SKU on Product Creation') }}</label>
              <div class="form-check form-switch mt-2">
                <input type="hidden" name="auto_generate_sku" value="0">
                <input class="form-check-input" type="checkbox" name="auto_generate_sku" value="1" {{ ($settings['auto_generate_sku'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label">{{ __('Enabled') }}</label>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('SKU Prefix Format') }}</label>
              <input type="text" name="sku_prefix" class="form-control" value="{{ $settings['sku_prefix'] ?? 'AKM-' }}" placeholder="e.g. AKM-" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Auto-Generate Barcodes') }}</label>
              <div class="form-check form-switch mt-2">
                <input type="hidden" name="auto_generate_barcode" value="0">
                <input class="form-check-input" type="checkbox" name="auto_generate_barcode" value="1" {{ ($settings['auto_generate_barcode'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label">{{ __('Enabled') }}</label>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Barcode Symbology') }}</label>
              <select name="barcode_format" class="form-select">
                <option value="EAN-13" {{ ($settings['barcode_format'] ?? 'EAN-13') === 'EAN-13' ? 'selected' : '' }}>EAN-13 (Standard Retail)</option>
                <option value="CODE-128" {{ ($settings['barcode_format'] ?? '') === 'CODE-128' ? 'selected' : '' }}>CODE-128 (Alphanumeric)</option>
                <option value="UPC-A" {{ ($settings['barcode_format'] ?? '') === 'UPC-A' ? 'selected' : '' }}>UPC-A (North America)</option>
              </select>
            </div>
          </div>

          <!-- Product Media Uploads -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-image text-primary"></i>
            <span>{{ __('Media & Image Upload Limits') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Maximum Images Per Product') }}</label>
              <input type="number" name="product_max_images" class="form-control" value="{{ $settings['product_max_images'] ?? '10' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Max Image Upload Size (MB)') }}</label>
              <input type="number" name="product_max_image_size_mb" class="form-control" value="{{ $settings['product_max_image_size_mb'] ?? '5' }}" />
            </div>
          </div>

          <!-- Smart AI Product Tools -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-bot text-primary"></i>
            <span>{{ __('AI Smart Product Tools') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable AI Product Description & Attribute Generator') }}</h6>
                <small class="text-muted">{{ __('Use Google Gemini AI to auto-generate marketing titles, bullet points, and SEO metadata.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="enable_ai_product_generation" value="0">
                <input class="form-check-input" type="checkbox" name="enable_ai_product_generation" value="1" {{ ($settings['enable_ai_product_generation'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Universal URL Product Importer') }}</h6>
                <small class="text-muted">{{ __('Allow importing product details and images directly from Amazon, Walmart, or supplier URLs.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="enable_url_product_import" value="0">
                <input class="form-check-input" type="checkbox" name="enable_url_product_import" value="1" {{ ($settings['enable_url_product_import'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Product Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
