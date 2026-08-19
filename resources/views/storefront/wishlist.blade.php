@extends('layouts.storefrontMaster')

@section('title', __('My Wishlist') . ' — AK-Mart')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="bx bxs-heart text-danger me-2"></i> {{ __('My Wishlist') }}</h3>
            <p class="text-muted small mb-0">{{ __('Saved favorite grocery items and essentials for quick reordering') }}</p>
        </div>
        <a href="{{ route('storefront.shop') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bx bx-cart me-1"></i> {{ __('Continue Shopping') }}
        </a>
    </div>

    @if($products->isEmpty())
        <div class="card p-5 text-center border shadow-xs rounded-4 bg-white">
            <div class="avatar avatar-xl bg-label-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="bx bx-heart fs-1 text-danger"></i>
            </div>
            <h4 class="fw-bold text-dark">{{ __('Your Wishlist is Empty') }}</h4>
            <p class="text-muted mb-4">{{ __('Explore our supermarket aisles and tap the heart icon on any product to save it here.') }}</p>
            <div>
                <a href="{{ route('storefront.shop') }}" class="btn btn-primary rounded-pill px-4">{{ __('Explore Supermarket Catalog') }}</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $prod)
                <div class="col-6 col-md-4 col-lg-3" id="wishlistCard-{{ $prod->id }}">
                    <div class="product-card p-3 h-100 d-flex flex-column justify-content-between position-relative">
                        <div>
                            <div class="product-img-wrap rounded-3 mb-3 position-relative">
                                <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-xs border-0 p-1.5" onclick="removeFromWishlist({{ $prod->id }})" title="Remove from wishlist" style="z-index: 5;">
                                    <i class="bx bxs-trash text-danger fs-6 align-middle"></i>
                                </button>
                                <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $prod->name }}">
                            </div>

                            <span class="text-muted small d-block mb-1">{{ $prod->category?->name ?? __('General') }}</span>
                            <h6 class="fw-bold mb-2">
                                <a href="{{ route('storefront.product', $prod->id) }}" class="text-dark text-decoration-none text-truncate d-block">{{ $prod->name }}</a>
                            </h6>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-5 fw-bold text-primary">${{ number_format($prod->price, 2) }}</span>
                                <span class="badge badge-stock {{ $prod->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $prod->qty > 0 ? __('In Stock') : __('Sold Out') }}
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1 rounded-pill btn-sm d-flex align-items-center justify-content-center gap-1" onclick="moveWishlistToCart({{ $prod->id }})" {{ $prod->qty <= 0 ? 'disabled' : '' }}>
                                    <i class="bx bx-cart-add fs-5"></i>
                                    <span>{{ __('Move to Cart') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function removeFromWishlist(productId) {
    fetch('{{ route("storefront.wishlist.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById('wishlistCard-' + productId);
            if (card) card.remove();
            const badge = document.getElementById('wishlistBadge');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
            showToast('Item removed from wishlist', 'info');
            if (data.count === 0) {
                window.location.reload();
            }
        }
    });
}

function moveWishlistToCart(productId) {
    fetch('{{ route("storefront.cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, qty: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.totalItems;
            removeFromWishlist(productId);
            showToast('Moved item directly to your cart!', 'success');
        }
    });
}
</script>
@endsection
