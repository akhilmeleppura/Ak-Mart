@extends('layouts/layoutMaster')

@section('title', __('E-Commerce & Catalog Settings') . ' — AK-Mart')

@section('content')
<div class="row g-6">
  <div class="col-12 col-lg-4 col-xl-3">
    @include('content.apps._settings-sidebar')
  </div>

  <div class="col-12 col-lg-8 col-xl-9">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-shopping-bag text-primary fs-4"></i>
            <span>{{ __('E-Commerce & Catalog Configuration') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure catalog sorting, pagination, cart thresholds, product compare, and review moderation.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'ecommerce') }}">
          @csrf

          <!-- Catalog Display -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-grid-alt text-primary"></i>
            <span>{{ __('Catalog Display & Pagination') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Products Per Page (Catalog)') }}</label>
              <select name="catalog_products_per_page" class="form-select">
                <option value="12" {{ ($settings['catalog_products_per_page'] ?? '12') === '12' ? 'selected' : '' }}>12 {{ __('Items per page') }}</option>
                <option value="24" {{ ($settings['catalog_products_per_page'] ?? '') === '24' ? 'selected' : '' }}>24 {{ __('Items per page') }}</option>
                <option value="36" {{ ($settings['catalog_products_per_page'] ?? '') === '36' ? 'selected' : '' }}>36 {{ __('Items per page') }}</option>
                <option value="48" {{ ($settings['catalog_products_per_page'] ?? '') === '48' ? 'selected' : '' }}>48 {{ __('Items per page') }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Product Sorting') }}</label>
              <select name="catalog_default_sort" class="form-select">
                <option value="newest" {{ ($settings['catalog_default_sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>{{ __('Newest Arrivals First') }}</option>
                <option value="price_low_high" {{ ($settings['catalog_default_sort'] ?? '') === 'price_low_high' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                <option value="price_high_low" {{ ($settings['catalog_default_sort'] ?? '') === 'price_high_low' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                <option value="best_selling" {{ ($settings['catalog_default_sort'] ?? '') === 'best_selling' ? 'selected' : '' }}>{{ __('Best Selling / Most Popular') }}</option>
                <option value="featured" {{ ($settings['catalog_default_sort'] ?? '') === 'featured' ? 'selected' : '' }}>{{ __('Featured Products First') }}</option>
              </select>
            </div>
          </div>

          <!-- Cart & Checkout Limits -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-cart text-primary"></i>
            <span>{{ __('Shopping Cart Thresholds') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Minimum Order Amount ($)') }}</label>
              <input type="number" step="0.01" name="cart_min_order_amount" class="form-control" value="{{ $settings['cart_min_order_amount'] ?? '0.00' }}" />
              <small class="text-muted">{{ __('Zero means no minimum requirement.') }}</small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Maximum Quantity Per Cart Item') }}</label>
              <input type="number" name="cart_max_quantity_per_item" class="form-control" value="{{ $settings['cart_max_quantity_per_item'] ?? '50' }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Cart Expiration (Hours)') }}</label>
              <input type="number" name="cart_expiration_hours" class="form-control" value="{{ $settings['cart_expiration_hours'] ?? '72' }}" />
            </div>
          </div>

          <!-- Product Details Display -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-detail text-primary"></i>
            <span>{{ __('Product Page Details & Badges') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Show Stock Quantity on Product Page') }}</h6>
                <small class="text-muted">{{ __('Displays exact units in stock to encourage buyer urgency.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="show_stock_quantity" value="0">
                <input class="form-check-input" type="checkbox" name="show_stock_quantity" value="1" {{ ($settings['show_stock_quantity'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Show SKU and Barcode in Storefront') }}</h6>
                <small class="text-muted">{{ __('Make SKU and EAN barcode visible to customers on product pages.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="show_sku_barcode" value="0">
                <input class="form-check-input" type="checkbox" name="show_sku_barcode" value="1" {{ ($settings['show_sku_barcode'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Require Admin Moderation for Reviews') }}</h6>
                <small class="text-muted">{{ __('Customer reviews require administrator approval before appearing publicly.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="reviews_require_approval" value="0">
                <input class="form-check-input" type="checkbox" name="reviews_require_approval" value="1" {{ ($settings['reviews_require_approval'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save E-Commerce Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
