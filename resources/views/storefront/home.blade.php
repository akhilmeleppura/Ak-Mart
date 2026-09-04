@extends('layouts.storefrontMaster')

@section('title', __('Online Supermarket & Fresh Grocery Delivery') . ' — AK-Mart')

@section('styles')
<style>
    /* Home Page Vibrant Highlighting */
    .hero-banner-vibrant {
        background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4338CA 100%);
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(49, 46, 129, 0.4);
    }
    .hero-circle-glow {
        position: absolute;
        width: 380px;
        height: 380px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(99, 102, 241, 0) 70%);
        top: -60px;
        right: -60px;
        pointer-events: none;
    }
    .category-highlight-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 22px;
        padding: 22px 16px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        display: block;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .category-highlight-card:hover {
        transform: translateY(-6px);
        border-color: #818CF8;
        box-shadow: 0 16px 30px -8px rgba(79, 70, 229, 0.18);
    }
    .category-icon-halo {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 28px;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .category-highlight-card:hover .category-icon-halo {
        transform: scale(1.14) rotate(5deg);
    }
    .badge-deal-flame {
        background: linear-gradient(135deg, #EF4444 0%, #F97316 100%);
        color: #FFFFFF;
        font-weight: 800;
        font-size: 11px;
        padding: 5px 12px;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .feature-strip-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.25s;
    }
    .feature-strip-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.06);
    }
    /* Hero Carousel Sleek Glassmorphism Controls */
    #heroCarousel .carousel-control-prev,
    #heroCarousel .carousel-control-next {
        width: 46px;
        height: 46px;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(10px);
        border: 1.5px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.85;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 15;
    }
    #heroCarousel .carousel-control-prev {
        left: 18px;
    }
    #heroCarousel .carousel-control-next {
        right: 18px;
    }
    #heroCarousel .carousel-control-prev:hover,
    #heroCarousel .carousel-control-next:hover {
        background: rgba(79, 70, 229, 0.9);
        border-color: #FFFFFF;
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
    }
    .hero-slide-container {
        padding: 48px 60px;
    }
    @media (max-width: 768px) {
        .hero-slide-container {
            padding: 32px 20px;
        }
        #heroCarousel .carousel-control-prev,
        #heroCarousel .carousel-control-next {
            display: none;
        }
    }
    .hero-mascot-glow {
        max-height: 280px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        padding: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        transition: transform 0.4s ease;
    }
    .hero-mascot-glow:hover {
        transform: translateY(-4px) scale(1.03);
    }
</style>
@endsection

