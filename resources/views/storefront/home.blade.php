@extends('layouts.storefrontMaster')

@section('title', __('Online Supermarket & Fresh Grocery Delivery') . ' — AK-Mart')

@section('content')
<div class="container">
    <!-- Hero Slider Carousel -->
    @if($heroSliders->isNotEmpty())
        <div id="heroCarousel" class="carousel slide mb-5 rounded-4 overflow-hidden shadow-sm" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($heroSliders as $idx => $slide)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-current="{{ $idx === 0 ? 'true' : 'false' }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($heroSliders as $idx => $slide)
                    <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}" style="background: {{ $slide->bg_color ?: 'linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #0D9488 100%)' }}; min-height: 380px;">
                        <div class="container p-5 text-white">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill mb-3 fw-bold">
                                        <i class="bx bxs-zap me-1"></i>{{ $slide->badge_text ?: __('Express Delivery') }}
                                    </span>
                                    <h1 class="display-5 fw-bold text-white mb-3">{{ $slide->title }}</h1>
                                    <p class="lead text-white-50 mb-4">{{ $slide->subtitle ?: __('Shop farm-fresh produce, authentic spices, organic dairy, and pantry essentials.') }}</p>
                                    <div class="d-flex gap-3">
                                        <a href="{{ $slide->link_url ?: route('storefront.shop') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark">
                                            {{ $slide->button_text ?: __('Shop Now') }} <i class="bx bx-right-arrow-alt align-middle"></i>
                                        </a>
                                        <a href="{{ route('storefront.track') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">{{ __('Track Order') }}</a>
                                    </div>
                                </div>
                                <div class="col-lg-5 text-center d-none d-lg-block">
                                    <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart Mascot" class="img-fluid" style="max-height: 270px;" onerror="this.style.display='none'">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('Previous') }}</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('Next') }}</span>
            </button>
        </div>
    @else
        <!-- Fallback Hero Banner -->
        <div class="p-5 mb-5 rounded-4 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #0D9488 100%);">
            <div class="row align-items-center position-relative" style="z-index: 2;">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill mb-3 fw-bold"><i class="bx bxs-zap me-1"></i>{{ __('Same-Day Express Delivery') }}</span>
                    <h1 class="display-5 fw-bold text-white mb-3">{{ __('Fresh Groceries, Staples & Everyday Essentials') }}</h1>
                    <p class="lead text-white-50 mb-4">{{ __('Shop farm-fresh produce, authentic spices, organic dairy, and pantry essentials delivered directly in 30 minutes.') }}</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('storefront.shop') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark">{{ __('Explore Catalog') }} <i class="bx bx-right-arrow-alt align-middle"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Shop by Category Aisles -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">{{ __('Popular Grocery Aisles') }}</h3>
                <p class="text-muted small mb-0">{{ __('Browse our highest rated supermarket categories') }}</p>
            </div>
            <a href="{{ route('storefront.shop') }}" class="text-primary fw-semibold text-decoration-none small">{{ __('View All Aisles') }} <i class="bx bx-chevron-right align-middle"></i></a>
        </div>

        <div class="row g-3">
            @forelse($featuredCategories as $category)
                <div class="col-6 col-md-3">
                    <a href="{{ route('storefront.shop', ['category' => $category->id]) }}" class="text-decoration-none text-dark">
                        <div class="card h-100 p-3 text-center border shadow-xs rounded-3 hover-shadow transition">
                            <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bx {{ $category->icon ?: 'bx-basket' }} fs-3"></i>
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

    <!-- Merchandising Showcase: Trending & Best Sellers -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">{{ __('Hand-Picked Merchandising Specials') }}</h3>
                <p class="text-muted small mb-0">{{ __('Freshly curated selections for your kitchen & household') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('storefront.shop', ['collection' => 'trending']) }}" class="btn btn-sm btn-outline-danger rounded-pill"><i class="bx bxs-flame me-1"></i> {{ __('Trending') }}</a>
                <a href="{{ route('storefront.shop', ['collection' => 'bestseller']) }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bx bxs-trophy me-1"></i> {{ __('Best Sellers') }}</a>
                <a href="{{ route('storefront.shop', ['collection' => 'deals']) }}" class="btn btn-sm btn-outline-success rounded-pill"><i class="bx bxs-discount me-1"></i> {{ __('Deals') }}</a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($featuredProducts as $prod)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100 d-flex flex-column justify-content-between p-3 position-relative">
                        <div>
                            <div class="product-img-wrap rounded-3 mb-3 position-relative">
                                @if($prod->is_trending)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2"><i class="bx bxs-flame"></i> Hot</span>
                                @elseif($prod->deal_of_the_day)
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2"><i class="bx bxs-zap"></i> Deal</span>
                                @endif
                                <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-xs border-0 p-1.5" onclick="quickToggleWishlist({{ $prod->id }}, this, event)" style="z-index: 5;" title="{{ __('Save to Wishlist') }}">
                                    <i class="bx {{ in_array($prod->id, session('wishlist', [])) ? 'bxs-heart text-danger' : 'bx-heart text-muted' }} fs-5 align-middle"></i>
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

    <!-- Trust Badges & Guarantees -->
    <div class="card p-4 border shadow-xs rounded-4 bg-white mb-5">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="avatar avatar-md bg-label-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                    <i class="bx bx-cycling fs-4"></i>
                </div>
                <h6 class="fw-bold mb-1">{{ __('30-Min Fast Delivery') }}</h6>
                <small class="text-muted">{{ __('Express doorstep dispatch from nearest local branch') }}</small>
            </div>
            <div class="col-md-3">
                <div class="avatar avatar-md bg-label-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                    <i class="bx bx-check-shield fs-4"></i>
                </div>
                <h6 class="fw-bold mb-1">{{ __('100% Quality Guaranteed') }}</h6>
                <small class="text-muted">{{ __('Farm fresh products with verified inspection check') }}</small>
            </div>
            <div class="col-md-3">
                <div class="avatar avatar-md bg-label-warning rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                    <i class="bx bx-gift fs-4"></i>
                </div>
                <h6 class="fw-bold mb-1">{{ __('Loyalty Rewards') }}</h6>
                <small class="text-muted">{{ __('Earn points on every purchase to redeem instant discounts') }}</small>
            </div>
            <div class="col-md-3">
                <div class="avatar avatar-md bg-label-info rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                    <i class="bx bx-wallet fs-4"></i>
                </div>
                <h6 class="fw-bold mb-1">{{ __('Store Credit Wallet') }}</h6>
                <small class="text-muted">{{ __('Instant 1-click checkout with automated return refunds') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
