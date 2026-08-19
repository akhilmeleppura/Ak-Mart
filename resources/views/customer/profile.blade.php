@extends('layouts.storefrontMaster')

@section('title', __('Profile & Security Settings') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card p-3 border shadow-xs rounded-4">
                <div class="nav flex-column gap-1">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-grid-alt me-2"></i>{{ __('Dashboard') }}</a>
                    <a href="{{ route('customer.orders') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-package me-2"></i>{{ __('My Orders') }}</a>
                    <a href="{{ route('customer.wishlist') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-heart me-2"></i>{{ __('Wishlist') }}</a>
                    <a href="{{ route('customer.wallet') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-wallet me-2"></i>{{ __('Store Credit Wallet') }}</a>
                    <a href="{{ route('customer.loyalty') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-gift me-2"></i>{{ __('Loyalty Points') }}</a>
                    <a href="{{ route('customer.profile') }}" class="nav-link active bg-primary text-white rounded-3 py-2 px-3 fw-semibold"><i class="bx bx-user me-2"></i>{{ __('Profile Settings') }}</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <h4 class="fw-bold mb-3">{{ __('Profile & Security Settings') }}</h4>

            <div class="card p-4 border shadow-xs rounded-4">
                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Email Address') }}</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}" placeholder="+1 (555) 000-0000">
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-top pt-4">{{ __('Change Password') }}</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('New Password') }}</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                        </div>
                    </div>

                    <button class="btn btn-primary rounded-pill px-4" type="submit">{{ __('Save Profile Changes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
