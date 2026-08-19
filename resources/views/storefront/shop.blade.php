@extends('layouts.storefrontMaster')

@section('title', __('Shop Groceries & Supermarket Products') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card p-3 border shadow-xs rounded-3">
                <h5 class="fw-bold mb-3"><i class="bx bx-filter-alt me-1 text-primary"></i> {{ __('Filter Catalog') }}</h5>

                <form action="{{ route('storefront.shop') }}" method="GET">
                    <!-- Search Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Keyword Search') }}</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="{{ __('Search name, SKU...') }}" value="{{ request('q') }}">
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Aisle Category') }}</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">{{ __('All Aisles') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- In-Stock Checkbox -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="in_stock" value="1" class="form-check-input" id="inStockCheck" {{ request('in_stock') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="inStockCheck">{{ __('In-Stock Only') }}</label>
                    </div>

                    <!-- Submit Filter -->
                    <button class="btn btn-primary btn-sm w-100 rounded-pill" type="submit">{{ __('Apply Filters') }}</button>
                    @if(request()->hasAny(['q', 'category', 'in_stock', 'min_price', 'max_price']))
                        <a href="{{ route('storefront.shop') }}" class="btn btn-link btn-sm w-100 text-muted mt-1">{{ __('Reset All') }}</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0">{{ __('Product Catalog') }}</h4>
                    <span class="text-muted small">{{ $products->total() }} {{ __('items found') }}</span>
                </div>
            </div>

            <div class="row g-3">
                @forelse($products as $prod)
                    <div class="col-6 col-md-4">
                        <div class="product-card h-100 d-flex flex-column justify-content-between p-3">
                            <div>
                                <div class="product-img-wrap rounded-3 mb-3 position-relative">
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
                                        {{ $prod->qty > 0 ? "Stock: {$prod->qty}" : __('Sold Out') }}
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
                    <div class="col-12 text-center py-5">
                        <i class="bx bx-search-alt fs-1 text-muted mb-2"></i>
                        <h5 class="fw-bold text-muted">{{ __('No products match your criteria.') }}</h5>
                        <a href="{{ route('storefront.shop') }}" class="btn btn-primary btn-sm rounded-pill mt-2">{{ __('Clear Filters') }}</a>
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
@endsection
