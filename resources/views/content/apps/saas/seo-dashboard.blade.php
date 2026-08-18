@extends('layouts/layoutMaster')

@section('title', __('Global SEO & Marketing Hub') . ' — AK-Mart')

@section('content')
<div class="row g-6 mb-6">
    <div class="col-md-4">
        <div class="card bg-label-primary h-100">
            <div class="card-body text-center">
                <i class="bx bx-search-alt-2 display-4 mb-3"></i>
                <h4 class="mb-1 fw-bold">{{ $productCount }}</h4>
                <p class="mb-0">{{ __('Indexed Products') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-label-warning h-100">
            <div class="card-body text-center">
                <i class="bx bx-error-circle display-4 mb-3"></i>
                <h4 class="mb-1 fw-bold">{{ $missingMeta }}</h4>
                <p class="mb-0">{{ __('Missing Meta Descriptions') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-label-success h-100">
            <div class="card-body text-center">
                <i class="bx bx-check-shield display-4 mb-3"></i>
                <h4 class="mb-1 fw-bold">{{ __('Active') }}</h4>
                <p class="mb-0">{{ __('Dynamic Sitemap Status') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Marketing Tools') }}</h5>
            </div>
            <div class="card-body pt-6">
                <div class="d-flex align-items-center mb-6">
                    <div class="avatar avatar-md me-4">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-map-alt font-24px"></i></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">{{ __('Dynamic Sitemap') }}</h6>
                        <p class="mb-0 text-muted small">{{ __('Auto-generated XML sitemap for Google/Bing indexing.') }}</p>
                    </div>
                    <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-sm btn-outline-primary">{{ __('View XML') }}</a>
                </div>

                <div class="d-flex align-items-center mb-6">
                    <div class="avatar avatar-md me-4">
                        <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-code-block font-24px"></i></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">{{ __('Schema.org Generator') }}</h6>
                        <p class="mb-0 text-muted small">{{ __('Injection of JSON-LD for rich snippets in search results.') }}</p>
                    </div>
                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                </div>

                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-4">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bxl-google font-24px"></i></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">{{ __('Google Search Console') }}</h6>
                        <p class="mb-0 text-muted small">{{ __('Connect and verify your site ownership.') }}</p>
                    </div>
                    <button class="btn btn-sm btn-primary">{{ __('Connect') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('SEO Score') }}</h5>
            </div>
            <div class="card-body text-center py-10">
                <div class="chart-container mb-4">
                    <h1 class="display-3 fw-bold text-primary">84%</h1>
                </div>
                <h6>{{ __('System Health Check') }}</h6>
                <p class="text-muted small">{{ __('Your marketplace is well-optimized for search engines.') }}</p>
                <button class="btn btn-sm btn-label-primary">{{ __('Run Deep Scan') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection
