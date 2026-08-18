@extends('layouts/layoutMaster')

@section('title', __('Catalog Health Scanner') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-scan text-primary me-2"></i> {{ __('Catalog Quality Scanner & Store Health') }}</h4>
        <p class="text-muted small mb-0">{{ __('Automated diagnostic scanner for missing SKU, images, descriptions, SEO metadata & prices') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('app-catalog-duplicates') }}" class="btn btn-outline-warning">
            <i class="bx bx-copy-alt me-1"></i> {{ __('Duplicate Scanner') }}
        </a>
        <a href="{{ route('app-product-importer') }}" class="btn btn-primary">
            <i class="bx bx-import me-1"></i> {{ __('Smart Product Importer') }}
        </a>
    </div>
</div>

{{-- Overall Health Banner --}}
<div class="card mb-4 bg-primary text-white shadow">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="text-white fw-bold mb-2">{{ __('Overall AK-Mart Store Health:') }} <span class="badge bg-white text-primary fs-5 ms-2">{{ $overallHealthScore }}%</span></h4>
                <p class="mb-0 text-white-50">{{ __('Composite rating based on product completeness, inventory availability, SEO rankings, customer data integrity, and RBAC security.') }}</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-label-light fs-6 px-3 py-2">
                    {{ $totalIssues === 0 ? __('✓ Zero Critical Errors') : ($criticalCount . ' ' . __('Critical Issues Found')) }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Score Metric Cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border">
            <div class="card-body text-center">
                <span class="text-muted d-block small mb-1">{{ __('Product Quality') }}</span>
                <h3 class="mb-1 fw-bold text-primary">{{ $productQualityScore }}%</h3>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $productQualityScore }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border">
            <div class="card-body text-center">
                <span class="text-muted d-block small mb-1">{{ __('Inventory Health') }}</span>
                <h3 class="mb-1 fw-bold text-success">{{ $inventoryHealthScore }}%</h3>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $inventoryHealthScore }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border">
            <div class="card-body text-center">
                <span class="text-muted d-block small mb-1">{{ __('SEO Readiness') }}</span>
                <h3 class="mb-1 fw-bold text-info">{{ $seoScore }}%</h3>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $seoScore }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border">
            <div class="card-body text-center">
                <span class="text-muted d-block small mb-1">{{ __('Security & Auth') }}</span>
                <h3 class="mb-1 fw-bold text-warning">{{ $securityScore }}%</h3>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $securityScore }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Auto-Fix Tools --}}
<div class="card mb-4 border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0"><i class="bx bx-wrench me-1 text-primary"></i> {{ __('1-Click Catalog Auto-Fix Utilities') }}</h5>
    </div>
    <div class="card-body py-3">
        <div class="row g-3">
            <div class="col-md-4">
                <form action="{{ route('app-catalog-scanner-autofix') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fix_type" value="missing_sku">
                    <button type="submit" class="btn btn-outline-primary w-100 py-2">
                        <i class="bx bx-barcode me-1"></i> {{ __('Auto-Generate Missing SKUs') }}
                    </button>
                </form>
            </div>
            <div class="col-md-4">
                <form action="{{ route('app-catalog-scanner-autofix') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fix_type" value="missing_seo">
                    <button type="submit" class="btn btn-outline-info w-100 py-2">
                        <i class="bx bx-globe me-1"></i> {{ __('Auto-Fill Missing SEO Tags') }}
                    </button>
                </form>
            </div>
            <div class="col-md-4">
                <form action="{{ route('app-catalog-scanner-autofix') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fix_type" value="missing_barcode">
                    <button type="submit" class="btn btn-outline-success w-100 py-2">
                        <i class="bx bx-scan me-1"></i> {{ __('Auto-Assign POS Barcodes') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Catalog Issues Breakdown --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Detected Catalog Issues & Recommendations') }} ({{ $totalIssues }})</h5>
        <div class="d-flex gap-2">
            <span class="badge bg-danger">{{ $criticalCount }} {{ __('Critical') }}</span>
            <span class="badge bg-warning">{{ $warningCount }} {{ __('Warnings') }}</span>
            <span class="badge bg-info">{{ $infoCount }} {{ __('Info') }}</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Severity') }}</th>
                    <th>{{ __('Issue Type') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Diagnostic Details') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Critical Issues --}}
                @foreach($issues['critical'] as $item)
                    <tr>
                        <td><span class="badge bg-danger">{{ __('Critical') }}</span></td>
                        <td><strong>{{ $item['type'] }}</strong></td>
                        <td>{{ $item['product']->name }}</td>
                        <td><span class="text-danger">{{ $item['message'] }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('app-ecommerce-product-edit', $item['product']->id) }}" class="btn btn-sm btn-primary">{{ __('Fix Now') }}</a>
                        </td>
                    </tr>
                @endforeach

                {{-- Warnings --}}
                @foreach($issues['warning'] as $item)
                    <tr>
                        <td><span class="badge bg-warning">{{ __('Warning') }}</span></td>
                        <td><strong>{{ $item['type'] }}</strong></td>
                        <td>{{ $item['product']->name }}</td>
                        <td><span class="text-muted">{{ $item['message'] }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('app-ecommerce-product-edit', $item['product']->id) }}" class="btn btn-sm btn-label-secondary">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach

                {{-- Info --}}
                @foreach($issues['info'] as $item)
                    <tr>
                        <td><span class="badge bg-info">{{ __('Info') }}</span></td>
                        <td><strong>{{ $item['type'] }}</strong></td>
                        <td>{{ $item['product']->name }}</td>
                        <td><span class="text-muted">{{ $item['message'] }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('app-ecommerce-product-edit', $item['product']->id) }}" class="btn btn-sm btn-label-secondary">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @endforeach

                @if($totalIssues === 0)
                    <tr>
                        <td colspan="5" class="text-center py-5 text-success">
                            <i class="bx bx-check-shield fs-1 d-block mb-2"></i>
                            <strong>{{ __('Excellent! 100% of products passed catalog health checks.') }}</strong>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
