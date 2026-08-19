@extends('layouts.storefrontMaster')

@section('title', __('Express Checkout') . ' — AK-Mart')

@section('content')
<div class="container">
    <h3 class="fw-bold mb-4"><i class="bx bx-check-shield text-primary me-2"></i> {{ __('Secure Storefront Checkout') }}</h3>

    <form action="{{ route('storefront.checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Customer & Shipping Form -->
            <div class="col-lg-7">
                <div class="card p-4 border shadow-xs rounded-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bx bx-user-pin text-primary me-2"></i> {{ __('1. Customer & Delivery Information') }}</h5>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ __('Full Name') }}</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ Auth::user()?->name ?? 'John Doe' }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Email Address') }}</label>
                            <input type="email" name="customer_email" class="form-control" value="{{ Auth::user()?->email ?? 'customer@example.com' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Phone Number') }}</label>
                            <input type="text" name="customer_phone" class="form-control" value="{{ Auth::user()?->phone ?? '+1 (555) 019-2834' }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ __('Delivery Address (House/Apt, Street, City, Zip)') }}</label>
                        <textarea name="shipping_address" class="form-control" rows="3" required placeholder="123 Market Street, Apt 4B, Central District, 10001">123 Market Street, Apt 4B, Central District, 10001</textarea>
                    </div>
                </div>

                <!-- Delivery Time Slot Selection -->
                <div class="card p-4 border shadow-xs rounded-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bx bx-time text-primary me-2"></i> {{ __('2. Preferred Delivery Time Slot') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Choose your convenient delivery window. Guaranteed on-time fresh delivery.') }}</p>

                    <div class="row g-2">
                        @foreach($deliverySlots as $index => $slot)
                            <div class="col-md-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 w-100 cursor-pointer hover-shadow" for="slot-{{ $slot->id }}">
                                    <input class="form-check-input mt-0" type="radio" name="delivery_slot_id" id="slot-{{ $slot->id }}" value="{{ $slot->id }}" {{ $index === 0 ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold small">{{ $slot->name }}</div>
                                        <span class="text-muted small font-monospace"><i class="bx bx-timer align-middle"></i> {{ date('h:i A', strtotime($slot->start_time)) }} – {{ date('h:i A', strtotime($slot->end_time)) }}</span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="card p-4 border shadow-xs rounded-4">
                    <h5 class="fw-bold mb-3"><i class="bx bx-credit-card-front text-primary me-2"></i> {{ __('3. Payment Method') }}</h5>

                    <div class="form-check p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payCod" value="cod" checked>
                            <label class="form-check-label fw-bold" for="payCod">
                                <i class="bx bx-money me-1 text-success fs-5 align-middle"></i> {{ __('Cash on Delivery (COD)') }}
                            </label>
                        </div>
                        <span class="badge bg-label-success">{{ __('Instant Available') }}</span>
                    </div>

                    <div class="form-check p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payUpi" value="upi">
                            <label class="form-check-label fw-bold" for="payUpi">
                                <i class="bx bx-qr-scan me-1 text-primary fs-5 align-middle"></i> {{ __('UPI / Instant QR Payment') }}
                            </label>
                        </div>
                        <span class="badge bg-label-primary">{{ __('0% Fee') }}</span>
                    </div>

                    <div class="form-check p-3 border rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payCard" value="card">
                            <label class="form-check-label fw-bold" for="payCard">
                                <i class="bx bx-credit-card me-1 text-info fs-5 align-middle"></i> {{ __('Credit / Debit Card') }}
                            </label>
                        </div>
                        <span class="badge bg-label-info">{{ __('Visa / MC') }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Review & Submit -->
            <div class="col-lg-5">
                <div class="card p-4 border shadow-xs rounded-4">
                    <h5 class="fw-bold mb-3">{{ __('Order Review') }} ({{ count($cart) }} {{ __('items') }})</h5>

                    <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                        @foreach($cart as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark">{{ $item['qty'] }}x</span>
                                    <span class="small fw-semibold text-truncate" style="max-width: 190px;">{{ $item['name'] }}</span>
                                </div>
                                <span class="small fw-bold">${{ number_format($item['price'] * $item['qty'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Subtotal') }}</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>

                    @if(!empty($coupon))
                        <div class="d-flex justify-content-between mb-2 text-success align-items-center">
                            <span><i class="bx bxs-purchase-tag me-1"></i> {{ __('Coupon') }} (<strong>{{ $coupon['code'] }}</strong>)</span>
                            <div>
                                <span class="fw-bold">-${{ number_format($couponDiscount, 2) }}</span>
                                <form action="{{ route('storefront.coupon.remove') }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="{{ __('Remove Coupon') }}"><i class="bx bx-x-circle align-middle"></i></button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Express Delivery') }}</span>
                        <span class="text-success fw-bold">{{ __('FREE') }}</span>
                    </div>

                    @if(Auth::check())
                        <div class="p-3 bg-light rounded-3 mb-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="bx bx-wallet text-primary me-1"></i> {{ __('Available Store Credit:') }}</span>
                                <strong class="text-primary fs-6">${{ number_format($walletBalance, 2) }}</strong>
                            </div>
                            @if($walletBalance > 0)
                                <div class="form-check form-switch pt-1 border-top">
                                    <input class="form-check-input" type="checkbox" name="use_store_credit" id="useStoreCreditSwitch" value="1" onchange="toggleStoreCredit(this)">
                                    <label class="form-check-label fw-bold text-dark" for="useStoreCreditSwitch">
                                        {{ __('Apply Store Credit Balance') }}
                                    </label>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mt-2 pt-1 border-top">
                                <span><i class="bx bx-gift text-warning me-1"></i> {{ __('Loyalty Points:') }}</span>
                                <strong>{{ $loyaltyPoints }} pts</strong>
                            </div>
                        </div>
                    @endif

                    @if(empty($coupon))
                        <!-- Optional Coupon Box on Checkout -->
                        <div class="mb-3">
                            <div class="input-group input-group-sm">
                                <input type="text" id="checkoutCouponInput" class="form-control text-uppercase" placeholder="{{ __('Promo code (e.g. WELCOME10)') }}">
                                <button class="btn btn-outline-primary" type="button" onclick="applyCheckoutCoupon()">{{ __('Apply') }}</button>
                            </div>
                        </div>
                    @endif

                    <div id="storeCreditDeductionRow" class="d-flex justify-content-between mb-2 text-primary" style="display: none !important;">
                        <span><i class="bx bx-wallet me-1"></i> {{ __('Store Credit Applied') }}</span>
                        <strong id="storeCreditDeductionAmount">-$0.00</strong>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">{{ __('Total Amount') }}</span>
                        <span class="fs-4 fw-bolder text-primary" id="checkoutFinalTotalDisplay">${{ number_format($finalTotal, 2) }}</span>
                    </div>

                    <button class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm" type="submit" id="checkoutSubmitBtn">
                        <i class="bx bx-lock-alt me-1"></i> <span id="checkoutSubmitText">{{ __('Place Order Now') }} • ${{ number_format($finalTotal, 2) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
const baseTotal = {{ (float)$finalTotal }};
const availableCredit = {{ (float)$walletBalance }};

function toggleStoreCredit(checkbox) {
    const deductionRow = document.getElementById('storeCreditDeductionRow');
    const deductionAmountEl = document.getElementById('storeCreditDeductionAmount');
    const finalTotalDisplay = document.getElementById('checkoutFinalTotalDisplay');
    const submitText = document.getElementById('checkoutSubmitText');

    if (checkbox.checked) {
        const creditToUse = Math.min(availableCredit, baseTotal);
        const newTotal = Math.max(0, baseTotal - creditToUse);
        
        deductionRow.style.display = 'flex';
        deductionRow.style.setProperty('display', 'flex', 'important');
        deductionAmountEl.textContent = `-$${creditToUse.toFixed(2)}`;
        finalTotalDisplay.textContent = `$${newTotal.toFixed(2)}`;
        submitText.textContent = newTotal === 0 ? `Pay with Store Credit • $0.00` : `Place Order Now • $${newTotal.toFixed(2)}`;
    } else {
        deductionRow.style.display = 'none';
        deductionRow.style.setProperty('display', 'none', 'important');
        finalTotalDisplay.textContent = `$${baseTotal.toFixed(2)}`;
        submitText.textContent = `Place Order Now • $${baseTotal.toFixed(2)}`;
    }
}

function applyCheckoutCoupon() {
    const code = document.getElementById('checkoutCouponInput').value.trim();
    if (!code) return;

    fetch('{{ route("storefront.coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: code })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast(data.message || 'Invalid coupon', 'primary');
        }
    });
}
</script>
@endsection
