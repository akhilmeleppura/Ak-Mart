@extends('layouts/layoutMaster')

@section('title', 'Product Import Staging Review — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-check-shield text-primary me-2"></i> Product Import Review Screen</h4>
        <p class="text-muted small mb-0">Verify extracted attributes, edit fields, and safely publish to your live catalog</p>
    </div>
    <a href="{{ route('app-product-importer') }}" class="btn btn-label-secondary">
        <i class="bx bx-arrow-back me-1"></i> Back to Importer
    </a>
</div>

{{-- Extraction Confidence & Sources Banner --}}
@php
    $score = $import->confidence_score ?: ($data['confidence_score'] ?? 85);
    $sources = $import->sources ?: ($data['sources'] ?? []);
    $warnings = $import->warnings ?: ($data['warnings'] ?? []);
@endphp

<div class="card mb-4 border shadow-sm">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                @php
                    $platform = $data['platform'] ?? ($import->asin ? 'amazon' : 'generic');
                @endphp
                @if($platform === 'amazon')
                    <span class="badge bg-warning"><i class="bx bxl-amazon me-1"></i> Amazon (ASIN: {{ $import->asin ?: $data['asin'] }})</span>
                @elseif($platform === 'flipkart')
                    <span class="badge bg-primary"><i class="bx bx-shopping-bag me-1"></i> Flipkart (ID: {{ $import->asin ?: $data['asin'] }})</span>
                @elseif($platform === 'meesho')
                    <span class="badge bg-info"><i class="bx bx-store-alt me-1"></i> Meesho Product</span>
                @elseif($platform === 'shopify')
                    <span class="badge bg-success"><i class="bx bxl-shopify me-1"></i> Shopify Store</span>
                @else
                    <span class="badge bg-secondary"><i class="bx bx-globe me-1"></i> E-Commerce Web Scrape</span>
                @endif
                <span class="badge {{ $score >= 80 ? 'bg-success' : ($score >= 60 ? 'bg-warning' : 'bg-danger') }}">
                    Extraction Quality: {{ $score }}%
                </span>
            </div>
            @if(!empty($import->canonical_url) || !empty($data['canonical_url']))
                <small class="text-muted text-truncate" style="max-width: 400px;">
                    <i class="bx bx-link me-1"></i> Canonical: <a href="{{ $import->canonical_url ?: $data['canonical_url'] }}" target="_blank">{{ $import->canonical_url ?: $data['canonical_url'] }}</a>
                </small>
            @endif
        </div>

        @if(!empty($warnings))
            <div class="alert alert-warning py-2 px-3 small mb-2" role="alert">
                <i class="bx bx-error me-1"></i> <strong>Extraction Warnings:</strong>
                <ul class="mb-0 ps-3">
                    @foreach($warnings as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex flex-wrap gap-2 align-items-center pt-2 border-top">
            <small class="text-muted fw-bold">Verified Sources:</small>
            @foreach($sources as $field => $src)
                <span class="badge bg-label-secondary font-monospace" style="font-size: 0.75rem;">
                    {{ $field }}: {{ $src }}
                </span>
            @endforeach
        </div>
    </div>
</div>

<form action="{{ route('app-product-import-publish', $import->id) }}" method="POST">
    @csrf
    <div class="row g-4">
        {{-- Left Column: Product Data Form --}}
        <div class="col-lg-8">
            {{-- Main Info --}}
            <div class="card border mb-4 shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">General Product Information</h5>
                </div>
                <div class="card-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name / Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ $data['name'] ?? '' }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SKU Code</label>
                            <input type="text" name="sku" class="form-control" value="{{ $data['sku'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Barcode / GTIN</label>
                            <input type="text" name="barcode" class="form-control" value="{{ $data['barcode'] ?? '' }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand / Manufacturer</label>
                            <input type="text" name="brand" class="form-control" value="{{ $data['brand'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Initial Stock Quantity</label>
                            <input type="number" name="qty" class="form-control" value="{{ $data['qty'] ?? 10 }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Description</label>
                        <textarea name="description" class="form-control" rows="6">{{ $data['description'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Bullet Points / Features --}}
            @if(!empty($data['bullet_points']))
            <div class="card border mb-4 shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bx bx-list-check me-1 text-primary"></i> About This Item (Bullet Points)</h5>
                    <span class="badge bg-label-primary">{{ count($data['bullet_points']) }} Bullets</span>
                </div>
                <div class="card-body py-3">
                    <ul class="list-group list-group-flush">
                        @foreach($data['bullet_points'] as $bullet)
                            <li class="list-group-item px-0 py-2 small">
                                <i class="bx bx-check-circle text-success me-2"></i> {{ $bullet }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Technical Specifications Table --}}
            @if(!empty($data['specifications']))
            <div class="card border mb-4 shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bx bx-slider me-1 text-primary"></i> Technical Specifications & Attributes</h5>
                    <span class="badge bg-label-info">{{ count($data['specifications']) }} Attributes</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Attribute / Spec</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['specifications'] as $key => $val)
                                <tr>
                                    <td><strong>{{ $key }}</strong></td>
                                    <td>
                                        <input type="text" name="specifications[{{ $key }}]" class="form-control form-control-sm" value="{{ $val }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Extracted Variants --}}
            @if(!empty($data['variants']))
            <div class="card border mb-4 shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Extracted Product Variants ({{ count($data['variants']) }})</h5>
                </div>
                <div class="card-body py-3">
                    @foreach($data['variants'] as $idx => $v)
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-3">
                            <input type="text" name="variants[{{ $idx }}][name]" class="form-control form-control-sm" value="{{ $v['name'] ?? 'Attribute' }}">
                        </div>
                        <div class="col-4">
                            <input type="text" name="variants[{{ $idx }}][value]" class="form-control form-control-sm" value="{{ $v['value'] ?? '' }}">
                        </div>
                        <div class="col-3">
                            <input type="number" step="0.01" name="variants[{{ $idx }}][price]" class="form-control form-control-sm" value="{{ $v['price'] ?? $data['price'] ?? 0 }}">
                        </div>
                        <div class="col-2">
                            <input type="number" name="variants[{{ $idx }}][qty]" class="form-control form-control-sm" value="{{ $v['qty'] ?? 5 }}">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Pricing, Category, Gallery Images --}}
        <div class="col-lg-4">
            {{-- Publishing Card --}}
            <div class="card border mb-4 shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Publishing Options</h5>
                </div>
                <div class="card-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Store Category <span class="text-danger">*</span></label>
                        @php
                            $detectedCat = strtolower($data['category_name'] ?? '');
                            $selectedCatId = null;
                            // 1. Exact or partial match
                            foreach($categories as $cat) {
                                $catNameLower = strtolower($cat->name);
                                if (!empty($detectedCat) && (str_contains($catNameLower, $detectedCat) || str_contains($detectedCat, $catNameLower))) {
                                    $selectedCatId = $cat->id;
                                    break;
                                }
                            }
                            // 2. Token match (e.g. "Wrist Watches" -> matches "Watches", "Watch", "Accessories")
                            if (!$selectedCatId && !empty($detectedCat)) {
                                $tokens = explode(' ', $detectedCat);
                                foreach($tokens as $t) {
                                    $t = trim(rtrim($t, 's'));
                                    if (strlen($t) >= 3) {
                                        foreach($categories as $cat) {
                                            if (str_contains(strtolower($cat->name), $t)) {
                                                $selectedCatId = $cat->id;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        <select name="category_id" class="form-select" required>
                            <option value="" disabled {{ !$selectedCatId ? 'selected' : '' }}>-- Select Store Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $selectedCatId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($data['category_name']))
                            <small class="text-muted">Detected category: <strong>{{ $data['category_name'] }}</strong></small>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bx bx-check-circle me-1"></i> Publish to Live Store
                        </button>
                    </div>
                </div>
            </div>

            {{-- Pricing Card --}}
            <div class="card border mb-4 shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Pricing & Discount</h5>
                </div>
                <div class="card-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selling Price ($ / ₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control form-control-lg text-success fw-bold" value="{{ $data['price'] ?? 0 }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">List Price / MRP ($ / ₹)</label>
                        <input type="number" step="0.01" name="compare_at_price" class="form-control" value="{{ $data['compare_at_price'] ?? 0 }}">
                        @if(!empty($data['discount_percent']) && $data['discount_percent'] > 0)
                            <small class="text-success fw-bold"><i class="bx bx-tag me-1"></i> {{ $data['discount_percent'] }}% Off extracted from Amazon</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- High-Resolution Gallery Images --}}
            <div class="card border shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Extracted Images</h5>
                    <span class="badge bg-label-primary">{{ count($data['gallery_images'] ?? [$data['image'] ?? '']) }} Images</span>
                </div>
                <div class="card-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Primary Image URL</label>
                        <input type="text" name="image" id="primaryImageInput" class="form-control form-control-sm mb-2" value="{{ $data['image'] ?? '' }}">
                        <div class="text-center p-2 border rounded bg-light">
                            <img id="primaryImagePreview" src="{{ $data['image'] ?? '' }}" alt="Product" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                        </div>
                    </div>

                    @if(!empty($data['gallery_images']) && count($data['gallery_images']) > 1)
                        <label class="form-label fw-semibold mb-2">High-Resolution Gallery:</label>
                        <div class="row g-2">
                            @foreach($data['gallery_images'] as $img)
                                <div class="col-4">
                                    <img src="{{ $img }}" class="img-thumbnail w-100" style="height: 70px; object-fit: cover; cursor: pointer;" onclick="setPrimaryImage('{{ $img }}')">
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">Click any gallery thumbnail to set as primary image.</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function setPrimaryImage(url) {
    document.getElementById('primaryImageInput').value = url;
    document.getElementById('primaryImagePreview').src = url;
}
</script>
@endsection
