@extends('layouts/layoutMaster')

@section('title', __('Storefront Advanced Filters & Facet Manager') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-slider-alt text-primary me-2"></i> {{ __('Storefront Advanced Filters & Merchandising Facets') }}</h4>
        <p class="text-muted small mb-0">{{ __('Configure interactive catalog filters, dual price slider bounds, brand lists, dietary tags, and quick-filter chips') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('storefront.shop') }}" target="_blank" class="btn btn-outline-primary rounded-pill">
            <i class="bx bx-show me-1"></i> {{ __('Preview Live Catalog') }}
        </a>
        <button type="submit" form="filtersConfigForm" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bx bx-save me-1"></i> {{ __('Save Filter Configuration') }}
        </button>
    </div>
</div>

<!-- Metrics Bar -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle p-3 fs-4">
                    <i class="bx bx-shopping-bag"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">{{ $totalProducts }}</h4>
                    <span class="text-muted small">{{ __('Filterable Products') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-success bg-opacity-10 text-success rounded-circle p-3 fs-4">
                    <i class="bx bx-category"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">{{ $categoriesCount }}</h4>
                    <span class="text-muted small">{{ __('Aisle Categories') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-warning bg-opacity-10 text-warning rounded-circle p-3 fs-4">
                    <i class="bx bx-purchase-tag-alt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">{{ count($availableBrands) }}</h4>
                    <span class="text-muted small">{{ __('Active Brands') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-info bg-opacity-10 text-info rounded-circle p-3 fs-4">
                    <i class="bx bx-check-shield"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">10 / 10</h4>
                    <span class="text-muted small">{{ __('Filter Modules') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="filtersConfigForm" action="{{ route('app-store-filters-save') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Configuration Controls (Left) -->
        <div class="col-lg-7">
            <div class="card border shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0"><i class="bx bx-cog text-primary me-2"></i> {{ __('Sidebar Filter Modules Control') }}</h5>
                </div>
                <div class="card-body p-4">
                    
                    <!-- 1. Keyword Search Module -->
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-4 text-primary"><i class="bx bx-search"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ __('Search Keyword Input') }}</h6>
                                <small class="text-muted">{{ __('Enables instant debounced search across product names, SKUs, and barcodes') }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" name="show_search" value="1" {{ !empty($filterConfig['show_search']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 2. Category Tree Module -->
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-4 text-success"><i class="bx bx-category"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ __('Aisle Category Selector') }}</h6>
                                <small class="text-muted">{{ __('Displays active categories with live product count badges') }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" name="show_category" value="1" {{ !empty($filterConfig['show_category']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 3. Brand Module -->
                    <div class="p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-4 text-warning"><i class="bx bx-purchase-tag-alt"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('Brand / Producer Multi-Select') }}</h6>
                                    <small class="text-muted">{{ __('Allows customers to filter simultaneously by one or multiple brands') }}</small>
                                </div>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" name="show_brand" value="1" {{ !empty($filterConfig['show_brand']) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 pt-2 border-top">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">{{ __('Display Style') }}</label>
                                <select name="brand_display" class="form-select form-select-sm">
                                    <option value="scroll_list" {{ ($filterConfig['brand_display'] ?? '') === 'scroll_list' ? 'selected' : '' }}>{{ __('Scrollable Checkbox List') }}</option>
                                    <option value="tag_pills" {{ ($filterConfig['brand_display'] ?? '') === 'tag_pills' ? 'selected' : '' }}>{{ __('Clickable Tag Pills') }}</option>
                                    <option value="dropdown" {{ ($filterConfig['brand_display'] ?? '') === 'dropdown' ? 'selected' : '' }}>{{ __('Dropdown Select Menu') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">{{ __('Brand Badges') }}</label>
                                <div class="small text-muted pt-2">{{ count($availableBrands) }} {{ __('unique brand names detected in catalog') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Price Dual Slider Module -->
                    <div class="p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-4 text-primary"><i class="bx bx-dollar-circle"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('Dual Draggable Price Range Slider') }}</h6>
                                    <small class="text-muted">{{ __('Interactive dual thumb range slider with dynamic numeric inputs') }}</small>
                                </div>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" name="show_price" value="1" {{ !empty($filterConfig['show_price']) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 pt-2 border-top">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted">{{ __('Min Price Limit ($)') }}</label>
                                <input type="number" name="price_min_limit" class="form-control form-control-sm" value="{{ $filterConfig['price_min_limit'] ?? 0 }}" min="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted">{{ __('Max Price Limit ($)') }}</label>
                                <input type="number" name="price_max_limit" class="form-control form-control-sm" value="{{ $filterConfig['price_max_limit'] ?? 100 }}" min="1">
                            </div>
                        </div>
                    </div>

                    <!-- 5. Customer Rating Module -->
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-4 text-warning"><i class="bx bx-star"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ __('Customer Star Ratings (4★, 3★, 2★ & Up)') }}</h6>
                                <small class="text-muted">{{ __('Enables star rating threshold filtering') }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" name="show_rating" value="1" {{ !empty($filterConfig['show_rating']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 6. Dietary & Special Attributes Module -->
                    <div class="p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-4 text-success"><i class="bx bx-leaf"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('Dietary & Lifestyle Attributes') }}</h6>
                                    <small class="text-muted">{{ __('Enable specialized grocery filter chips (Organic, Vegan, Halal, etc.)') }}</small>
                                </div>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" name="show_dietary" value="1" {{ !empty($filterConfig['show_dietary']) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <label class="form-label small fw-semibold text-muted">{{ __('Custom Dietary Tags (Comma-Separated)') }}</label>
                            <input type="text" name="dietary_tags" class="form-control form-control-sm" value="{{ $filterConfig['dietary_tags'] ?? 'Organic, Gluten-Free, Vegan, Dairy-Free, Sugar-Free, Non-GMO, Halal' }}">
                        </div>
                    </div>

                    <!-- 7. In-Stock Checkbox -->
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-4 text-info"><i class="bx bx-package"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ __('In-Stock Only Filter Toggle') }}</h6>
                                <small class="text-muted">{{ __('Hides out-of-stock items when enabled by shopper') }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" name="show_stock" value="1" {{ !empty($filterConfig['show_stock']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- 8. Top Quick Filters Bar & Grid/List View -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 bg-light bg-opacity-50 h-100">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('Top Quick-Filter Bar') }}</h6>
                                    <small class="text-muted">{{ __('Shows 1-click pills (Flash Deals, Organic, 4★+, Under $10)') }}</small>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input" type="checkbox" name="quick_filter_bar" value="1" {{ !empty($filterConfig['quick_filter_bar']) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 bg-light bg-opacity-50 h-100">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('Grid / List Switcher') }}</h6>
                                    <small class="text-muted">{{ __('Allows customers to switch between grid and list views') }}</small>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input" type="checkbox" name="grid_list_toggle" value="1" {{ !empty($filterConfig['grid_list_toggle']) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Live Sidebar Mockup / Preview (Right) -->
        <div class="col-lg-5">
            <div class="card border shadow-sm rounded-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-white"><i class="bx bx-desktop me-1"></i> {{ __('Live Sidebar Preview') }}</h6>
                    <span class="badge bg-white text-primary rounded-pill fw-bold">{{ __('Interactive') }}</span>
                </div>
                <div class="card-body p-3 bg-light bg-opacity-25">
                    <div class="card p-3 border shadow-xs rounded-3 bg-white">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bx bx-filter-alt me-1 text-primary"></i> {{ __('Filter Catalog') }}</h6>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">{{ __('Keyword Search') }}</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Search name, SKU..." disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">{{ __('Aisle Category') }}</label>
                            <select class="form-select form-select-sm" disabled>
                                <option>All Aisles ({{ $totalProducts }})</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">{{ __('Brand / Producer') }}</label>
                            <div class="p-2 border rounded-2 bg-light small" style="max-height: 110px; overflow-y: auto;">
                                @foreach(array_slice($availableBrands->toArray(), 0, 4) as $b)
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input" type="checkbox" checked disabled>
                                        <label class="form-check-label text-truncate">{{ $b }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">{{ __('Price Range ($)') }}</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" class="form-control form-control-sm text-center" value="${{ $filterConfig['price_min_limit'] ?? 0 }}" disabled>
                                <span>-</span>
                                <input type="text" class="form-control form-control-sm text-center" value="${{ $filterConfig['price_max_limit'] ?? 100 }}" disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">{{ __('Customer Rating') }}</label>
                            <div class="text-warning small mb-1">
                                <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star text-muted"></i>
                                <span class="text-dark ms-1">&amp; Up</span>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold mb-2" disabled>{{ __('Apply Filters') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
