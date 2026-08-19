@extends('layouts/layoutMaster')

@section('title', __('Product Recommendations & Suggestions') . ' — ' . $product->name . ' | AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('app-merchandising') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Merchandising') }}
        </a>
        <h4 class="fw-bold mb-1"><i class="bx bx-git-merge text-primary me-2"></i> {{ __('Product Relationships & Suggestions') }}</h4>
        <p class="text-muted small mb-0">{{ __('Configure cross-sells, related aisles, and "Frequently Bought Together" bundles for') }} <strong>{{ $product->name }}</strong></p>
    </div>
    <div>
        <a href="{{ route('storefront.product', $product->id) }}" target="_blank" class="btn btn-primary rounded-pill">
            <i class="bx bx-show me-1"></i> {{ __('View on Storefront') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Target Product Summary & Add Relation Form -->
    <div class="col-lg-4">
        <!-- Target Product Card -->
        <div class="card p-3 border shadow-sm rounded-4 mb-4">
            <div class="text-center mb-3">
                <img src="{{ $product->image ? asset($product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" class="rounded object-fit-contain bg-light p-2" width="120" height="120">
                <h5 class="fw-bold mt-2 mb-1">{{ $product->name }}</h5>
                <span class="badge bg-label-primary">{{ $product->category?->name ?? 'General' }}</span>
                <div class="fs-5 fw-bold text-primary mt-2">${{ number_format($product->price, 2) }}</div>
                <small class="text-muted">SKU: {{ $product->sku }} | Qty: {{ $product->qty }}</small>
            </div>
        </div>

        <!-- Add Recommendation Form -->
        <div class="card p-4 border shadow-sm rounded-4">
            <h5 class="fw-bold mb-3"><i class="bx bx-plus-circle text-primary me-1"></i> {{ __('Add Recommendation') }}</h5>
            <form action="{{ route('app-product-relations-store', $product->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ __('Select Product from Catalog') }}</label>
                    <select name="related_id" class="form-select" required>
                        <option value="">-- {{ __('Choose Item') }} --</option>
                        @foreach($allProducts as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (${{ number_format($item->price, 2) }}) - SKU: {{ $item->sku }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Recommendation Type') }}</label>
                    <select name="type" class="form-select" required>
                        <option value="suggested">⚡ {{ __('Frequently Bought Together (Bundle)') }}</option>
                        <option value="related">🔗 {{ __('Related Product (Similar Category)') }}</option>
                        <option value="cross_sell">🛒 {{ __('Cross-Sell (Cart Upgrade)') }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bx bx-check me-1"></i> {{ __('Link Product') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Linked Relations by Type -->
    <div class="col-lg-8">
        <!-- 1. Frequently Bought Together (Suggested) -->
        <div class="card border shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-success"><i class="bx bxs-zap me-1"></i> {{ __('Frequently Bought Together Bundles') }} ({{ $product->suggestedProducts->count() }})</h6>
                <small class="text-muted">{{ __('Displayed as interactive 1-click bundle cards on Storefront') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->suggestedProducts as $rel)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $rel->image ? asset($rel->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="40" height="40" class="rounded object-fit-contain bg-light p-1">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $rel->name }}</h6>
                                            <small class="text-muted">SKU: {{ $rel->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><strong>${{ number_format($rel->price, 2) }}</strong></td>
                                <td><span class="badge {{ $rel->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }}">{{ $rel->qty }}</span></td>
                                <td class="text-end">
                                    <form action="{{ route('app-product-relations-destroy', ['id' => $product->id, 'relatedId' => $rel->id, 'type' => 'suggested']) }}" method="POST" onsubmit="return confirm('Remove bundle recommendation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0"><i class="bx bx-trash fs-5"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">{{ __('No bundle suggestions configured yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Related Products -->
        <div class="card border shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-primary"><i class="bx bx-link me-1"></i> {{ __('Related Products') }} ({{ $product->relatedProducts->count() }})</h6>
                <small class="text-muted">{{ __('Displayed in "You May Also Like" carousel on Storefront') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->relatedProducts as $rel)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $rel->image ? asset($rel->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="40" height="40" class="rounded object-fit-contain bg-light p-1">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $rel->name }}</h6>
                                            <small class="text-muted">SKU: {{ $rel->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><strong>${{ number_format($rel->price, 2) }}</strong></td>
                                <td><span class="badge {{ $rel->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }}">{{ $rel->qty }}</span></td>
                                <td class="text-end">
                                    <form action="{{ route('app-product-relations-destroy', ['id' => $product->id, 'relatedId' => $rel->id, 'type' => 'related']) }}" method="POST" onsubmit="return confirm('Remove related recommendation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0"><i class="bx bx-trash fs-5"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">{{ __('No explicit related products linked. Storefront will auto-recommend from same category.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