@section('content')
<div class="container">

    <!-- Hero Slider Carousel with High-Impact Visuals -->
    @if($heroSliders->isNotEmpty())
        <div id="heroCarousel" class="carousel slide mb-5 rounded-5 overflow-hidden shadow-lg position-relative" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($heroSliders as $idx => $slide)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-current="{{ $idx === 0 ? 'true' : 'false' }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($heroSliders as $idx => $slide)
                    <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}" style="background: {{ $slide->bg_color ?: 'linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4338CA 100%)' }}; min-height: 420px;">
                        <div class="hero-slide-container text-white">
                            <div class="row align-items-center py-3">
                                <div class="col-lg-7">
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill mb-3 fw-bold shadow-xs">
                                        <i class="bx bxs-zap me-1"></i>{{ $slide->badge_text ?: __('Same-Day Express Delivery') }}
                                    </span>
                                    <h1 class="display-4 fw-bolder text-white mb-3 lh-sm">{{ $slide->title }}</h1>
                                    <p class="lead text-white-50 mb-4 fs-6">{{ $slide->subtitle ?: __('Shop farm-fresh produce, authentic spices, organic dairy, and pantry essentials delivered directly to your door in 30 minutes.') }}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <a href="{{ $slide->link_url ?: route('storefront.shop') }}" class="btn btn-warning btn-lg rounded-pill px-4.5 py-2.5 fw-bolder text-dark shadow-sm">
                                            {{ $slide->button_text ?: __('Shop Catalog') }} <i class="bx bx-right-arrow-alt align-middle fs-5"></i>
                                        </a>
                                        <a href="{{ route('storefront.buy_again') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-2.5 fw-bold">
                                            <i class="bx bx-repeat me-1"></i>{{ __('Buy Again') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-5 text-center d-none d-lg-block position-relative">
                                    <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart Mascot" class="img-fluid hero-mascot-glow" onerror="this.style.display='none'">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <i class="bx bx-chevron-left fs-3 text-white"></i>
                <span class="visually-hidden">{{ __('Previous') }}</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <i class="bx bx-chevron-right fs-3 text-white"></i>
                <span class="visually-hidden">{{ __('Next') }}</span>
            </button>
        </div>
    @else
        <!-- Fallback Vibrant Hero Banner -->
        <div class="hero-banner-vibrant p-5 mb-5 text-white">
            <div class="hero-circle-glow"></div>
            <div class="row align-items-center position-relative" style="z-index: 2;">
                <div class="col-lg-7">
                    <span class="badge bg-emerald-500 bg-opacity-20 text-success px-3 py-1.5 rounded-pill mb-3 fw-bold border border-success" style="background: rgba(16,185,129,0.2); color: #34D399;">
                        <i class="bx bxs-zap me-1"></i> {{ __('30-Minute Guaranteed Grocery Delivery') }}
                    </span>
                    <h1 class="display-4 fw-bolder text-white mb-3">{{ __('Fresh Groceries, Organic Staples & Essentials') }}</h1>
                    <p class="lead text-white-50 mb-4 fs-6">{{ __('Explore thousands of farm-fresh produce items, imported snacks, pantry items, and household goods at guaranteed lowest market prices.') }}</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('storefront.shop') }}" class="btn btn-warning btn-lg rounded-pill px-4.5 py-2.5 fw-bolder text-dark shadow-sm">
                            {{ __('Explore Supermarket') }} <i class="bx bx-right-arrow-alt align-middle fs-5"></i>
                        </a>
                        <a href="{{ route('storefront.referral') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-2.5 fw-bold">
                            <i class="bx bx-gift me-1"></i> {{ __('Refer & Earn $10') }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart Mascot" class="img-fluid" style="max-height: 280px; filter: drop-shadow(0 15px 25px rgba(0,0,0,0.3));" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    @endif

    <!-- Trust & Quality Feature Highlights Strip -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3">
            <div class="feature-strip-card">
                <div class="avatar bg-success bg-opacity-10 text-success p-3 rounded-circle fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bx bx-rocket"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ __('30-Min Delivery') }}</h6>
                    <small class="text-muted">{{ __('Direct to your doorstep') }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-strip-card">
                <div class="avatar bg-primary bg-opacity-10 text-primary p-3 rounded-circle fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bx bx-check-shield"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ __('100% Quality') }}</h6>
                    <small class="text-muted">{{ __('Organic & Verified Stock') }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-strip-card">
                <div class="avatar bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bx bx-gift"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ __('Earn $10 Wallet') }}</h6>
                    <small class="text-muted">{{ __('On every friend referral') }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-strip-card">
                <div class="avatar bg-danger bg-opacity-10 text-danger p-3 rounded-circle fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bx bx-revision"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ __('Easy Returns') }}</h6>
                    <small class="text-muted">{{ __('1-Click instant portal') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop by Category Aisles with Color Halos -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bolder mb-1 text-dark">{{ __('Shop Popular Aisles') }}</h3>
                <p class="text-muted small mb-0">{{ __('Browse fresh supermarket departments with live stock counts') }}</p>
            </div>
            <a href="{{ route('storefront.shop') }}" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-bold small">
                {{ __('View All Aisles') }} <i class="bx bx-chevron-right align-middle"></i>
            </a>
        </div>

        <div class="row g-3.5">
            @php
                $haloColors = [
                    ['bg' => '#ECFDF5', 'text' => '#10B981', 'icon' => '🍎'],
                    ['bg' => '#EFF6FF', 'text' => '#3B82F6', 'icon' => '🥤'],
                    ['bg' => '#FFFBEB', 'text' => '#F59E0B', 'icon' => '🧀'],
                    ['bg' => '#FFF1F2', 'text' => '#F43F5E', 'icon' => '🥐'],
                    ['bg' => '#F5F3FF', 'text' => '#8B5CF6', 'icon' => '🍫'],
                    ['bg' => '#F0FDFA', 'text' => '#14B8A6', 'icon' => '🌾'],
                    ['bg' => '#FEF2F2', 'text' => '#EF4444', 'icon' => '🥩'],
                    ['bg' => '#F1F5F9', 'text' => '#475569', 'icon' => '🧼'],
                ];
            @endphp
            @forelse($featuredCategories as $idx => $category)
                @php $color = $haloColors[$idx % count($haloColors)]; @endphp
                <div class="col-6 col-md-3">
                    <a href="{{ route('storefront.shop', ['category' => $category->id]) }}" class="category-highlight-card">
                        <div class="category-icon-halo" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                            <span>{{ $color['icon'] }}</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark text-truncate">{{ $category->name }}</h6>
                        <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1 small">{{ $category->products_count }} {{ __('Items') }}</span>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">{{ __('No categories found.') }}</div>
            @endforelse
        </div>
    </div>

    <!-- Hand-Picked Merchandising Specials -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <span class="badge-deal-flame mb-2"><i class="bx bxs-flame"></i> {{ __('SPECIAL MERCHANDISING') }}</span>
                <h3 class="fw-bolder mb-1 text-dark">{{ __('Featured & Trending Groceries') }}</h3>
                <p class="text-muted small mb-0">{{ __('Freshly curated selections with verified customer reviews & ratings') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('storefront.shop', ['collection' => 'trending']) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"><i class="bx bxs-flame me-1"></i> {{ __('Trending') }}</a>
                <a href="{{ route('storefront.shop', ['collection' => 'bestseller']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"><i class="bx bxs-trophy me-1"></i> {{ __('Best Sellers') }}</a>
                <a href="{{ route('storefront.shop', ['deals' => '1']) }}" class="btn btn-sm btn-gradient-primary rounded-pill px-3.5 fw-bold"><i class="bx bxs-discount me-1"></i> {{ __('Flash Deals') }}</a>
            </div>
        </div>

        <div class="row g-3.5">
            @forelse($featuredProducts as $prod)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-grid-card">
                        <div>
                            <!-- Image Frame Canvas with Floating Glass Icons -->
                            <div class="product-img-canvas">
                                @if($prod->is_trending)
                                    <span class="glass-badge-deal" style="background: linear-gradient(135deg, #EF4444 0%, #F97316 100%);">
                                        🔥 {{ __('Hot') }}
                                    </span>
                                @elseif($prod->deal_of_the_day || ($prod->compare_at_price && $prod->compare_at_price > $prod->price))
                                    <span class="glass-badge-deal">
                                        ⚡ {{ __('Deal') }}
                                    </span>
                                @endif

                                <!-- Compare Action Button -->
                                <div class="card-action-btn top-0 start-0 m-2.5" onclick="quickToggleCompare({{ $prod->id }}, this, event)" title="{{ __('Add to Compare') }}">
                                    <i class="bx {{ in_array($prod->id, session('compare_list', [])) ? 'bx-git-compare text-primary fw-bold' : 'bx-git-compare text-muted' }} fs-5 align-middle"></i>
                                </div>

                                <!-- Wishlist Action Button -->
                                <div class="card-action-btn top-0 end-0 m-2.5" onclick="quickToggleWishlist({{ $prod->id }}, this, event)" title="{{ __('Save to Wishlist') }}">
                                    <i class="bx {{ in_array($prod->id, session('wishlist', [])) ? 'bxs-heart text-danger' : 'bx-heart text-muted' }} fs-5 align-middle"></i>
                                </div>

                                <a href="{{ route('storefront.product', $prod->id) }}" class="d-flex align-items-center justify-content-center w-100 h-100">
                                    <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/ecommerce-images/product-1.png') }}" alt="{{ $prod->name }}" class="object-fit-contain p-2">
                                </a>
                            </div>

                            <!-- Meta Row: Category & Brand -->
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="text-muted small fw-semibold text-uppercase letter-spacing-1" style="font-size: 11px;">{{ $prod->category?->name ?? __('General') }}</span>
                                @if($prod->brand)
                                    <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5" style="font-size: 10.5px;">{{ $prod->brand }}</span>
                                @endif
                            </div>

                            <!-- Product Title -->
                            <h6 class="fw-bold mb-1 text-dark lh-sm">
                                <a href="{{ route('storefront.product', $prod->id) }}" class="text-dark text-decoration-none text-truncate d-block" title="{{ $prod->name }}">{{ $prod->name }}</a>
                            </h6>

                            <!-- Rating Row -->
                            @if($prod->rating_cache > 0)
                                <div class="d-flex align-items-center gap-1 small mb-2.5">
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 fw-bold d-flex align-items-center gap-1" style="font-size: 11px;">
                                        <i class="bx bxs-star text-white"></i> {{ number_format($prod->rating_cache, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Bottom Row: Price & Add to Cart -->
                        <div class="mt-2 pt-2 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-2.5">
                                <div>
                                    <span class="fs-5 fw-bolder text-dark">${{ number_format($prod->price, 2) }}</span>
                                    @if($prod->compare_at_price && $prod->compare_at_price > $prod->price)
                                        <span class="text-muted text-decoration-line-through small ms-1">${{ number_format($prod->compare_at_price, 2) }}</span>
                                    @endif
                                </div>
                                <span class="badge {{ $prod->qty > 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                    {{ $prod->qty > 0 ? "Stock: {$prod->qty}" : __('Sold Out') }}
                                </span>
                            </div>

                            <button class="btn btn-add-cart" onclick="quickAddToCart({{ $prod->id }})" {{ $prod->qty <= 0 ? 'disabled' : '' }}>
                                <i class="bx bx-shopping-bag fs-5"></i>
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
