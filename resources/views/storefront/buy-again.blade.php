@extends('layouts.storefrontMaster')

@section('title', __('Buy Again — Fast Grocery Reorder') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="bx bx-repeat text-primary me-2"></i> {{ __('Buy Again & Quick Reorder Hub') }}</h3>
            <p class="text-muted mb-0">
                @if(!$isGuest)
                    {{ __('Easily restock items from your previous orders in 1 click.') }}
                @else
                    {{ __('Popular supermarket essentials and frequently reordered groceries.') }}
                @endif
            </p>
        </div>
        <a href="{{ route('storefront.shop') }}" class="btn btn-outline-primary rounded-pill btn-sm">
            <i class="bx bx-store me-1"></i> {{ __('Explore Full Catalog') }}
        </a>
    </div>

    @if($products->isEmpty())
        <div class="card p-5 text-center border shadow-xs rounded-4">
            <i class="bx bx-shopping-bag fs-1 text-muted mb-3"></i>
            <h4 class="fw-bold text-muted">{{ __('No previously ordered items yet') }}</h4>
            <p class="text-muted">{{ __('Once you place orders on AK-Mart, your frequently bought groceries will appear here for fast 1-click reordering.') }}</p>
            <div>
                <a href="{{ route('storefront.shop') }}" class="btn btn-primary rounded-pill px-4 mt-2">{{ __('Browse Catalog') }}</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($products as $prod)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100 d-flex flex-column justify-content-between p-3 position-relative">
                        <!-- Wishlist Toggle -->
                        <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-xs border-0 p-1.5" onclick="quickToggleWishlist({{ $prod->id }}, this, event)" style="z-index: 5;" title="{{ __('Save to Wishlist') }}">
                            <i class="bx {{ in_array($prod->id, session('wishlist', [])) ? 'bxs-heart text-danger' : 'bx-heart text-muted' }} fs-5 align-middle"></i>
                        </button>

                        <div>
                            <div class="product-img-wrap rounded-3 mb-3 position-relative">
                                <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $prod->name }}">
                            </div>

                            @if(isset($prod->purchase_qty) && $prod->purchase_qty > 0)
                                <div class="badge bg-label-info mb-2 small d-inline-flex align-items-center">
                                    <i class="bx bx-check me-1"></i> {{ __('Bought :count times', ['count' => $prod->purchase_qty]) }}
                                </div>
                            @else
                                <div class="badge bg-label-primary mb-2 small d-inline-flex align-items-center">
                                    <i class="bx bx-star me-1"></i> {{ __('Top Essential') }}
                                </div>
                            @endif

                            <span class="text-muted small d-block mb-1">{{ $prod->category?->name ?? __('Supermarket') }}</span>
                            <h6 class="fw-bold mb-2">
                                <a href="{{ route('storefront.product', $prod->id) }}" class="text-dark text-decoration-none text-truncate d-block">{{ $prod->name }}</a>
                            </h6>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fs-5 fw-bold text-primary">${{ number_format($prod->price, 2) }}</span>
                                <span class="badge {{ $prod->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }} small">
                                    {{ $prod->qty > 0 ? __('In Stock') : __('Out of Stock') }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-primary w-100 rounded-pill btn-sm d-flex align-items-center justify-content-center gap-1" onclick="quickAddToCart({{ $prod->id }})" {{ $prod->qty <= 0 ? 'disabled' : '' }}>
                                    <i class="bx bx-cart-add fs-5"></i>
                                    <span>{{ __('Buy Again') }}</span>
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
