@extends('layouts/layoutMaster')

@section('title', 'Universal Product Importer — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-import text-primary me-2"></i> Universal E-Commerce Product Importer</h4>
        <p class="text-muted small mb-0">Extract structured catalog data from Amazon, Flipkart, Meesho, Shopify, WooCommerce, or batch upload CSV/JSON</p>
    </div>
    <a href="{{ route('app-ecommerce-product-list') }}" class="btn btn-label-secondary">
        <i class="bx bx-arrow-back me-1"></i> Product List
    </a>
</div>

{{-- Flash Warnings or Errors --}}
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible mb-4" role="alert">
        <i class="bx bx-error-circle me-1"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    {{-- URL Product Scraper --}}
    <div class="col-lg-6">
        <div class="card h-100 border shadow-sm">
            <div class="card-header border-bottom bg-label-primary d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary"><i class="bx bx-link me-1"></i> Extract from Any E-Commerce URL</h5>
                <span class="badge bg-primary">Universal Engine</span>
            </div>
            <div class="card-body py-4">
                <p class="small text-muted mb-2">
                    Enter any product link. Our multi-platform router automatically detects the store type and extracts title, selling price, list price (MRP), high-res images, brand, specifications, variants, and bullet points.
                </p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-label-warning"><i class="bx bxl-amazon me-1"></i> Amazon</span>
                    <span class="badge bg-label-primary"><i class="bx bx-shopping-bag me-1"></i> Flipkart</span>
                    <span class="badge bg-label-info"><i class="bx bx-store-alt me-1"></i> Meesho</span>
                    <span class="badge bg-label-success"><i class="bx bxl-shopify me-1"></i> Shopify</span>
                    <span class="badge bg-label-secondary"><i class="bx bx-globe me-1"></i> WooCommerce / Custom</span>
                </div>
                <form action="{{ route('app-product-import-url') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Product URL</label>
                        <div class="input-group">
                            <input type="url" name="product_url" class="form-control" placeholder="https://amazon.in/dp/... or https://flipkart.com/... or https://store.myshopify.com/..." required>
                            <button type="submit" class="btn btn-primary"><i class="bx bx-search-alt me-1"></i> Extract Product</button>
                        </div>
                        <small class="text-muted"><i class="bx bx-shield-check me-1 text-success"></i> SSRF protected with automatic platform detection & canonicalization</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- File Upload Importer --}}
    <div class="col-lg-6">
        <div class="card h-100 border shadow-sm">
            <div class="card-header border-bottom bg-label-success">
                <h5 class="card-title mb-0 text-success"><i class="bx bx-file me-1"></i> Batch CSV / JSON File Import</h5>
            </div>
            <div class="card-body py-4">
                <p class="small text-muted mb-3">Upload catalog sheets in CSV, Excel or JSON format. All products will be staged for verification before publishing to the live catalog.</p>
                <form action="{{ route('app-product-import-file') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose File (CSV, JSON)</label>
                        <div class="input-group">
                            <input type="file" name="import_file" class="form-control" accept=".csv,.json,.txt" required>
                            <button type="submit" class="btn btn-success"><i class="bx bx-upload me-1"></i> Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Staging Review Queue --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">Import Staging & Verification Queue ({{ $drafts->count() }})</h5>
            <small class="text-muted">Review, edit, and publish imported draft products to the live store.</small>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Platform</th>
                    <th>Extracted Product</th>
                    <th>Price / MRP</th>
                    <th>Identifier / SKU</th>
                    <th>Extraction Quality</th>
                    <th>Staging Status</th>
                    <th>Imported At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drafts as $d)
                    @php
                        $platform = $d->data['platform'] ?? ($d->asin ? 'amazon' : 'generic');
                        $badgeClasses = [
                            'amazon'   => 'bg-label-warning',
                            'flipkart' => 'bg-label-primary',
                            'meesho'   => 'bg-label-info',
                            'shopify'  => 'bg-label-success',
                            'generic'  => 'bg-label-secondary'
                        ];
                    @endphp
                    <tr>
                        <td>
                            @if($d->source_type === 'url')
                                <span class="badge {{ $badgeClasses[$platform] ?? 'bg-label-info' }}">
                                    @if($platform === 'amazon') <i class="bx bxl-amazon me-1"></i> Amazon
                                    @elseif($platform === 'flipkart') <i class="bx bx-shopping-bag me-1"></i> Flipkart
                                    @elseif($platform === 'meesho') <i class="bx bx-store-alt me-1"></i> Meesho
                                    @elseif($platform === 'shopify') <i class="bx bxl-shopify me-1"></i> Shopify
                                    @else <i class="bx bx-globe me-1"></i> Web Scrape
                                    @endif
                                </span>
                            @else
                                <span class="badge bg-label-success"><i class="bx bx-file me-1"></i> File Upload</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ \Illuminate\Support\Str::limit($d->data['name'] ?? 'Product', 45) }}</strong>
                            <div class="small text-muted">{{ $d->data['brand'] ?? 'Generic' }} • {{ $d->data['category_name'] ?? 'General' }}</div>
                        </td>
                        <td>
                            <strong class="text-success">
                                {{ ($d->data['currency'] ?? 'INR') === 'INR' ? '₹' : '$' }}{{ number_format($d->data['price'] ?? 0, 2) }}
                            </strong>
                            @if(!empty($d->data['compare_at_price']) && (float)$d->data['compare_at_price'] > (float)$d->data['price'])
                                <br><small class="text-muted text-decoration-line-through">{{ ($d->data['currency'] ?? 'INR') === 'INR' ? '₹' : '$' }}{{ number_format($d->data['compare_at_price'], 2) }}</small>
                            @endif
                        </td>
                        <td>
                            <code>{{ $d->asin ?: ($d->data['sku'] ?? 'N/A') }}</code>
                        </td>
                        <td>
                            @php
                                $score = $d->confidence_score ?: ($d->data['confidence_score'] ?? 85);
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                    <div class="progress-bar {{ $score >= 80 ? 'bg-success' : ($score >= 60 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $score }}%"></div>
                                </div>
                                <span class="small fw-bold">{{ $score }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($d->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-warning">Draft (Review Needed)</span>
                            @endif
                        </td>
                        <td><small>{{ $d->created_at->diffForHumans() }}</small></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('app-product-import-review', $d->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Review & Publish
                                </a>
                                <form action="{{ route('app-product-import-destroy', $d->id) }}" method="POST" onsubmit="return confirm('Discard this imported draft?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bx bx-inbox fs-1 d-block mb-2"></i>
                            No imported products in staging queue. Enter any e-commerce URL or upload a file above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
