@extends('layouts.storefrontMaster')

@section('title', __('My Saved Wishlist') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card p-3 border shadow-xs rounded-4">
                <div class="nav flex-column gap-1">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-grid-alt me-2"></i>{{ __('Dashboard') }}</a>
                    <a href="{{ route('customer.orders') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-package me-2"></i>{{ __('My Orders') }}</a>
                    <a href="{{ route('customer.wishlist') }}" class="nav-link active bg-primary text-white rounded-3 py-2 px-3 fw-semibold"><i class="bx bx-heart me-2"></i>{{ __('Wishlist') }}</a>
                    <a href="{{ route('customer.wallet') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-wallet me-2"></i>{{ __('Store Credit Wallet') }}</a>
                    <a href="{{ route('customer.loyalty') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-gift me-2"></i>{{ __('Loyalty Points') }}</a>
                    <a href="{{ route('customer.profile') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-user me-2"></i>{{ __('Profile Settings') }}</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <h4 class="fw-bold mb-3">{{ __('My Wishlist') }} ({{ $wishlistItems->count() }} {{ __('items') }})</h4>

            @if($wishlistItems->isEmpty())
                <div class="card p-5 text-center border shadow-xs rounded-4">
                    <i class="bx bx-heart fs-1 text-muted mb-2"></i>
                    <h5 class="fw-bold text-muted">{{ __('Your wishlist is empty') }}</h5>
                    <p class="text-muted small">{{ __('Save your favorite products while browsing the catalog.') }}</p>
                    <div>
                        <a href="{{ route('storefront.shop') }}" class="btn btn-primary btn-sm rounded-pill mt-2">{{ __('Browse Catalog') }}</a>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach($wishlistItems as $item)
                        @if($item->product)
                            <div class="col-md-6 col-lg-4">
                                <div class="product-card p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="product-img-wrap rounded-3 mb-2">
                                            <img src="{{ $item->product->image ? asset($item->product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $item->product->name }}">
                                        </div>
                                        <h6 class="fw-bold mb-1">
                                            <a href="{{ route('storefront.product', $item->product->id) }}" class="text-dark text-decoration-none text-truncate d-block">{{ $item->product->name }}</a>
                                        </h6>
                                        <span class="text-primary fw-bold">${{ number_format($item->product->price, 2) }}</span>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-sm w-100 rounded-pill" onclick="quickAddToCart({{ $item->product->id }})">
                                            <i class="bx bx-cart-add me-1"></i> {{ __('Move to Cart') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
