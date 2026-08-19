@extends('layouts.storefrontMaster')

@section('title', __('My Order History') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card p-3 border shadow-xs rounded-4">
                <div class="nav flex-column gap-1">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-grid-alt me-2"></i>{{ __('Dashboard') }}</a>
                    <a href="{{ route('customer.orders') }}" class="nav-link active bg-primary text-white rounded-3 py-2 px-3 fw-semibold"><i class="bx bx-package me-2"></i>{{ __('My Orders') }}</a>
                    <a href="{{ route('customer.wishlist') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-heart me-2"></i>{{ __('Wishlist') }}</a>
                    <a href="{{ route('customer.wallet') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-wallet me-2"></i>{{ __('Store Credit Wallet') }}</a>
                    <a href="{{ route('customer.loyalty') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-gift me-2"></i>{{ __('Loyalty Points') }}</a>
                    <a href="{{ route('customer.profile') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-user me-2"></i>{{ __('Profile Settings') }}</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <h4 class="fw-bold mb-3">{{ __('Order History') }}</h4>

            <div class="card border shadow-xs rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Order #') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $ord)
                                <tr>
                                    <td><strong class="text-primary">{{ $ord->order_number }}</strong></td>
                                    <td><small>{{ $ord->created_at->format('M d, Y') }}</small></td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst($ord->payment_method) }}</span></td>
                                    <td><span class="badge bg-label-primary text-uppercase">{{ $ord->order_status }}</span></td>
                                    <td><strong>${{ number_format($ord->total_amount, 2) }}</strong></td>
                                    <td class="text-end">
                                        <a href="{{ route('customer.order.details', $ord->order_number) }}" class="btn btn-sm btn-outline-primary rounded-pill">{{ __('View Invoice & Tracking') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">{{ __('You have not placed any orders yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
