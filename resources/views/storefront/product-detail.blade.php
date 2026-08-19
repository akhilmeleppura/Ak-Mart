@extends('layouts.storefrontMaster')

@section('title', $product->name . ' — AK-Mart')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}" class="text-decoration-none">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('storefront.shop') }}" class="text-decoration-none">{{ __('Shop') }}</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('storefront.shop', ['category' => $product->category->id]) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Overview Grid -->
    <div class="row g-5 mb-5">
        <!-- Gallery Images -->
        <div class="col-lg-5">
            <div class="card p-4 border shadow-xs rounded-4 text-center bg-white">
                <img src="{{ $product->image ? asset($product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" id="mainProductImg" class="img-fluid rounded-3 object-fit-contain" style="max-height: 380px;" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-7">
            <span class="badge bg-label-primary px-3 py-1.5 rounded-pill mb-2 fw-semibold">{{ $product->category?->name ?? __('Grocery') }}</span>
            <h2 class="fw-bold mb-2">{{ $product->name }}</h2>

            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="text-warning small">
                    <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star-half"></i>
                </div>
                <span class="text-muted small">({{ $product->reviews->count() }} {{ __('verified customer reviews') }})</span>
                <span class="text-muted small">| SKU: <strong>{{ $product->sku }}</strong></span>
            </div>

            <!-- Price & Stock -->
            <div class="d-flex align-items-baseline gap-3 mb-4">
                <span class="display-6 fw-bold text-primary">${{ number_format($product->price, 2) }}</span>
                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="text-decoration-line-through text-muted fs-5">${{ number_format($product->compare_at_price, 2) }}</span>
                    <span class="badge bg-danger rounded-pill">{{ __('Save') }} ${{ number_format($product->compare_at_price - $product->price, 2) }}</span>
                @endif
            </div>

            <div class="mb-4">
                @if($availableStock > 5)
                    <span class="badge bg-label-success px-3 py-1.5 rounded-pill"><i class="bx bx-check-circle me-1"></i> {{ __('In Stock') }} ({{ $availableStock }} {{ __('available') }})</span>
                @elseif($availableStock > 0)
                    <span class="badge bg-label-warning px-3 py-1.5 rounded-pill"><i class="bx bx-error-circle me-1"></i> {{ __('Low Stock') }} ({{ __('Only') }} {{ $availableStock }} {{ __('left!') }})</span>
                @else
                    <span class="badge bg-label-danger px-3 py-1.5 rounded-pill"><i class="bx bx-x-circle me-1"></i> {{ __('Sold Out / Backorder Available') }}</span>
                @endif
            </div>

            <p class="text-muted mb-4 lead fs-6">{{ $product->description ?: __('Fresh hand-picked supermarket quality guaranteed. Certified organic and verified for daily essentials.') }}</p>

            <!-- Quantity & Actions -->
            <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                <div class="input-group" style="width: 140px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                    <input type="number" id="detailQty" class="form-control text-center fw-bold" value="1" min="1" max="{{ max(1, $availableStock) }}">
                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                </div>
                <button class="btn btn-primary btn-lg rounded-pill px-4 flex-grow-1 d-flex align-items-center justify-content-center gap-2" onclick="addDetailToCart({{ $product->id }})" {{ $availableStock <= 0 ? 'disabled' : '' }}>
                    <i class="bx bx-cart-add fs-4"></i>
                    <span>{{ __('Add to Cart') }}</span>
                </button>
            </div>

            <!-- Perks -->
            <div class="border-top pt-3 text-muted small d-flex flex-column gap-2">
                <div><i class="bx bx-check text-success me-2 fs-5 align-middle"></i> {{ __('30-Minute Doorstep Delivery Available') }}</div>
                <div><i class="bx bx-check text-success me-2 fs-5 align-middle"></i> {{ __('Earn Loyalty Points with this purchase') }}</div>
                <div><i class="bx bx-check text-success me-2 fs-5 align-middle"></i> {{ __('Hassle-Free Return & Instant Store Credit Refund') }}</div>
            </div>
        </div>
    </div>

    <!-- Frequently Bought Together Bundle Card -->
    @if($frequentlyBought->isNotEmpty())
        @php
            $bundleTotal = $product->price + $frequentlyBought->sum('price');
        @endphp
        <div class="card border-primary border shadow-sm rounded-4 p-4 mb-5 bg-primary bg-opacity-10">
            <h4 class="fw-bold mb-3 text-primary"><i class="bx bxs-zap me-2"></i> {{ __('Frequently Bought Together (Recommended Bundle)') }}</h4>
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <!-- Main Product -->
                        <div class="bg-white p-2 rounded-3 border text-center" style="width: 120px;">
                            <img src="{{ $product->image ? asset($product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="60" height="60" class="object-fit-contain">
                            <div class="fw-bold small text-truncate mt-1">{{ $product->name }}</div>
                            <span class="text-primary fw-bold small">${{ number_format($product->price, 2) }}</span>
                        </div>

                        <span class="fs-4 fw-bold text-muted">+</span>

                        <!-- Suggested Bundle Products -->
                        @foreach($frequentlyBought as $fb)
                            <div class="bg-white p-2 rounded-3 border text-center" style="width: 120px;">
                                <img src="{{ $fb->image ? asset($fb->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="60" height="60" class="object-fit-contain">
                                <div class="fw-bold small text-truncate mt-1">{{ $fb->name }}</div>
                                <span class="text-primary fw-bold small">${{ number_format($fb->price, 2) }}</span>
                            </div>
                            @if(!$loop->last)
                                <span class="fs-4 fw-bold text-muted">+</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="text-muted small mb-1">{{ __('Total Bundle Price:') }}</div>
                    <div class="display-6 fw-bold text-primary mb-2">${{ number_format($bundleTotal, 2) }}</div>
                    <button class="btn btn-primary rounded-pill px-4 fw-bold" onclick="addBundleToCart([{{ $product->id }}, {{ $frequentlyBought->pluck('id')->implode(',') }}])">
                        <i class="bx bx-cart-add me-1"></i> {{ __('Add Bundle to Cart') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">{{ __('Related Products You May Also Like') }}</h4>
                <a href="{{ route('storefront.shop') }}" class="text-primary text-decoration-none small">{{ __('View All') }} <i class="bx bx-chevron-right"></i></a>
            </div>
            <div class="row g-3">
                @foreach($relatedProducts as $rel)
                    <div class="col-6 col-md-3">
                        <div class="product-card p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="product-img-wrap rounded-3 mb-2">
                                    <img src="{{ $rel->image ? asset($rel->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $rel->name }}">
                                </div>
                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('storefront.product', $rel->id) }}" class="text-dark text-decoration-none text-truncate d-block">{{ $rel->name }}</a>
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-primary">${{ number_format($rel->price, 2) }}</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="quickAddToCart({{ $rel->id }})"><i class="bx bx-plus"></i></button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function adjustQty(amount) {
    const input = document.getElementById('detailQty');
    let val = parseInt(input.value) + amount;
    if (val < 1) val = 1;
    input.value = val;
}

function addDetailToCart(productId) {
    const qty = parseInt(document.getElementById('detailQty').value) || 1;
    fetch('{{ route("storefront.cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, qty: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.totalItems;
            alert(data.message);
        }
    });
}

function addBundleToCart(productIds) {
    let promises = productIds.map(id => {
        return fetch('{{ route("storefront.cart.add") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_id: id, qty: 1 })
        }).then(r => r.json());
    });

    Promise.all(promises).then(results => {
        const lastResult = results[results.length - 1];
        const badge = document.getElementById('cartBadge');
        if (badge && lastResult) badge.textContent = lastResult.totalItems;
        alert('All bundle items added to your cart!');
    });
}
</script>
@endsection
