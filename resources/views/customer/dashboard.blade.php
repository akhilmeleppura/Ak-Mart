@extends('layouts.storefrontMaster')

@section('title', __('My Customer Account') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card p-3 border shadow-xs rounded-4">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                </div>

                <div class="nav flex-column gap-1">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link active bg-primary text-white rounded-3 py-2 px-3 fw-semibold"><i class="bx bx-grid-alt me-2"></i>{{ __('Dashboard') }}</a>
                    <a href="{{ route('customer.orders') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-package me-2"></i>{{ __('My Orders') }}</a>
                    <a href="{{ route('customer.wishlist') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-heart me-2"></i>{{ __('Wishlist') }}</a>
                    <a href="{{ route('customer.wallet') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-wallet me-2"></i>{{ __('Store Credit Wallet') }}</a>
                    <a href="{{ route('customer.loyalty') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-gift me-2"></i>{{ __('Loyalty Points') }}</a>
                    <a href="{{ route('customer.profile') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-user me-2"></i>{{ __('Profile Settings') }}</a>
                </div>
            </div>
        </div>

        <!-- Main Account Dashboard Content -->
        <div class="col-lg-9">
            <h4 class="fw-bold mb-3">{{ __('Account Overview') }}</h4>

            <!-- KPI Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card p-3 border shadow-xs rounded-3 text-center">
                        <span class="text-muted small">{{ __('Total Orders') }}</span>
                        <h3 class="fw-bold text-primary my-1">{{ $totalOrders }}</h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-3 border shadow-xs rounded-3 text-center">
                        <span class="text-muted small">{{ __('Total Spent') }}</span>
                        <h3 class="fw-bold text-success my-1">${{ number_format($totalSpent, 2) }}</h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-3 border shadow-xs rounded-3 text-center">
                        <span class="text-muted small">{{ __('Wallet Credit') }}</span>
                        <h3 class="fw-bold text-info my-1">${{ number_format($walletBalance, 2) }}</h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-3 border shadow-xs rounded-3 text-center">
                        <span class="text-muted small">{{ __('Loyalty Points') }}</span>
                        <h3 class="fw-bold text-warning my-1">{{ $loyaltyPoints }}</h3>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="card border shadow-xs rounded-4 overflow-hidden">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ __('Recent Orders') }}</h6>
                    <a href="{{ route('customer.orders') }}" class="small text-primary text-decoration-none">{{ __('View All') }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Order #') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $ord)
                                <tr>
                                    <td><strong class="text-primary">{{ $ord->order_number }}</strong></td>
                                    <td><small>{{ $ord->created_at->format('M d, Y') }}</small></td>
                                    <td><span class="badge bg-label-primary text-uppercase">{{ $ord->order_status }}</span></td>
                                    <td><strong>${{ number_format($ord->total_amount, 2) }}</strong></td>
                                    <td class="text-end">
                                        <a href="{{ route('customer.order.details', $ord->order_number) }}" class="btn btn-sm btn-outline-primary rounded-pill">{{ __('Details') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No orders placed yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
