@extends('layouts.storefrontMaster')

@section('title', $product->name . ' — AK-Mart')
@section('meta_description', Str::limit(strip_tags($product->description ?: $product->name), 150))

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('storefront.shop') }}">{{ __('Shop') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('storefront.shop', ['category' => $product->category_id]) }}">{{ $product->category?->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Main Info Card -->
    <div class="card p-4 border shadow-xs rounded-4 mb-5">
        <div class="row g-4 align-items-center">
            <!-- Product Gallery Column -->
            <div class="col-lg-5 text-center">
                <div class="bg-light p-4 rounded-3 d-flex align-items-center justify-content-center" style="min-height: 380px;">
                    <img src="{{ $product->image ? asset($product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 340px;">
                </div>
            </div>

            <!-- Product Details Column -->
            <div class="col-lg-7">
                <span class="badge bg-label-primary px-3 py-1.5 rounded-pill mb-2">{{ $product->category?->name ?? __('Grocery') }}</span>
                <h2 class="fw-bold mb-2">{{ $product->name }}</h2>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="text-warning"><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star-half"></i></span>
                    <span class="small text-muted">4.8 ({{ $product->reviews->count() ?: 12 }} {{ __('verified reviews') }})</span>
                    <span class="text-muted">|</span>
                    <span class="small text-muted">SKU: <strong>{{ $product->sku ?: 'SKU-' . $product->id }}</strong></span>
                </div>

                <div class="d-flex align-items-baseline gap-3 mb-4">
                    <h2 class="fw-bolder text-primary mb-0">${{ number_format($product->price, 2) }}</h2>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="text-muted text-decoration-line-through fs-5">${{ number_format($product->compare_price, 2) }}</span>
                    @endif
                    <span class="badge {{ $availableStock > 0 ? 'bg-success' : 'bg-danger' }} px-2.5 py-1">
                        {{ $availableStock > 0 ? __('In Stock') . " ({$availableStock} units)" : __('Currently Out of Stock') }}
                    </span>
                </div>

                <p class="text-muted mb-4 leading-relaxed">
                    {{ $product->description ?: __('Supermarket fresh quality guaranteed. Packed in hygienic conditions and backed by the AK-Mart 100% freshness pledge.') }}
                </p>

                <!-- Dynamic EAV Attributes & Variants -->
                @if($product->variants->isNotEmpty())
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">{{ __('Available Variant Options:') }}</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <span class="badge bg-white border text-dark px-3 py-2 rounded-3 shadow-xs">
                                    {{ $variant->attribute_name }}: {{ $variant->attribute_value }} (+${{ number_format($variant->price ?: 0, 2) }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add to Cart & Buy Now Actions -->
                <div class="d-flex gap-3 align-items-center mb-4">
                    <div class="input-group" style="max-width: 130px;">
                        <input type="number" id="detailQty" class="form-control text-center" value="1" min="1" max="{{ max(1, $availableStock) }}">
                    </div>
                    <button class="btn btn-primary btn-lg rounded-pill px-4 flex-grow-1" onclick="addDetailToCart({{ $product->id }})" {{ $availableStock <= 0 ? 'disabled' : '' }}>
                        <i class="bx bx-cart-add me-1 fs-5"></i> {{ __('Add to Basket') }}
                    </button>
                </div>

                <div class="border-top pt-3 text-muted small d-flex flex-wrap gap-4">
                    <span><i class="bx bx-shield-check text-success me-1"></i> {{ __('100% Quality Guaranteed') }}</span>
                    <span><i class="bx bx-time-five text-primary me-1"></i> {{ __('30-Min Express Delivery') }}</span>
                    <span><i class="bx bx-refresh text-info me-1"></i> {{ __('Easy 24-Hour Return Policy') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mb-5">
            <h4 class="fw-bold mb-3">{{ __('Frequently Bought Together') }}</h4>
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
</script>
@endsection
