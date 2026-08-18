@extends('layouts/layoutMaster')

@section('title', 'Omnichannel Product Feeds — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-broadcast text-primary me-2"></i> Omnichannel Marketplace Product Feeds</h4>
        <p class="text-muted small mb-0">Automated catalog synchronization feeds for Google Merchant Center, Meta Catalog, and TikTok Shop</p>
    </div>
</div>

<!-- Channel Feed Cards -->
<div class="row g-4">
    <!-- Google Shopping -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-danger fs-6"><i class="bx bxl-google me-1"></i> Google Shopping</span>
                    <span class="badge bg-success">Live RSS 2.0</span>
                </div>
                <h5 class="fw-bold mb-2">Google Merchant Feed</h5>
                <p class="text-muted small mb-3">Compliant XML RSS 2.0 feed with GTIN/Barcode, MPN, condition, and availability metadata.</p>
                <div class="p-2 bg-light rounded mb-3">
                    <small class="text-break"><code>{{ url('/feeds/google.xml') }}</code></small>
                </div>
                <a href="{{ route('app-feeds-google') }}" target="_blank" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bx bx-link-external me-1"></i> View Live XML Feed
                </a>
            </div>
        </div>
    </div>

    <!-- Meta / Facebook Catalog -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-primary fs-6"><i class="bx bxl-facebook-square me-1"></i> Meta Commerce</span>
                    <span class="badge bg-success">CSV Catalog</span>
                </div>
                <h5 class="fw-bold mb-2">Facebook & Instagram Shop</h5>
                <p class="text-muted small mb-3">Structured CSV product catalog for dynamic product ads (DPA) and Instagram Shopping tag synchronization.</p>
                <div class="p-2 bg-light rounded mb-3">
                    <small class="text-break"><code>{{ url('/feeds/meta.csv') }}</code></small>
                </div>
                <a href="{{ route('app-feeds-meta') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bx bx-download me-1"></i> Download Meta CSV
                </a>
            </div>
        </div>
    </div>

    <!-- TikTok Shop -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-dark fs-6"><i class="bx bxl-tiktok me-1"></i> TikTok Shop</span>
                    <span class="badge bg-success">JSON Feed</span>
                </div>
                <h5 class="fw-bold mb-2">TikTok Product Sync</h5>
                <p class="text-muted small mb-3">Real-time JSON endpoint formatted for TikTok Catalog Partner API and in-app storefront showcase.</p>
                <div class="p-2 bg-light rounded mb-3">
                    <small class="text-break"><code>{{ url('/feeds/tiktok.json') }}</code></small>
                </div>
                <a href="{{ route('app-feeds-tiktok') }}" target="_blank" class="btn btn-sm btn-outline-dark w-100">
                    <i class="bx bx-code-alt me-1"></i> View JSON Feed
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
