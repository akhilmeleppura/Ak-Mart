@extends('layouts.storefrontMaster')

@section('title', __('Track Your Order') . ' — AK-Mart')

@section('content')
<div class="container" style="max-width: 760px;">
    <div class="card p-4 border shadow-xs rounded-4 mb-4">
        <h4 class="fw-bold mb-3"><i class="bx bx-package text-primary me-2"></i> {{ __('Track Your Order Status') }}</h4>
        <p class="text-muted small mb-4">{{ __('Enter your AK-Mart Order Number (e.g. ORD-XXXXXXXXXX) to view real-time packing and delivery progress.') }}</p>

        <form action="{{ route('storefront.track') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="order_number" class="form-control rounded-pill ps-3" placeholder="ORD-XXXXXXXXXX" value="{{ request('order_number') }}" required>
            <button class="btn btn-primary rounded-pill px-4" type="submit">{{ __('Track') }}</button>
        </form>
    </div>

    @if($order)
        <div class="card p-4 border shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1">{{ __('Order') }} #{{ $order->order_number }}</h5>
                    <small class="text-muted">{{ __('Placed on') }} {{ $order->created_at->format('M d, Y h:i A') }}</small>
                </div>
                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill text-uppercase">{{ $order->order_status }}</span>
            </div>

            <!-- Delivery Progress Pipeline -->
            <div class="d-flex justify-content-between position-relative my-4 text-center">
                <div class="position-absolute top-50 start-0 translate-middle-y w-100 bg-light" style="height: 4px; z-index: 1;"></div>
                <div class="position-relative" style="z-index: 2;">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bx bx-check fs-4"></i>
                    </div>
                    <small class="fw-bold d-block">{{ __('Received') }}</small>
                </div>
                <div class="position-relative" style="z-index: 2;">
                    <div class="avatar {{ in_array($order->order_status, ['processing', 'packed', 'shipped', 'delivered']) ? 'bg-primary text-white' : 'bg-light text-muted' }} rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bx bx-box fs-4"></i>
                    </div>
                    <small class="fw-bold d-block">{{ __('Packing') }}</small>
                </div>
                <div class="position-relative" style="z-index: 2;">
                    <div class="avatar {{ in_array($order->order_status, ['shipped', 'delivered']) ? 'bg-primary text-white' : 'bg-light text-muted' }} rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bx bx-cycling fs-4"></i>
                    </div>
                    <small class="fw-bold d-block">{{ __('On Route') }}</small>
                </div>
                <div class="position-relative" style="z-index: 2;">
                    <div class="avatar {{ $order->order_status === 'delivered' ? 'bg-success text-white' : 'bg-light text-muted' }} rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bx bx-home-heart fs-4"></i>
                    </div>
                    <small class="fw-bold d-block">{{ __('Delivered') }}</small>
                </div>
            </div>

            <div class="p-3 bg-light rounded-3 mt-4">
                <h6 class="fw-bold mb-2">{{ __('Itemized Breakdown:') }}</h6>
                @foreach($order->items as $item)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $item->qty }}x {{ $item->product_name }}</span>
                        <strong>${{ number_format($item->total_price, 2) }}</strong>
                    </div>
                @endforeach
                <div class="border-top pt-2 mt-2 d-flex justify-content-between fw-bold">
                    <span>{{ __('Total:') }}</span>
                    <span class="text-primary">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    @elseif(request('order_number'))
        <div class="alert alert-warning text-center rounded-3">
            <i class="bx bx-info-circle me-1"></i> {{ __('No order found with number:') }} <strong>{{ request('order_number') }}</strong>. {{ __('Please verify and try again.') }}
        </div>
    @endif
</div>
@endsection
