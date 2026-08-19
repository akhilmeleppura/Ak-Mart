@extends('layouts/layoutMaster')

@section('title', __('Integrations & Connected Services') . ' — AK-Mart')

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
            <i class="bx bx-grid-alt text-dark fs-4"></i>
            <span>{{ __('Third-Party Integrations & Cloud Services') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Connect external APIs, geolocation providers, and cloud file storage systems.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'integrations') }}">
          @csrf

          <!-- Google Maps Integration -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-danger rounded p-2">
                  <i class="bx bx-map-alt fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ __('Google Maps Platform API') }}</h6>
                  <small class="text-muted">{{ __('Powers driver live location tracking, branch pin locators, and autocomplete address checkout.') }}</small>
                </div>
              </div>
              <span class="badge bg-label-success">{{ __('Connected') }}</span>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-medium">{{ __('Google Maps JavaScript API Key') }}</label>
                <input type="text" name="google_maps_api_key" class="form-control" value="{{ $settings['google_maps_api_key'] ?? 'AIzaSySampleKeyGoogleMapsPlatform' }}" />
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Default Map Latitude') }}</label>
                <input type="text" name="default_lat" class="form-control" value="{{ $settings['default_lat'] ?? '40.7128' }}" />
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Default Map Longitude') }}</label>
                <input type="text" name="default_lng" class="form-control" value="{{ $settings['default_lng'] ?? '-74.0060' }}" />
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">{{ __('Default Map Zoom Level') }}</label>
                <input type="number" name="default_zoom" class="form-control" value="{{ $settings['default_zoom'] ?? '12' }}" />
              </div>
            </div>
          </div>

          <!-- Cloud Storage -->
          <div class="border rounded p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-warning rounded p-2">
                  <i class="bx bx-cloud fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ __('Cloud File Storage (Media & Invoices)') }}</h6>
                  <small class="text-muted">{{ __('Store product images, PDF invoices, and brand logos securely.') }}</small>
                </div>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Default Filesystem Disk') }}</label>
                <select name="filesystem_disk" class="form-select">
                  <option value="public" {{ ($settings['filesystem_disk'] ?? 'public') === 'public' ? 'selected' : '' }}>Local Public Disk (Storage Symlink)</option>
                  <option value="s3" {{ ($settings['filesystem_disk'] ?? '') === 's3' ? 'selected' : '' }}>Amazon AWS S3 Bucket</option>
                </select>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Integration Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
