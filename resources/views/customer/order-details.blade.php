@extends('layouts.storefrontMaster')

@section('title', __('Order Details') . ' #' . $order->order_number . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('customer.orders') }}" class="text-muted text-decoration-none small mb-1 d-inline-block">← {{ __('Back to Orders') }}</a>
            <h4 class="fw-bold mb-0">{{ __('Order') }} #{{ $order->order_number }}</h4>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bx bx-printer me-1"></i> {{ __('Print Invoice') }}</button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card p-4 border shadow-xs rounded-4 mb-4">
                <h5 class="fw-bold mb-3">{{ __('Ordered Products') }}</h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Unit Price') }}</th>
                                <th>{{ __('Qty') }}</th>
                                <th class="text-end">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">{{ $item->product_name }}</h6>
                                        <small class="text-muted">SKU: {{ $item->product?->sku ?? 'N/A' }}</small>
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td class="text-end fw-bold text-primary">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Summary & Delivery -->
        <div class="col-lg-4">
            <div class="card p-4 border shadow-xs rounded-4 mb-4">
                <h5 class="fw-bold mb-3">{{ __('Payment & Shipping') }}</h5>
                <div class="mb-3">
                    <small class="text-muted d-block">{{ __('Delivery Address:') }}</small>
                    <strong>{{ $order->shipping_address }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">{{ __('Payment Method:') }}</small>
                    <span class="badge bg-light text-dark">{{ ucfirst($order->payment_method) }}</span>
                    <span class="badge bg-success ms-1">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Subtotal') }}</span>
                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Delivery') }}</span>
                    <span class="text-success fw-bold">{{ __('FREE') }}</span>
                </div>
                <div class="d-flex justify-content-between fs-5 fw-bold mt-2">
                    <span>{{ __('Total') }}</span>
                    <span class="text-primary">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
