@extends('layouts.storefrontMaster')

@section('title', __('Store Credit Wallet') . ' — AK-Mart')

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
                    <a href="{{ route('customer.wallet') }}" class="nav-link active bg-primary text-white rounded-3 py-2 px-3 fw-semibold"><i class="bx bx-wallet me-2"></i>{{ __('Store Credit Wallet') }}</a>
                    <a href="{{ route('customer.loyalty') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-gift me-2"></i>{{ __('Loyalty Points') }}</a>
                    <a href="{{ route('customer.profile') }}" class="nav-link text-dark rounded-3 py-2 px-3"><i class="bx bx-user me-2"></i>{{ __('Profile Settings') }}</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Balance Card -->
            <div class="card p-4 border-0 shadow-sm rounded-4 text-white mb-4" style="background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);">
                <span class="text-white-50 small text-uppercase">{{ __('Available Store Credit') }}</span>
                <h1 class="display-5 fw-bold my-1 text-white">${{ number_format($credit->balance, 2) }}</h1>
                <small class="text-white-50">{{ __('Instant 1-click checkout funds for your supermarket purchases.') }}</small>
            </div>

            <!-- Ledger Table -->
            <div class="card border shadow-xs rounded-4 overflow-hidden">
                <div class="card-header bg-white p-3 border-bottom">
                    <h6 class="mb-0 fw-bold">{{ __('Wallet Transaction History') }}</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($credit->transactions as $tx)
                                <tr>
                                    <td><small>{{ $tx->created_at->format('M d, Y h:i A') }}</small></td>
                                    <td>
                                        <span class="badge {{ $tx->type === 'credit' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($tx->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="{{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                        </strong>
                                    </td>
                                    <td><small class="text-muted">{{ $tx->notes ?? __('Account credit') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">{{ __('No wallet transactions recorded yet.') }}</td>
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
