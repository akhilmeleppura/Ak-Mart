@extends('layouts.storefrontMaster')

@section('title', __('Order Confirmed!') . ' — AK-Mart')

@section('content')
<div class="container" style="max-width: 680px;">
    <div class="card p-5 text-center border shadow-sm rounded-4">
        <div class="avatar avatar-xl bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="bx bx-check fs-1"></i>
        </div>

        <h3 class="fw-bold mb-1">{{ __('Thank You for Your Order!') }}</h3>
        <p class="text-muted mb-4">{{ __('Your order has been placed successfully and is now being packed by our branch team.') }}</p>

        <div class="p-3 bg-light rounded-3 mb-4 text-start">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">{{ __('Order Number:') }}</span>
                <strong class="text-primary">{{ $order->order_number }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">{{ __('Payment Method:') }}</span>
                <strong>{{ ucfirst($order->payment_method) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">{{ __('Payment Status:') }}</span>
                <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">{{ __('Total Paid:') }}</span>
                <strong class="fs-5 text-dark">${{ number_format($order->total_amount, 2) }}</strong>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ route('storefront.track', ['order_number' => $order->order_number]) }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bx bx-package me-1"></i> {{ __('Live Order Tracking') }}
            </a>
            <a href="{{ route('storefront.shop') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bx bx-store me-1"></i> {{ __('Continue Shopping') }}
            </a>
        </div>
    </div>
</div>
@endsection
