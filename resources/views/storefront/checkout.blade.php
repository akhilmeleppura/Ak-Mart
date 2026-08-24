@extends('layouts.storefrontMaster')

@section('title', __('Express Checkout') . ' — AK-Mart')

@section('styles')
<style>
    /* 3D Virtual Credit Card Container */
    .card-3d-wrapper {
        perspective: 1000px;
        max-width: 400px;
        margin: 0 auto 20px;
    }
    .card-3d-inner {
        position: relative;
        width: 100%;
        height: 220px;
        text-align: left;
        transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-style: preserve-3d;
        border-radius: 20px;
        box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.35);
    }
    .card-3d-inner.flipped {
        transform: rotateY(180deg);
    }
    .card-front, .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 20px;
        padding: 22px;
        color: #FFFFFF;
        background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4338CA 100%);
        overflow: hidden;
    }
    .card-back {
        transform: rotateY(180deg);
        padding: 20px 0;
    }
    .card-chip {
        width: 44px;
        height: 32px;
        background: linear-gradient(135deg, #FDE047 0%, #CA8A04 100%);
        border-radius: 6px;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.2);
    }
    .card-number-display {
        font-family: 'Courier New', Courier, monospace;
        font-size: 19px;
        letter-spacing: 2px;
        font-weight: 700;
        margin: 18px 0 12px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    .magnetic-stripe {
        height: 42px;
        background: #0F172A;
        width: 100%;
        margin-bottom: 15px;
    }
    .signature-bar {
        background: #F8FAFC;
        height: 36px;
        margin: 0 20px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 14px;
        color: #0F172A;
        font-family: monospace;
        font-weight: 800;
        font-size: 16px;
    }
    /* UPI Gateway Styles */
    .upi-app-btn {
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        background: #FFFFFF;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .upi-app-btn:hover {
        border-color: #4F46E5;
        background: #EEF2FF;
        transform: translateY(-2px);
    }
    .upi-qr-box {
        background: #FFFFFF;
        padding: 16px;
        border-radius: 18px;
        border: 2px dashed #6366F1;
        display: inline-block;
        box-shadow: 0 8px 24px -6px rgba(99, 102, 241, 0.2);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0"><i class="bx bx-check-shield text-primary me-2"></i> {{ __('Secure Storefront Checkout') }}</h3>
        <span class="badge bg-label-success px-3 py-2 rounded-pill font-monospace"><i class="bx bx-lock-alt me-1"></i> 256-Bit SSL Encrypted</span>
    </div>

    <form action="{{ route('storefront.checkout.process') }}" method="POST" id="checkoutMainForm">
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

                <!-- Payment Method Selection & Interactive Virtual Sandbox -->
                <div class="card p-4 border shadow-xs rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0"><i class="bx bx-credit-card-front text-primary me-2"></i> {{ __('3. Payment Method') }}</h5>
                        <span class="badge bg-label-info rounded-pill"><i class="bx bx-atom me-1"></i> Interactive Sandbox Ready</span>
                    </div>

                    <!-- Option 1: COD -->
                    <div class="form-check p-3 border rounded-3 mb-3 d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payCod" value="cod" checked onchange="togglePaymentSection('cod')">
                            <label class="form-check-label fw-bold cursor-pointer" for="payCod">
                                <i class="bx bx-money me-1 text-success fs-5 align-middle"></i> {{ __('Cash on Delivery (COD)') }}
                            </label>
                        </div>
                        <span class="badge bg-label-success">{{ __('Pay at Doorstep') }}</span>
                    </div>

                    <!-- Option 2: UPI / QR Payment -->
                    <div class="form-check p-3 border rounded-3 mb-3 d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payUpi" value="upi" onchange="togglePaymentSection('upi')">
                            <label class="form-check-label fw-bold cursor-pointer" for="payUpi">
                                <i class="bx bx-qr-scan me-1 text-primary fs-5 align-middle"></i> {{ __('UPI / Instant QR Payment') }}
                            </label>
                        </div>
                        <span class="badge bg-label-primary">{{ __('0% Fee • Instant QR') }}</span>
                    </div>

                    <!-- UPI Simulator Panel (Hidden by default) -->
                    <div id="sectionUpiSimulator" class="p-3 bg-light rounded-4 border mb-3 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark"><i class="bx bx-mobile-alt me-1 text-primary"></i> Scan Dynamic UPI QR Code</span>
                            <span class="badge bg-danger rounded-pill font-monospace" id="upiTimerBadge"><i class="bx bx-stopwatch me-1"></i> 04:59</span>
                        </div>

                        <div class="row align-items-center g-3 text-center text-md-start">
                            <div class="col-md-5 text-center">
                                <div class="upi-qr-box">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=upi%3A%2F%2Fpay%3Fpa%3Dakmart%40icici%26pn%3DAK-Mart%26am%3D{{ $finalTotal }}%26cu%3DUSD" alt="UPI QR" class="img-fluid rounded-3" style="width: 140px; height: 140px;">
                                </div>
                                <div class="small text-muted mt-2 font-monospace">akmart@icici</div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-bold">{{ __('Virtual UPI ID / VPA') }}</label>
                                <div class="input-group mb-2">
                                    <input type="text" id="inputUpiId" class="form-control" placeholder="yourname@okhdfcbank" value="tester_99@akmart">
                                    <button class="btn btn-outline-primary" type="button" onclick="generateVirtualUpi()"><i class="bx bx-refresh me-1"></i> {{ __('New ID') }}</button>
                                </div>
                                <div class="d-flex gap-2 flex-wrap mb-3">
                                    <div class="upi-app-btn" onclick="selectUpiApp('Google Pay')"><i class="bx bxl-google text-danger"></i> GPay</div>
                                    <div class="upi-app-btn" onclick="selectUpiApp('PhonePe')"><i class="bx bx-shield-quarter text-primary"></i> PhonePe</div>
                                    <div class="upi-app-btn" onclick="selectUpiApp('Paytm')"><i class="bx bx-wallet text-info"></i> Paytm</div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm rounded-pill w-100 fw-bold" onclick="simulateUpiApproval()">
                                    <i class="bx bx-check-double me-1"></i> {{ __('Simulate UPI App Instant Approval') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Option 3: Virtual Credit / Debit Card -->
                    <div class="form-check p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payCard" value="card" onchange="togglePaymentSection('card')">
                            <label class="form-check-label fw-bold cursor-pointer" for="payCard">
                                <i class="bx bx-credit-card me-1 text-info fs-5 align-middle"></i> {{ __('Credit / Debit Card (Virtual Card Ready)') }}
                            </label>
                        </div>
                        <span class="badge bg-label-info">{{ __('Visa / MC / RuPay') }}</span>
                    </div>

                    <!-- 3D Interactive Virtual Card Panel (Hidden by default) -->
                    <div id="sectionCardSimulator" class="p-3 bg-light rounded-4 border mt-3 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark"><i class="bx bx-credit-card me-1 text-primary"></i> 3D Interactive Virtual Card</span>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="generateVirtualCard()">
                                <i class="bx bx-atom me-1"></i> {{ __('Generate Virtual Card') }}
                            </button>
                        </div>

                        <!-- 3D Flipping Card -->
                        <div class="card-3d-wrapper">
                            <div class="card-3d-inner" id="interactiveCard">
                                <!-- Card Front -->
                                <div class="card-front d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="card-chip"></div>
                                        <span class="fw-bolder fs-5 text-uppercase" id="cardBrandBadge">VISA</span>
                                    </div>
                                    <div class="card-number-display text-center" id="displayCardNumber">4242 •••• •••• 4242</div>
                                    <div class="d-flex justify-content-between align-items-end small">
                                        <div>
                                            <span class="text-white-50 text-uppercase d-block" style="font-size: 9px;">Card Holder</span>
                                            <span class="fw-bold text-uppercase" id="displayCardHolder">{{ Auth::user()?->name ?? 'John Doe' }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-white-50 text-uppercase d-block" style="font-size: 9px;">Expires</span>
                                            <span class="fw-bold font-monospace" id="displayCardExpiry">12/28</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Back -->
                                <div class="card-back">
                                    <div class="magnetic-stripe"></div>
                                    <div class="signature-bar">
                                        <span id="displayCardCvv">834</span>
                                    </div>
                                    <div class="text-end pe-4 pt-3 small text-white-50" style="font-size: 10px;">
                                        AK-Mart Virtual Sandbox Card
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Form Inputs -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">{{ __('Card Number') }}</label>
                                <input type="text" id="inputCardNumber" class="form-control font-monospace" placeholder="4242 4242 4242 4242" value="4242 4242 4242 4242" maxlength="19" oninput="updateCardNumber(this)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">{{ __('Cardholder Name') }}</label>
                                <input type="text" id="inputCardHolder" class="form-control text-uppercase" placeholder="JOHN DOE" value="{{ Auth::user()?->name ?? 'JOHN DOE' }}" oninput="updateCardHolder(this)">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">{{ __('Expiry (MM/YY)') }}</label>
                                <input type="text" id="inputCardExpiry" class="form-control font-monospace" placeholder="12/28" value="12/28" maxlength="5" oninput="updateCardExpiry(this)">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">{{ __('CVV') }}</label>
                                <input type="password" id="inputCardCvv" class="form-control font-monospace" placeholder="834" value="834" maxlength="3" onfocus="flipCard(true)" onblur="flipCard(false)" oninput="updateCardCvv(this)">
                            </div>
                        </div>
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

                    <!-- VAT / Sales Tax Breakdown -->
                    @if(isset($taxAmount) && $taxAmount > 0)
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <span class="text-muted">
                                <i class="bx bx-receipt text-primary me-1"></i>{{ __('Estimated VAT / Sales Tax') }}
                                @if(!empty($isTaxInclusive))
                                    <small class="text-muted">({{ __('Included in price') }})</small>
                                @endif
                            </span>
                            <strong class="{{ !empty($isTaxInclusive) ? 'text-muted' : 'text-danger' }}">
                                {{ !empty($isTaxInclusive) ? '' : '+' }}${{ number_format($taxAmount, 2) }}
                            </strong>
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

                    <div id="storeCreditDeductionRow" class="d-flex justify-content-between mb-2 text-primary" style="display: none !important;">
                        <span><i class="bx bx-wallet me-1"></i> {{ __('Store Credit Applied') }}</span>
                        <strong id="storeCreditDeductionAmount">-$0.00</strong>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">{{ __('Total Amount') }}</span>
                        <span class="fs-4 fw-bolder text-primary" id="checkoutFinalTotalDisplay">${{ number_format($finalTotal, 2) }}</span>
                    </div>

                    <button class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm" type="button" id="checkoutSubmitBtn" onclick="handleCheckoutSubmission()">
                        <i class="bx bx-lock-alt me-1"></i> <span id="checkoutSubmitText">{{ __('Place Order Now') }} • ${{ number_format($finalTotal, 2) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Simulated 3D Secure Bank OTP Verification -->
<div class="modal fade" id="bankOtpModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h6 class="modal-title fw-bold text-white mb-0"><i class="bx bx-shield-quarter me-1"></i> 3D-Secure Bank Gateway</h6>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="small text-muted mb-2">A one-time passcode (OTP) has been sent to your registered mobile number for authentication.</p>
                <div class="badge bg-label-success mb-3 p-2 font-monospace" style="font-size: 13px;">
                    Test OTP: <strong>123456</strong>
                </div>

                <div class="mb-3">
                    <input type="text" id="bankOtpInput" class="form-control text-center font-monospace fs-4 fw-bold" placeholder="123456" maxlength="6" value="123456">
                </div>

                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" onclick="submitBankOtp()">
                    <i class="bx bx-check me-1"></i> Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const baseTotal = {{ (float)$finalTotal }};
const availableCredit = {{ (float)$walletBalance }};

function togglePaymentSection(type) {
    document.getElementById('sectionUpiSimulator').classList.add('d-none');
    document.getElementById('sectionCardSimulator').classList.add('d-none');

    if (type === 'upi') {
        document.getElementById('sectionUpiSimulator').classList.remove('d-none');
        startUpiCountdown();
    } else if (type === 'card') {
        document.getElementById('sectionCardSimulator').classList.remove('d-none');
    }
}

// 3D Card Live Sync & Flip Handlers
function flipCard(isFlipped) {
    const card = document.getElementById('interactiveCard');
    if (isFlipped) card.classList.add('flipped');
    else card.classList.remove('flipped');
}

function updateCardNumber(el) {
    let val = el.value.replace(/\D/g, '').substring(0, 16);
    let chunks = val.match(/.{1,4}/g) || [];
    el.value = chunks.join(' ');
    document.getElementById('displayCardNumber').textContent = el.value || '•••• •••• •••• ••••';

    // Detect Brand
    const badge = document.getElementById('cardBrandBadge');
    if (val.startsWith('4')) badge.textContent = 'VISA';
    else if (val.startsWith('5')) badge.textContent = 'MASTERCARD';
    else if (val.startsWith('6')) badge.textContent = 'RUPAY';
    else if (val.startsWith('3')) badge.textContent = 'AMEX';
    else badge.textContent = 'CARD';
}

function updateCardHolder(el) {
    document.getElementById('displayCardHolder').textContent = el.value || 'CARD HOLDER';
}

function updateCardExpiry(el) {
    document.getElementById('displayCardExpiry').textContent = el.value || 'MM/YY';
}

function updateCardCvv(el) {
    document.getElementById('displayCardCvv').textContent = el.value || '•••';
}

// Sandbox Virtual Number Generators
function generateVirtualCard() {
    fetch('{{ route("storefront.sandbox.generate_card") }}')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const c = data.card;
                document.getElementById('inputCardNumber').value = c.card_number;
                document.getElementById('inputCardHolder').value = c.holder_name;
                document.getElementById('inputCardExpiry').value = c.expiry;
                document.getElementById('inputCardCvv').value = c.cvv;

                document.getElementById('displayCardNumber').textContent = c.card_number;
                document.getElementById('displayCardHolder').textContent = c.holder_name;
                document.getElementById('displayCardExpiry').textContent = c.expiry;
                document.getElementById('displayCardCvv').textContent = c.cvv;
                document.getElementById('cardBrandBadge').textContent = c.brand;

                if (window.showToast) showToast('Generated fresh Virtual ' + c.brand + ' Card!', 'success');
            }
        });
}

function generateVirtualUpi() {
    fetch('{{ route("storefront.sandbox.generate_upi") }}')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('inputUpiId').value = data.upi.upi_id;
                if (window.showToast) showToast('Generated Virtual UPI: ' + data.upi.upi_id, 'info');
            }
        });
}

function selectUpiApp(appName) {
    if (window.showToast) showToast('Connected to ' + appName + ' Gateway', 'info');
}

function simulateUpiApproval() {
    if (window.showToast) showToast('UPI Payment Approved by Bank! Submitting order...', 'success');
    setTimeout(() => {
        document.getElementById('checkoutMainForm').submit();
    }, 900);
}

// UPI Timer
let timerInterval;
function startUpiCountdown() {
    let timeLeft = 300;
    const badge = document.getElementById('upiTimerBadge');
    clearInterval(timerInterval);

    timerInterval = setInterval(() => {
        timeLeft--;
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            badge.textContent = 'EXPIRED';
            return;
        }
        let m = Math.floor(timeLeft / 60);
        let s = timeLeft % 60;
        badge.innerHTML = `<i class="bx bx-stopwatch me-1"></i> 0${m}:${s < 10 ? '0' : ''}${s}`;
    }, 1000);
}

// Checkout Submission Router
function handleCheckoutSubmission() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

    if (selectedMethod === 'card') {
        const otpModal = new bootstrap.Modal(document.getElementById('bankOtpModal'));
        otpModal.show();
    } else {
        document.getElementById('checkoutMainForm').submit();
    }
}

function submitBankOtp() {
    const otp = document.getElementById('bankOtpInput').value;
    fetch('{{ route("storefront.sandbox.verify_otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ otp: otp })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.showToast) showToast('3D Secure Authenticated!', 'success');
            setTimeout(() => {
                document.getElementById('checkoutMainForm').submit();
            }, 600);
        } else {
            alert(data.message);
        }
    });
}

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
</script>
@endsection
