@extends('layouts.storefrontMaster')

@section('title', __('Shopping Cart') . ' — AK-Mart')

@section('content')
<div class="container">
    <h3 class="fw-bold mb-4"><i class="bx bx-shopping-bag text-primary me-2"></i> {{ __('Your Grocery Basket') }}</h3>

    @if(empty($cart))
        <div class="card p-5 text-center border shadow-xs rounded-4">
            <i class="bx bx-cart fs-1 text-muted mb-3"></i>
            <h4 class="fw-bold text-muted">{{ __('Your cart is currently empty') }}</h4>
            <p class="text-muted">{{ __('Add fresh groceries and mini-mart essentials to proceed with checkout.') }}</p>
            <div>
                <a href="{{ route('storefront.shop') }}" class="btn btn-primary rounded-pill px-4 mt-2">{{ __('Start Shopping') }}</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card p-3 border shadow-xs rounded-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="text-center" style="width: 140px;">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Total') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                    <tr id="cartRow-{{ $id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item['image'] ? asset($item['image']) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $item['name'] }}" width="50" height="50" class="rounded object-fit-contain bg-light p-1">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $item['name'] }}</h6>
                                                    <small class="text-muted">SKU: {{ $item['sku'] ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item['price'], 2) }}</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" onclick="changeQty({{ $id }}, -1)">-</button>
                                                <input type="text" class="form-control text-center" id="cartQty-{{ $id }}" value="{{ $item['qty'] }}" readonly>
                                                <button class="btn btn-outline-secondary" onclick="changeQty({{ $id }}, 1)">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-primary" id="rowTotal-{{ $id }}">${{ number_format($item['price'] * $item['qty'], 2) }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-link text-danger p-0" onclick="removeCartItem({{ $id }})"><i class="bx bx-trash fs-5"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary Column -->
            <div class="col-lg-4">
                <div class="card p-4 border shadow-xs rounded-4">
                    <h5 class="fw-bold mb-3">{{ __('Order Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Subtotal') }}</span>
                        <strong id="cartSubtotal">${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Estimated Delivery') }}</span>
                        <span class="text-success fw-bold">{{ __('FREE') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">{{ __('Estimated Total') }}</span>
                        <span class="fs-5 fw-bold text-primary" id="cartTotal">${{ number_format($subtotal, 2) }}</span>
                    </div>

                    <a href="{{ route('storefront.checkout') }}" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold">
                        {{ __('Proceed to Checkout') }} <i class="bx bx-right-arrow-alt align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function changeQty(productId, delta) {
    const input = document.getElementById('cartQty-' + productId);
    let qty = parseInt(input.value) + delta;
    if (qty <= 0) {
        removeCartItem(productId);
        return;
    }
    updateCartAjax(productId, qty);
}

function removeCartItem(productId) {
    updateCartAjax(productId, 0);
}

function updateCartAjax(productId, qty) {
    fetch('{{ route("storefront.cart.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, qty: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}
</script>
@endsection
