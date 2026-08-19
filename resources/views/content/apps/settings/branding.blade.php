@extends('layouts/layoutMaster')

@section('title', __('Logos & Brand Identity') . ' — AK-Mart')

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
            <i class="bx bx-palette text-info fs-4"></i>
            <span>{{ __('Logos, Favicons & Brand Appearance') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Customize storefront light and dark logos, browser favicons, PDF invoice branding, and primary color accents.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'branding') }}" enctype="multipart/form-data">
          @csrf

          <!-- Logo Uploads -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-image text-primary"></i>
            <span>{{ __('Store Brand Logos & Icons') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <!-- Light Logo -->
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Light Mode Logo') }}</label>
              <div class="p-3 border rounded mb-2 bg-white text-center">
                @if(!empty($settings['site_logo']))
                  <img src="{{ asset($settings['site_logo']) }}" alt="Logo" style="max-height: 50px;" class="img-fluid" />
                @else
                  <span class="badge bg-label-primary fs-4 p-2 rounded"><i class="bx bx-shopping-bag"></i> AK-Mart</span>
                @endif
              </div>
              <input type="file" name="site_logo_file" class="form-control" accept="image/*" />
            </div>

            <!-- Dark Logo -->
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Dark Mode Logo') }}</label>
              <div class="p-3 border rounded mb-2 bg-dark text-center">
                @if(!empty($settings['site_logo_dark']))
                  <img src="{{ asset($settings['site_logo_dark']) }}" alt="Dark Logo" style="max-height: 50px;" class="img-fluid" />
                @else
                  <span class="badge bg-primary fs-4 p-2 rounded"><i class="bx bx-shopping-bag"></i> AK-Mart</span>
                @endif
              </div>
              <input type="file" name="site_logo_dark_file" class="form-control" accept="image/*" />
            </div>

            <!-- Favicon -->
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Browser Favicon (.ico / .png)') }}</label>
              <div class="p-3 border rounded mb-2 bg-light text-center">
                @if(!empty($settings['site_favicon']))
                  <img src="{{ asset($settings['site_favicon']) }}" alt="Favicon" style="max-height: 32px;" />
                @else
                  <i class="bx bx-globe fs-2 text-primary"></i>
                @endif
              </div>
              <input type="file" name="site_favicon_file" class="form-control" accept="image/*" />
            </div>

            <!-- Invoice Logo -->
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Printable Invoice Brand Logo') }}</label>
              <div class="p-3 border rounded mb-2 bg-white text-center">
                @if(!empty($settings['invoice_logo']))
                  <img src="{{ asset($settings['invoice_logo']) }}" alt="Invoice Logo" style="max-height: 50px;" class="img-fluid" />
                @else
                  <span class="fw-bold text-primary fs-5"><i class="bx bx-shopping-bag me-1"></i>AK-Mart</span>
                @endif
              </div>
              <input type="file" name="invoice_logo_file" class="form-control" accept="image/*" />
            </div>
          </div>

          <!-- Brand Colors -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-color-fill text-primary"></i>
            <span>{{ __('Theme & Accent Colors') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Primary Brand Accent Color') }}</label>
              <div class="input-group">
                <input type="color" name="brand_primary_color" class="form-control form-control-color" value="{{ $settings['brand_primary_color'] ?? '#696cff' }}" />
                <input type="text" class="form-control" value="{{ $settings['brand_primary_color'] ?? '#696cff' }}" readonly />
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Secondary Success Accent Color') }}</label>
              <div class="input-group">
                <input type="color" name="brand_secondary_color" class="form-control form-control-color" value="{{ $settings['brand_secondary_color'] ?? '#00d25b' }}" />
                <input type="text" class="form-control" value="{{ $settings['brand_secondary_color'] ?? '#00d25b' }}" readonly />
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Branding Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
