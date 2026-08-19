@extends('layouts/layoutMaster')

@section('title', __('SEO & Marketing Settings') . ' — AK-Mart')

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
            <i class="bx bx-search-alt text-info fs-4"></i>
            <span>{{ __('Search Engine Optimization (SEO) & Marketing') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure global meta tags, OpenGraph social previews, sitemaps, and analytics tracking codes.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'seo') }}">
          @csrf

          <!-- Meta Tags -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-globe text-primary"></i>
            <span>{{ __('Global Store Metadata') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Default Meta Title') }}</label>
              <input type="text" name="seo_meta_title" class="form-control" value="{{ $settings['seo_meta_title'] ?? 'AK-Mart — Premium Online Mini-Mart & E-Commerce Superstore' }}" />
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Default Meta Description') }}</label>
              <textarea name="seo_meta_description" class="form-control" rows="3">{{ $settings['seo_meta_description'] ?? 'Shop thousands of premium groceries, daily essentials, electronics, and home supplies with lightning-fast delivery from AK-Mart.' }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Meta Keywords (Comma separated)') }}</label>
              <input type="text" name="seo_meta_keywords" class="form-control" value="{{ $settings['seo_meta_keywords'] ?? 'ecommerce, mini-mart, grocery, online shopping, fast delivery, retail store' }}" />
            </div>
          </div>

          <!-- Analytics & Tracking -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-line-chart text-primary"></i>
            <span>{{ __('Analytics & Conversion Pixels') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Google Analytics 4 Measurement ID') }}</label>
              <input type="text" name="seo_google_analytics_id" class="form-control" value="{{ $settings['seo_google_analytics_id'] ?? 'G-AKMART9999' }}" placeholder="G-XXXXXXXXXX" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Meta (Facebook) Pixel ID') }}</label>
              <input type="text" name="seo_facebook_pixel_id" class="form-control" value="{{ $settings['seo_facebook_pixel_id'] ?? '' }}" placeholder="e.g. 192837465019" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save SEO Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
