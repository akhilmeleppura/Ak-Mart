@extends('layouts.storefrontMaster')

@section('title', __('Shop Groceries & Supermarket Products') . ' — AK-Mart')

@section('content')
<div class="container py-2">
    <!-- Top Quick Filters Bar -->
    @if(!empty($filterConfig['quick_filter_bar']))
        <div class="d-flex align-items-center gap-2 overflow-auto pb-3 mb-3 border-bottom text-nowrap no-scrollbar">
            <span class="small fw-bold text-muted me-1"><i class="bx bx-bolt-circle text-primary"></i> {{ __('Quick Filters:') }}</span>
            <a href="{{ route('storefront.shop') }}" class="btn btn-sm rounded-pill {{ !request()->hasAny(['deals', 'deals_only', 'dietary', 'min_rating', 'max_price', 'in_stock']) ? 'btn-primary' : 'btn-outline-secondary bg-white' }}">
                {{ __('All Items') }} ({{ $products->total() }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['deals' => '1']) }}" class="btn btn-sm rounded-pill {{ request('deals') === '1' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark bg-white' }}">
                ⚡ {{ __('Flash Deals') }} ({{ $dealsCount }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['dietary' => 'Organic']) }}" class="btn btn-sm rounded-pill {{ request('dietary') === 'Organic' ? 'btn-success fw-bold' : 'btn-outline-success bg-white' }}">
                🌿 {{ __('Organic') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['min_rating' => '4']) }}" class="btn btn-sm rounded-pill {{ request('min_rating') == '4' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' }}">
                ⭐ {{ __('4★ & Up') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['in_stock' => '1']) }}" class="btn btn-sm rounded-pill {{ request('in_stock') == '1' ? 'btn-info text-white fw-bold' : 'btn-outline-info bg-white' }}">
                📦 {{ __('In Stock') }} ({{ $inStockCount }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['max_price' => '10']) }}" class="btn btn-sm rounded-pill {{ request('max_price') == '10' ? 'btn-danger fw-bold' : 'btn-outline-danger bg-white' }}">
                💰 {{ __('Under $10') }}
            </a>
        </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card p-3.5 border shadow-xs rounded-4 bg-white sticky-top" style="top: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-filter-alt me-1 text-primary"></i> {{ __('Filter Catalog') }}</h5>
                    @if(request()->hasAny(['q', 'category', 'brands', 'brand', 'in_stock', 'min_price', 'max_price', 'min_rating', 'dietary', 'deals', 'deals_only', 'sort']))
                        <a href="{{ route('storefront.shop') }}" class="small text-danger fw-semibold text-decoration-none"><i class="bx bx-trash me-0.5"></i>{{ __('Reset') }}</a>
                    @endif
                </div>

                <form action="{{ route('storefront.shop') }}" method="GET" id="catalogFilterForm">
                    <!-- Keyword Search -->
                    @if(!empty($filterConfig['show_search']))
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase letter-spacing-1">{{ __('Keyword Search') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                                <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="{{ __('Search name, SKU...') }}" value="{{ request('q') }}">
                                @if(request('q'))
                                    <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="input-group-text bg-light text-muted"><i class="bx bx-x"></i></a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Category Filter -->
                    @if(!empty($filterConfig['show_category']))
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase letter-spacing-1">{{ __('Aisle Category') }}</label>
                            <select name="category" class="form-select form-select-sm" onchange="document.getElementById('catalogFilterForm').submit()">
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
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-muted text-uppercase letter-spacing-1 mb-0">{{ __('Brand / Producer') }}</label>
                                <span class="badge bg-light text-muted border rounded-pill">{{ count($availableBrands) }}</span>
                            </div>
                            <!-- Mini Search in Brands -->
                            <input type="text" class="form-control form-control-sm mb-2" placeholder="{{ __('Filter brands...') }}" onkeyup="filterBrandList(this.value)">
                            
                            <div class="p-2 border rounded-3 bg-light bg-opacity-50 brand-list-container" id="brandListContainer" style="max-height: 140px; overflow-y: auto;">
                                @php
                                    $selectedBrands = (array) request('brands', []);
                                    if (request('brand')) $selectedBrands[] = request('brand');
                                @endphp
                                @foreach($availableBrands as $brand)
                                    @php $cnt = $brandCounts[$brand] ?? 0; @endphp
                                    <div class="form-check small mb-1 brand-item">
                                        <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand }}" id="brand_{{ Str::slug($brand) }}" {{ in_array($brand, $selectedBrands) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex justify-content-between text-truncate w-100" for="brand_{{ Str::slug($brand) }}">
                                            <span class="text-truncate">{{ $brand }}</span>
                                            <span class="text-muted ms-1 small">({{ $cnt }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Dual Range Price Slider -->
                    @if(!empty($filterConfig['show_price']))
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-muted text-uppercase letter-spacing-1 mb-0">{{ __('Price Range ($)') }}</label>
                                <span class="small fw-semibold text-primary" id="priceDisplayRange">
                                    ${{ request('min_price', $filterConfig['price_min_limit'] ?? 0) }} - ${{ request('max_price', $filterConfig['price_max_limit'] ?? 100) }}
                                </span>
                            </div>
                            
                            <div class="range-slider-container position-relative my-2">
                                <input type="range" class="form-range" id="priceRangeSlider" min="{{ $filterConfig['price_min_limit'] ?? 0 }}" max="{{ $filterConfig['price_max_limit'] ?? 100 }}" value="{{ request('max_price', $filterConfig['price_max_limit'] ?? 100) }}" step="1" oninput="updatePriceSlider(this.value)">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="min_price" id="minPriceInput" class="form-control" placeholder="Min" value="{{ request('min_price') }}" step="0.5" min="0" onchange="syncPriceInputs()">
                                </div>
                                <span class="text-muted">-</span>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="max_price" id="maxPriceInput" class="form-control" placeholder="Max" value="{{ request('max_price') }}" step="0.5" min="0" onchange="syncPriceInputs()">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Dietary & Special Attributes Tags -->
                    @if(!empty($filterConfig['show_dietary']) && !empty($filterConfig['dietary_tags']))
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase letter-spacing-1 mb-1.5">{{ __('Dietary & Features') }}</label>
                            <div class="d-flex flex-wrap gap-1.5">
                                @php
                                    $tags = array_map('trim', explode(',', $filterConfig['dietary_tags']));
                                @endphp
                                @foreach($tags as $tag)
                                    @php $isActive = request('dietary') === $tag; @endphp
                                    <a href="{{ $isActive ? request()->fullUrlWithQuery(['dietary' => null]) : request()->fullUrlWithQuery(['dietary' => $tag]) }}" class="badge rounded-pill px-2.5 py-1.5 text-decoration-none {{ $isActive ? 'bg-success text-white' : 'bg-light text-dark border' }}">
                                        {{ $tag }} {{ $isActive ? '✓' : '' }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Customer Rating Filter -->
                    @if(!empty($filterConfig['show_rating']))
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase letter-spacing-1 mb-1">{{ __('Customer Rating') }}</label>
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="radio" name="min_rating" value="4" id="rating4" {{ request('min_rating') == '4' ? 'checked' : '' }}>
                                <label class="form-check-label text-warning" for="rating4">
                                    <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star text-muted"></i>
                                    <span class="text-dark ms-1 fw-semibold">&amp; {{ __('Up') }}</span>
                                </label>
                            </div>
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="radio" name="min_rating" value="3" id="rating3" {{ request('min_rating') == '3' ? 'checked' : '' }}>
                                <label class="form-check-label text-warning" for="rating3">
                                    <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star text-muted"></i><i class="bx bx-star text-muted"></i>
                                    <span class="text-dark ms-1 fw-semibold">&amp; {{ __('Up') }}</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- In-Stock & Deals Toggles -->
                    <div class="mb-3 pt-2 border-top">
                        @if(!empty($filterConfig['show_stock']))
                            <div class="form-check small mb-1">
                                <input type="checkbox" name="in_stock" value="1" class="form-check-input" id="inStockCheck" {{ request('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="inStockCheck">{{ __('In-Stock Only') }} ({{ $inStockCount }})</label>
                            </div>
                        @endif
                        @if(!empty($filterConfig['show_deals']))
                            <div class="form-check small">
                                <input type="checkbox" name="deals_only" value="1" class="form-check-input" id="dealsOnlyCheck" {{ request('deals_only') || request('deals') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-danger" for="dealsOnlyCheck">⚡ {{ __('On Sale Deals Only') }} ({{ $dealsCount }})</label>
                            </div>
                        @endif
                    </div>

                    <!-- Preserve Active State -->
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request('dietary')) <input type="hidden" name="dietary" value="{{ request('dietary') }}"> @endif
                    @if(request('view')) <input type="hidden" name="view" value="{{ request('view') }}"> @endif

                    <!-- Submit Action Button -->
                    <button class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold shadow-xs" type="submit">
                        <i class="bx bx-check me-1"></i> {{ __('Apply Filters') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Products Grid & Content Area -->
        <div class="col-lg-9">
            <!-- Header Bar with Sorting and Grid/List Switcher -->
            <div class="card p-3 border shadow-xs rounded-4 mb-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ __('Product Catalog') }}</h4>
                        <span class="text-muted small">{{ $products->total() }} {{ __('items found matching criteria') }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if(!empty($filterConfig['grid_list_toggle']))
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" id="btnGridView" onclick="setCatalogView('grid')" title="{{ __('Grid View') }}">
                                    <i class="bx bx-grid-alt fs-5 align-middle"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnListView" onclick="setCatalogView('list')" title="{{ __('List View') }}">
                                    <i class="bx bx-list-ul fs-5 align-middle"></i>
                                </button>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted fw-semibold text-nowrap mb-0">{{ __('Sort By:') }}</label>
                            <select class="form-select form-select-sm rounded-pill" style="width: 180px;" onchange="location = this.value;">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>{{ __('Newest Arrivals') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating_high']) }}" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>{{ __('Top Rated (5★ to 1★)') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('Best Sellers & In Stock') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Active Filter Badges / Chips -->
                @if(request()->hasAny(['q', 'category', 'brands', 'brand', 'in_stock', 'min_price', 'max_price', 'min_rating', 'dietary', 'deals', 'deals_only']))
                    <div class="d-flex align-items-center flex-wrap gap-1.5 pt-3 mt-3 border-top">
                        <span class="small text-muted fw-bold me-1"><i class="bx bx-check-circle text-primary"></i> {{ __('Active Filters:') }}</span>
                        
                        @if(request('q'))
                            <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-2.5 py-1 text-decoration-none">
                                {{ __('Search:') }} "{{ request('q') }}" <i class="bx bx-x align-middle"></i>
                            </a>
                        @endif

                        @if(request('category'))
                            @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
                            @if($activeCat)
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2.5 py-1 text-decoration-none">
                                    {{ __('Aisle:') }} {{ $activeCat->name }} <i class="bx bx-x align-middle"></i>
                                </a>
                            @endif
                        @endif

                        @if(request('brands'))
                            @foreach((array)request('brands') as $b)
                                @php
                                    $remainingBrands = array_diff((array)request('brands'), [$b]);
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['brands' => $remainingBrands ?: null]) }}" class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-2.5 py-1 text-decoration-none">
                                    {{ __('Brand:') }} {{ $b }} <i class="bx bx-x align-middle"></i>
                                </a>
                            @endforeach
                        @endif

                        @if(request('min_price') || request('max_price'))
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-2.5 py-1 text-decoration-none">
                                {{ __('Price:') }} ${{ request('min_price', 0) }} - ${{ request('max_price', 'Max') }} <i class="bx bx-x align-middle"></i>
                            </a>
                        @endif

                        @if(request('min_rating'))
                            <a href="{{ request()->fullUrlWithQuery(['min_rating' => null]) }}" class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-2.5 py-1 text-decoration-none">
                                {{ __('Rating:') }} {{ request('min_rating') }}★ &amp; Up <i class="bx bx-x align-middle"></i>
                            </a>
                        @endif

                        @if(request('dietary'))
                            <a href="{{ request()->fullUrlWithQuery(['dietary' => null]) }}" class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2.5 py-1 text-decoration-none">
                                🌿 {{ request('dietary') }} <i class="bx bx-x align-middle"></i>
                            </a>
                        @endif

                        @if(request('deals') || request('deals_only'))
                            <a href="{{ request()->fullUrlWithQuery(['deals' => null, 'deals_only' => null]) }}" class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2.5 py-1 text-decoration-none">
                                ⚡ {{ __('Deals Only') }} <i class="bx bx-x align-middle"></i>
                            </a>
                        @endif

                        @if(request('in_stock'))
                            <a href="{{ request()->fullUrlWithQuery(['in_stock' => null]) }}" class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-2.5 py-1 text-decoration-none">
                                📦 {{ __('In Stock') }} <i class="bx bx-x align-middle"></i>
                            </a>
                        @endif

                        <a href="{{ route('storefront.shop') }}" class="badge bg-dark text-white rounded-pill px-2.5 py-1 text-decoration-none ms-auto">
                            {{ __('Clear All') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Products Grid Container -->
            <div class="row g-3" id="productsCatalogContainer">
                @forelse($products as $prod)
                    <div class="col-6 col-md-4 product-item-col">
                        <div class="product-card h-100 d-flex flex-column justify-content-between p-3 rounded-4 border bg-white shadow-xs transition-hover">
                            <div>
                                <div class="product-img-wrap rounded-3 mb-3 position-relative overflow-hidden">
                                    @if($prod->deal_of_the_day || ($prod->compare_at_price && $prod->compare_at_price > $prod->price))
                                        <span class="badge bg-danger position-absolute bottom-0 start-0 m-2 rounded-pill shadow-xs" style="z-index: 5;">
                                            ⚡ {{ __('Deal') }}
                                        </span>
                                    @endif

                                    <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 start-0 m-2 shadow-xs border-0 p-1.5" onclick="quickToggleCompare({{ $prod->id }}, this, event)" style="z-index: 5;" title="{{ __('Add to Compare') }}">
                                        <i class="bx {{ in_array($prod->id, session('compare_list', [])) ? 'bx-git-compare text-primary fw-bold' : 'bx-git-compare text-muted' }} fs-5 align-middle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-xs border-0 p-1.5" onclick="quickToggleWishlist({{ $prod->id }}, this, event)" style="z-index: 5;" title="{{ __('Save to Wishlist') }}">
                                        <i class="bx {{ in_array($prod->id, session('wishlist', [])) ? 'bxs-heart text-danger' : 'bx-heart text-muted' }} fs-5 align-middle"></i>
                                    </button>
                                    <a href="{{ route('storefront.product', $prod->id) }}">
                                        <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $prod->name }}" class="w-100 object-fit-contain" style="height: 160px;">
                                    </a>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small d-block">{{ $prod->category?->name ?? __('General') }}</span>
                                    @if($prod->brand)
                                        <span class="badge bg-light text-muted border small">{{ $prod->brand }}</span>
                                    @endif
                                </div>

                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('storefront.product', $prod->id) }}" class="text-dark text-decoration-none text-truncate d-block" title="{{ $prod->name }}">{{ $prod->name }}</a>
                                </h6>

                                @if($prod->rating_cache > 0)
                                    <div class="text-warning small mb-2">
                                        <i class="bx bxs-star"></i> {{ number_format($prod->rating_cache, 1) }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2.5">
                                    <div>
                                        <span class="fs-5 fw-bold text-primary">${{ number_format($prod->price, 2) }}</span>
                                        @if($prod->compare_at_price && $prod->compare_at_price > $prod->price)
                                            <span class="text-muted text-decoration-line-through small ms-1">${{ number_format($prod->compare_at_price, 2) }}</span>
                                        @endif
                                    </div>
                                    <span class="badge badge-stock {{ $prod->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill">
                                        {{ $prod->qty > 0 ? "Stock: {$prod->qty}" : __('Sold Out') }}
                                    </span>
                                </div>

                                <button class="btn btn-outline-primary w-100 rounded-pill btn-sm d-flex align-items-center justify-content-center gap-1 shadow-xs fw-semibold" onclick="quickAddToCart({{ $prod->id }})" {{ $prod->qty <= 0 ? 'disabled' : '' }}>
                                    <i class="bx bx-cart-add fs-5"></i>
                                    <span>{{ __('Add to Cart') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 bg-white rounded-4 border">
                        <i class="bx bx-search-alt fs-1 text-muted mb-2"></i>
                        <h5 class="fw-bold text-muted">{{ __('No products match your criteria.') }}</h5>
                        <p class="small text-muted mb-3">{{ __('Try adjusting your filter facets or resetting your search.') }}</p>
                        <a href="{{ route('storefront.shop') }}" class="btn btn-primary btn-sm rounded-pill px-4">{{ __('Clear All Filters') }}</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
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
            const card = col.querySelector('.product-card');
            if (card) card.classList.add('flex-md-row', 'align-items-md-center');
        });
        btnList.classList.add('active');
        btnGrid.classList.remove('active');
        localStorage.setItem('akmart_catalog_view', 'list');
    } else {
        cols.forEach(col => {
            col.className = 'col-6 col-md-4 product-item-col';
            const card = col.querySelector('.product-card');
            if (card) card.classList.remove('flex-md-row', 'align-items-md-center');
        });
        btnGrid.classList.add('active');
        btnList.classList.remove('active');
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
