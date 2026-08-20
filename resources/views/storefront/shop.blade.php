@extends('layouts.storefrontMaster')

@section('title', __('Shop Fresh Groceries, Essentials & Supermarket Products') . ' — AK-Mart')

@section('styles')
<style>
    /* Catalog Page Custom Theme Enhancements */
    .catalog-hero-bar {
        background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.03);
    }
    .filter-sidebar-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 22px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
    }
    .filter-section-title {
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: 8px;
    }
    .quick-filter-chip {
        padding: 7px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .quick-filter-chip:hover {
        border-color: #4F46E5;
        color: #4F46E5;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    }
    .quick-filter-chip.active-primary {
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
        color: #FFFFFF;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
    }
    .quick-filter-chip.active-deal {
        background: linear-gradient(135deg, #EF4444 0%, #F97316 100%);
        color: #FFFFFF;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.25);
    }
    .quick-filter-chip.active-organic {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: #FFFFFF;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    }
    .quick-filter-chip.active-amber {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: #FFFFFF;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);
    }

    /* Product Card Visuals */
    .product-grid-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 22px;
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .product-grid-card:hover {
        transform: translateY(-6px);
        border-color: rgba(79, 70, 229, 0.35);
        box-shadow: 0 20px 30px -10px rgba(79, 70, 229, 0.12), 0 8px 12px -4px rgba(0, 0, 0, 0.04);
    }
    .product-img-canvas {
        height: 180px;
        background: radial-gradient(circle at center, #F8FAFC 0%, #F1F5F9 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .product-img-canvas img {
        max-height: 80%;
        max-width: 80%;
        object-fit: contain;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .product-grid-card:hover .product-img-canvas img {
        transform: scale(1.08);
    }

    /* Floating Glass Badge */
    .glass-badge-deal {
        background: rgba(239, 68, 68, 0.9);
        backdrop-filter: blur(8px);
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 5;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }
    .glass-badge-organic {
        background: rgba(16, 185, 129, 0.9);
        backdrop-filter: blur(8px);
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 5;
    }

    /* Round Action Icon Buttons */
    .card-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        z-index: 6;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .card-action-btn:hover {
        background: #FFFFFF;
        transform: scale(1.14);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }

    /* Add to Cart Button */
    .btn-add-cart {
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 30px;
        font-size: 13.5px;
        font-weight: 700;
        padding: 9px 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.2);
    }
    .btn-add-cart:hover {
        background: linear-gradient(135deg, #4338CA 0%, #2563EB 100%);
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
    }
    .btn-add-cart:disabled {
        background: #E2E8F0;
        color: #94A3B8;
        box-shadow: none;
        cursor: not-allowed;
    }

    /* Active Filter Pill */
    .active-filter-pill {
        background: #EEF2FF;
        color: #4F46E5;
        border: 1px solid #C7D2FE;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .active-filter-pill:hover {
        background: #E0E7FF;
        color: #3730A3;
    }

    /* Range Slider Styling */
    .custom-range-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: #E2E8F0;
        outline: none;
        transition: background 0.2s;
    }
    .custom-range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #4F46E5;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4);
        border: 2px solid #FFFFFF;
        transition: transform 0.15s;
    }
    .custom-range-slider::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }
</style>
@endsection

@section('content')
<div class="container py-3">

    <!-- Top Quick Filters Carousel Bar -->
    @if(!empty($filterConfig['quick_filter_bar']))
        <div class="d-flex align-items-center gap-2 overflow-auto pb-3 mb-3 text-nowrap no-scrollbar">
            <span class="small fw-bolder text-muted me-1 text-uppercase letter-spacing-1 d-flex align-items-center gap-1">
                <i class="bx bx-bolt-circle text-primary fs-5"></i> {{ __('Quick:') }}
            </span>
            <a href="{{ route('storefront.shop') }}" class="quick-filter-chip {{ !request()->hasAny(['deals', 'deals_only', 'dietary', 'min_rating', 'max_price', 'in_stock']) ? 'active-primary' : '' }}">
                <i class="bx bx-grid-alt"></i> {{ __('All Items') }} ({{ $products->total() }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['deals' => '1']) }}" class="quick-filter-chip {{ request('deals') === '1' ? 'active-deal' : '' }}">
                ⚡ {{ __('Flash Deals') }} <span class="badge bg-white bg-opacity-25 rounded-pill ms-1">{{ $dealsCount }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['dietary' => 'Organic']) }}" class="quick-filter-chip {{ request('dietary') === 'Organic' ? 'active-organic' : '' }}">
                🌿 {{ __('100% Organic') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['min_rating' => '4']) }}" class="quick-filter-chip {{ request('min_rating') == '4' ? 'active-amber' : '' }}">
                ⭐ {{ __('4★ & Up Rated') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['in_stock' => '1']) }}" class="quick-filter-chip {{ request('in_stock') == '1' ? 'active-primary' : '' }}">
                📦 {{ __('In Stock Now') }} <span class="badge bg-white bg-opacity-25 rounded-pill ms-1">{{ $inStockCount }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['max_price' => '10']) }}" class="quick-filter-chip {{ request('max_price') == '10' ? 'active-deal' : '' }}">
                💰 {{ __('Under $10 Specials') }}
            </a>
        </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="filter-sidebar-card p-4 sticky-top" style="top: 24px;">
                <div class="d-flex justify-content-between align-items-center mb-3.5 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-1.5 rounded-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bx bx-slider-alt fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('Filter Catalog') }}</h5>
                    </div>
                    @if(request()->hasAny(['q', 'category', 'brands', 'brand', 'size', 'sizes', 'in_stock', 'min_price', 'max_price', 'min_rating', 'dietary', 'deals', 'deals_only', 'sort']))
                        <a href="{{ route('storefront.shop') }}" class="small text-danger fw-bold text-decoration-none">
                            <i class="bx bx-rotate-left me-0.5"></i>{{ __('Reset') }}
                        </a>
                    @endif
                </div>

                <form action="{{ route('storefront.shop') }}" method="GET" id="catalogFilterForm">
                    <!-- Keyword Search -->
                    @if(!empty($filterConfig['show_search']))
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Keyword Search') }}</div>
                            <div class="position-relative">
                                <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" name="q" class="form-control form-control-sm rounded-pill ps-5 bg-light border-0" placeholder="{{ __('Search name, SKU...') }}" value="{{ request('q') }}">
                                @if(request('q'))
                                    <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted"><i class="bx bx-x fs-5"></i></a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Aisle / Category Filter -->
                    @if(!empty($filterConfig['show_category']))
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Aisle Category') }}</div>
                            <select name="category" class="form-select form-select-sm rounded-3 bg-light border-0 fw-semibold" onchange="document.getElementById('catalogFilterForm').submit()">
                                <option value="">{{ __('All Aisles') }} ({{ $products->total() }})</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Brand / Producer Filter -->
                    @if(!empty($filterConfig['show_brand']) && !empty($availableBrands) && $availableBrands->isNotEmpty())
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="filter-section-title mb-0">{{ __('Brand / Producer') }}</div>
                                <span class="badge rounded-pill bg-light text-muted border small">{{ count($availableBrands) }}</span>
                            </div>
                            
                            <input type="text" class="form-control form-control-sm rounded-pill mb-2 bg-light border-0 px-3" placeholder="{{ __('Filter brand name...') }}" onkeyup="filterBrandList(this.value)">
                            
                            <div class="p-2 border rounded-3 bg-light bg-opacity-40" id="brandListContainer" style="max-height: 150px; overflow-y: auto;">
                                @php
                                    $selectedBrands = (array) request('brands', []);
                                    if (request('brand')) $selectedBrands[] = request('brand');
                                @endphp
                                @foreach($availableBrands as $brand)
                                    @php $cnt = $brandCounts[$brand] ?? 0; @endphp
                                    <div class="form-check small mb-1.5 brand-item">
                                        <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand }}" id="brand_{{ Str::slug($brand) }}" {{ in_array($brand, $selectedBrands) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex justify-content-between align-items-center text-truncate w-100 fw-medium" for="brand_{{ Str::slug($brand) }}">
                                            <span class="text-truncate">{{ $brand }}</span>
                                            <span class="text-muted ms-1 small">({{ $cnt }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Size & Package Options Filter -->
                    @if(!empty($filterConfig['show_size']) && !empty($filterConfig['size_options']))
                        <div class="mb-4">
                            <div class="filter-section-title d-flex justify-content-between align-items-center">
                                <span>{{ __('Size & Options') }}</span>
                                @if(request('size') || request('sizes'))
                                    <a href="{{ request()->fullUrlWithQuery(['size' => null, 'sizes' => null]) }}" class="text-danger small text-decoration-none fw-normal">{{ __('Clear') }}</a>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-1.5">
                                @php
                                    $availableSizes = array_map('trim', explode(',', $filterConfig['size_options']));
                                    $selectedSizes = (array) (request('sizes') ?: (request('size') ? [request('size')] : []));
                                @endphp
                                @foreach($availableSizes as $sz)
                                    @php $isSelected = in_array($sz, $selectedSizes); @endphp
                                    <a href="{{ $isSelected ? request()->fullUrlWithQuery(['size' => null, 'sizes' => array_diff($selectedSizes, [$sz]) ?: null]) : request()->fullUrlWithQuery(['size' => $sz]) }}" class="badge rounded-pill px-3 py-1.5 text-decoration-none fw-bold {{ $isSelected ? 'bg-primary text-white shadow-xs' : 'bg-light text-dark border' }}" style="font-size: 11.5px; transition: all 0.2s;">
                                        {{ $sz }} {{ $isSelected ? '✓' : '' }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Dual Range Price Slider -->
                    @if(!empty($filterConfig['show_price']))
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="filter-section-title mb-0">{{ __('Price Range') }}</div>
                                <span class="small fw-bolder text-primary" id="priceDisplayRange">
                                    ${{ request('min_price', $filterConfig['price_min_limit'] ?? 0) }} - ${{ request('max_price', $filterConfig['price_max_limit'] ?? 100) }}
                                </span>
                            </div>
                            
                            <div class="my-2.5">
                                <input type="range" class="custom-range-slider" id="priceRangeSlider" min="{{ $filterConfig['price_min_limit'] ?? 0 }}" max="{{ $filterConfig['price_max_limit'] ?? 100 }}" value="{{ request('max_price', $filterConfig['price_max_limit'] ?? 100) }}" step="1" oninput="updatePriceSlider(this.value)">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-0 small">$</span>
                                    <input type="number" name="min_price" id="minPriceInput" class="form-control bg-light border-0 small" placeholder="Min" value="{{ request('min_price') }}" step="0.5" min="0" onchange="syncPriceInputs()">
                                </div>
                                <span class="text-muted font-monospace">-</span>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-0 small">$</span>
                                    <input type="number" name="max_price" id="maxPriceInput" class="form-control bg-light border-0 small" placeholder="Max" value="{{ request('max_price') }}" step="0.5" min="0" onchange="syncPriceInputs()">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Dietary & Features Badges -->
                    @if(!empty($filterConfig['show_dietary']) && !empty($filterConfig['dietary_tags']))
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Dietary & Features') }}</div>
                            <div class="d-flex flex-wrap gap-1.5">
                                @php
                                    $tags = array_map('trim', explode(',', $filterConfig['dietary_tags']));
                                @endphp
                                @foreach($tags as $tag)
                                    @php $isActive = request('dietary') === $tag; @endphp
                                    <a href="{{ $isActive ? request()->fullUrlWithQuery(['dietary' => null]) : request()->fullUrlWithQuery(['dietary' => $tag]) }}" class="badge rounded-pill px-2.5 py-1.5 text-decoration-none fw-semibold {{ $isActive ? 'bg-success text-white shadow-xs' : 'bg-light text-dark border' }}">
                                        {{ $tag }} {{ $isActive ? '✓' : '' }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Customer Rating Filter -->
                    @if(!empty($filterConfig['show_rating']))
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Customer Rating') }}</div>
                            <div class="form-check small mb-1.5">
                                <input class="form-check-input" type="radio" name="min_rating" value="4" id="rating4" {{ request('min_rating') == '4' ? 'checked' : '' }}>
                                <label class="form-check-label text-warning d-flex align-items-center gap-1" for="rating4">
                                    <span><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star text-muted"></i></span>
                                    <span class="text-dark ms-1 fw-bold">4.0 &amp; Up</span>
                                </label>
                            </div>
                            <div class="form-check small mb-1.5">
                                <input class="form-check-input" type="radio" name="min_rating" value="3" id="rating3" {{ request('min_rating') == '3' ? 'checked' : '' }}>
                                <label class="form-check-label text-warning d-flex align-items-center gap-1" for="rating3">
                                    <span><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star text-muted"></i><i class="bx bx-star text-muted"></i></span>
                                    <span class="text-dark ms-1 fw-bold">3.0 &amp; Up</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- In-Stock & Deals Toggles -->
                    <div class="mb-4 pt-3 border-top">
                        @if(!empty($filterConfig['show_stock']))
                            <div class="form-check form-switch small mb-2">
                                <input type="checkbox" name="in_stock" value="1" class="form-check-input" id="inStockCheck" {{ request('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="inStockCheck">{{ __('In-Stock Only') }} ({{ $inStockCount }})</label>
                            </div>
                        @endif
                        @if(!empty($filterConfig['show_deals']))
                            <div class="form-check form-switch small">
                                <input type="checkbox" name="deals_only" value="1" class="form-check-input" id="dealsOnlyCheck" {{ request('deals_only') || request('deals') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="dealsOnlyCheck">⚡ {{ __('Flash Deals Only') }} ({{ $dealsCount }})</label>
                            </div>
                        @endif
                    </div>

                    <!-- Preserve Active State -->
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request('dietary')) <input type="hidden" name="dietary" value="{{ request('dietary') }}"> @endif
                    @if(request('view')) <input type="hidden" name="view" value="{{ request('view') }}"> @endif

                    <!-- Submit Button -->
                    <button class="btn btn-gradient-primary rounded-pill w-100 py-2 fw-bold" type="submit">
                        <i class="bx bx-filter-alt me-1"></i> {{ __('Apply Filters') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Products Catalog Grid -->
        <div class="col-lg-9">
            <!-- Catalog Hero Header -->
            <div class="catalog-hero-bar p-3.5 mb-3.5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <nav aria-label="breadcrumb" class="mb-1">
                            <ol class="breadcrumb small mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}" class="text-muted text-decoration-none">{{ __('Home') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('storefront.shop') }}" class="text-muted text-decoration-none">{{ __('Shop') }}</a></li>
                                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">{{ __('Catalog') }}</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bolder mb-0 text-dark">{{ __('Supermarket Products') }}</h4>
                        <span class="text-muted small fw-medium">{{ $products->total() }} {{ __('groceries & essentials found') }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if(!empty($filterConfig['grid_list_toggle']))
                            <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group">
                                <button type="button" class="btn btn-sm rounded-pill px-3 active border-0" id="btnGridView" onclick="setCatalogView('grid')" title="{{ __('Grid View') }}">
                                    <i class="bx bx-grid-alt fs-5 align-middle"></i>
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill px-3 border-0" id="btnListView" onclick="setCatalogView('list')" title="{{ __('List View') }}">
                                    <i class="bx bx-list-ul fs-5 align-middle"></i>
                                </button>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted fw-bold text-nowrap mb-0 d-none d-sm-inline">{{ __('Sort:') }}</label>
                            <select class="form-select form-select-sm rounded-pill bg-light border-0 fw-semibold px-3" style="width: 190px;" onchange="location = this.value;">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>{{ __('Newest Arrivals') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating_high']) }}" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>{{ __('Top Rated (5★)') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('Best Sellers & In Stock') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Active Filter Chips Bar -->
                @if(request()->hasAny(['q', 'category', 'brands', 'brand', 'size', 'sizes', 'in_stock', 'min_price', 'max_price', 'min_rating', 'dietary', 'deals', 'deals_only']))
                    <div class="d-flex align-items-center flex-wrap gap-1.5 pt-3 mt-3 border-top">
                        <span class="small text-muted fw-bold me-1"><i class="bx bx-check-circle text-primary"></i> {{ __('Active Filters:') }}</span>
                        
                        @if(request('q'))
                            <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="active-filter-pill">
                                {{ __('Search:') }} "{{ request('q') }}" <i class="bx bx-x fs-6"></i>
                            </a>
                        @endif

                        @if(request('category'))
                            @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
                            @if($activeCat)
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="active-filter-pill" style="background: #ECFDF5; color: #059669; border-color: #A7F3D0;">
                                    {{ __('Aisle:') }} {{ $activeCat->name }} <i class="bx bx-x fs-6"></i>
                                </a>
                            @endif
                        @endif

                        @if(request('brands'))
                            @foreach((array)request('brands') as $b)
                                @php
                                    $remainingBrands = array_diff((array)request('brands'), [$b]);
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['brands' => $remainingBrands ?: null]) }}" class="active-filter-pill" style="background: #FFFBEB; color: #D97706; border-color: #FDE68A;">
                                    {{ __('Brand:') }} {{ $b }} <i class="bx bx-x fs-6"></i>
                                </a>
                            @endforeach
                        @endif

                        @if(request('size') || request('sizes'))
                            @php $szList = (array)(request('sizes') ?: [request('size')]); @endphp
                            @foreach($szList as $sz)
                                <a href="{{ request()->fullUrlWithQuery(['size' => null, 'sizes' => array_diff($szList, [$sz]) ?: null]) }}" class="active-filter-pill" style="background: #F0FDF4; color: #15803D; border-color: #BBF7D0;">
                                    📏 {{ __('Size:') }} {{ $sz }} <i class="bx bx-x fs-6"></i>
                                </a>
                            @endforeach
                        @endif

                        @if(request('min_price') || request('max_price'))
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="active-filter-pill" style="background: #F0FDF4; color: #16A34A; border-color: #BBF7D0;">
                                ${{ request('min_price', 0) }} - ${{ request('max_price', 'Max') }} <i class="bx bx-x fs-6"></i>
                            </a>
                        @endif

                        @if(request('min_rating'))
                            <a href="{{ request()->fullUrlWithQuery(['min_rating' => null]) }}" class="active-filter-pill" style="background: #FFF7ED; color: #EA580C; border-color: #FED7AA;">
                                {{ request('min_rating') }}★ &amp; Up <i class="bx bx-x fs-6"></i>
                            </a>
                        @endif

                        @if(request('dietary'))
                            <a href="{{ request()->fullUrlWithQuery(['dietary' => null]) }}" class="active-filter-pill" style="background: #ECFDF5; color: #059669; border-color: #A7F3D0;">
                                🌿 {{ request('dietary') }} <i class="bx bx-x fs-6"></i>
                            </a>
                        @endif

                        @if(request('deals') || request('deals_only'))
                            <a href="{{ request()->fullUrlWithQuery(['deals' => null, 'deals_only' => null]) }}" class="active-filter-pill" style="background: #FEF2F2; color: #DC2626; border-color: #FECACA;">
                                ⚡ {{ __('Deals Only') }} <i class="bx bx-x fs-6"></i>
                            </a>
                        @endif

                        @if(request('in_stock'))
                            <a href="{{ request()->fullUrlWithQuery(['in_stock' => null]) }}" class="active-filter-pill" style="background: #F1F5F9; color: #475569; border-color: #CBD5E1;">
                                📦 {{ __('In Stock') }} <i class="bx bx-x fs-6"></i>
                            </a>
                        @endif

                        <a href="{{ route('storefront.shop') }}" class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-bold ms-auto small">
                            {{ __('Clear All') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Products Grid Container -->
            <div class="row g-3.5" id="productsCatalogContainer">
                @forelse($products as $prod)
                    <div class="col-6 col-md-4 product-item-col">
                        <div class="product-grid-card">
                            <div>
                                <!-- Image Frame Canvas with Floating Glass Icons -->
                                <div class="product-img-canvas">
                                    @if($prod->deal_of_the_day || ($prod->compare_at_price && $prod->compare_at_price > $prod->price))
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
                                        <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $prod->name }}">
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
                                        <span class="text-muted" style="font-size: 11px;">({{ $prod->reviews_count ?? 12 }})</span>
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
                    <div class="col-12 text-center py-5 bg-white rounded-4 border">
                        <div class="avatar bg-light text-muted p-4 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                            <i class="bx bx-search-alt fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ __('No products match your criteria.') }}</h5>
                        <p class="small text-muted mb-3">{{ __('Try adjusting your filter facets, price range, or resetting your search.') }}</p>
                        <a href="{{ route('storefront.shop') }}" class="btn btn-gradient-primary rounded-pill px-4">{{ __('Clear All Filters') }}</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4.5 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
function filterBrandList(val) {
    val = val.toLowerCase().trim();
    const items = document.querySelectorAll('.brand-item');
    items.forEach(el => {
        const text = el.textContent.toLowerCase();
        el.style.display = text.includes(val) ? '' : 'none';
    });
}

function updatePriceSlider(val) {
    document.getElementById('maxPriceInput').value = val;
    document.getElementById('priceDisplayRange').textContent = '$' + (document.getElementById('minPriceInput').value || 0) + ' - $' + val;
}

function syncPriceInputs() {
    const min = document.getElementById('minPriceInput').value || 0;
    const max = document.getElementById('maxPriceInput').value || {{ $filterConfig['price_max_limit'] ?? 100 }};
    document.getElementById('priceDisplayRange').textContent = '$' + min + ' - $' + max;
    document.getElementById('priceRangeSlider').value = max;
}

function setCatalogView(mode) {
    const container = document.getElementById('productsCatalogContainer');
    const cols = container.querySelectorAll('.product-item-col');
    const btnGrid = document.getElementById('btnGridView');
    const btnList = document.getElementById('btnListView');

    if (mode === 'list') {
        cols.forEach(col => {
            col.className = 'col-12 product-item-col';
            const card = col.querySelector('.product-grid-card');
            if (card) {
                card.classList.add('flex-md-row', 'align-items-md-center');
                const canvas = card.querySelector('.product-img-canvas');
                if (canvas) canvas.style.width = '200px';
            }
        });
        btnList.classList.add('active', 'bg-white', 'shadow-xs');
        btnGrid.classList.remove('active', 'bg-white', 'shadow-xs');
        localStorage.setItem('akmart_catalog_view', 'list');
    } else {
        cols.forEach(col => {
            col.className = 'col-6 col-md-4 product-item-col';
            const card = col.querySelector('.product-grid-card');
            if (card) {
                card.classList.remove('flex-md-row', 'align-items-md-center');
                const canvas = card.querySelector('.product-img-canvas');
                if (canvas) canvas.style.width = '';
            }
        });
        btnGrid.classList.add('active', 'bg-white', 'shadow-xs');
        btnList.classList.remove('active', 'bg-white', 'shadow-xs');
        localStorage.setItem('akmart_catalog_view', 'grid');
    }
}

// Restore saved view preference
document.addEventListener('DOMContentLoaded', () => {
    const savedView = localStorage.getItem('akmart_catalog_view');
    if (savedView === 'list') {
        setCatalogView('list');
    }
});
</script>
@endsection
