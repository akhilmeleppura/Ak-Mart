@extends('layouts.storefrontMaster')

@section('title', __('Online Supermarket & Fresh Grocery Delivery') . ' — AK-Mart')

@section('content')
<div class="container">
    <!-- Hero Banner -->
    <div class="p-5 mb-4 rounded-4 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #0D9488 100%);">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill mb-3 fw-bold"><i class="bx bxs-zap me-1"></i>{{ __('Same-Day Express Delivery') }}</span>
                <h1 class="display-5 fw-bold text-white mb-3">{{ __('Fresh Groceries, Staples & Everyday Essentials') }}</h1>
                <p class="lead text-white-50 mb-4">{{ __('Shop farm-fresh produce, authentic spices, organic dairy, and pantry essentials delivered directly to your doorstep in 30 minutes.') }}</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('storefront.shop') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark">{{ __('Explore Catalog') }} <i class="bx bx-right-arrow-alt align-middle"></i></a>
                    <a href="{{ route('storefront.track') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">{{ __('Track Order') }}</a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart Mascot" class="img-fluid" style="max-height: 280px;" onerror="this.style.display='none'">
            </div>
        </div>
    </div>

    <!-- Shop by Category Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">{{ __('Popular Grocery Aisles') }}</h3>
                <p class="text-muted small mb-0">{{ __('Browse our highest rated supermarket categories') }}</p>
            </div>
            <a href="{{ route('storefront.shop') }}" class="text-primary fw-semibold text-decoration-none small">{{ __('View All Aisles') }} <i class="bx bx-chevron-right align-middle"></i></a>
        </div>

        <div class="row g-3">
            @forelse($categories as $category)
                <div class="col-6 col-md-3">
                    <a href="{{ route('storefront.shop', ['category' => $category->id]) }}" class="text-decoration-none text-dark">
                        <div class="card h-100 p-3 text-center border shadow-xs rounded-3 hover-shadow transition">
                            <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bx bx-basket fs-3"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $category->name }}</h6>
                            <span class="text-muted small">{{ $category->products_count }} {{ __('Products') }}</span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">{{ __('No categories found.') }}</div>
            @endforelse
        </div>
    </div>

    <!-- Featured Products Grid -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">{{ __('Featured & Trending Items') }}</h3>
                <p class="text-muted small mb-0">{{ __('Hand-picked supermarket quality with verified farm freshness') }}</p>
            </div>
            <a href="{{ route('storefront.shop') }}" class="text-primary fw-semibold text-decoration-none small">{{ __('See Full Collection') }} <i class="bx bx-chevron-right align-middle"></i></a>
        </div>

        <div class="row g-4">
            @forelse($featuredProducts as $prod)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100 d-flex flex-column justify-content-between p-3">
                        <div>
                            <div class="product-img-wrap rounded-3 mb-3 position-relative">
                                @if($prod->qty <= 0)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">{{ __('Out of Stock') }}</span>
                                @elseif($prod->qty <= 5)
                                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">{{ __('Low Stock') }}</span>
                                @endif
                                <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $prod->name }}">
                            </div>

                            <span class="text-muted small d-block mb-1">{{ $prod->category?->name ?? __('General') }}</span>
                            <h6 class="fw-bold mb-2">
                                <a href="{{ route('storefront.product', $prod->id) }}" class="text-dark text-decoration-none text-truncate d-block">{{ $prod->name }}</a>
                            </h6>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="fs-5 fw-bold text-primary">${{ number_format($prod->price, 2) }}</span>
                                </div>
                                <span class="badge badge-stock {{ $prod->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $prod->qty > 0 ? __('In Stock') : __('Sold Out') }}
                                </span>
                            </div>

                            <button class="btn btn-outline-primary w-100 rounded-pill btn-sm d-flex align-items-center justify-content-center gap-1" onclick="quickAddToCart({{ $prod->id }})" {{ $prod->qty <= 0 ? 'disabled' : '' }}>
                                <i class="bx bx-cart-add fs-5"></i>
                                <span>{{ __('Add to Cart') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">{{ __('No products available yet.') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
