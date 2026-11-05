@extends('layouts.storefrontMaster')

@section('title', __('Shopping Cart') . ' — AK-Mart')

@section('styles')
<style>
    .cart-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .cart-table th {
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748B;
        background: #F8FAFC;
        padding: 14px 16px;
        border-bottom: 1.5px solid #E2E8F0;
    }
    .cart-table td {
        padding: 18px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
    }
    .cart-table tr:last-child td {
        border-bottom: none;
    }
    .cart-prod-img {
        width: 64px;
        height: 64px;
        min-width: 64px;
        border-radius: 14px;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        object-fit: contain;
        padding: 4px;
        transition: transform 0.2s ease;
    }
    .cart-prod-img:hover {
        transform: scale(1.05);
    }
    .cart-prod-name {
        font-size: 14.5px;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.35;
        margin-bottom: 4px;
    }
    .cart-qty-stepper {
        display: inline-flex;
        align-items: center;
        background: #F1F5F9;
        border: 1.5px solid #E2E8F0;
        border-radius: 9999px;
        padding: 3px 5px;
        gap: 2px;
        transition: all 0.2s ease;
        user-select: none;
    }
    .cart-qty-stepper:hover {
        border-color: #CBD5E1;
        background: #EEF2FF;
    }
    .cart-qty-btn {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 50%;
        border: none;
        background: #FFFFFF;
        color: #334155;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 0;
        line-height: 1;
    }
    .cart-qty-btn:hover {
        background: #4F46E5;
        color: #FFFFFF;
        transform: scale(1.1);
        box-shadow: 0 3px 8px rgba(79, 70, 229, 0.3);
    }
    .cart-qty-btn:active {
        transform: scale(0.92);
    }
    .cart-qty-val {
        width: 38px;
        min-width: 38px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 800;
        font-size: 14.5px;
        color: #0F172A;
        outline: none;
        padding: 0;
    }
    .cart-price-text {
        font-size: 15px;
        font-weight: 600;
        color: #475569;
    }
    .cart-total-text {
        font-size: 16px;
        font-weight: 800;
        color: #4F46E5;
    }
    /* ── Cart Recommendations ───────── */
    .rec-section-title {
        font-size: 20px;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.02em;
    }
    .rec-card {
        background: #fff;
        border: 1.5px solid #E2E8F0;
        border-radius: 20px;
        overflow: hidden;
        transition: transform 0.22s cubic-bezier(.16,1,.3,1), box-shadow 0.22s ease, border-color 0.2s ease;
        position: relative;
        cursor: pointer;
    }
    .rec-card:hover {
        transform: translateY(-5px) scale(1.015);
        box-shadow: 0 16px 48px rgba(79,70,229,0.14);
        border-color: #A5B4FC;
    }
    .rec-card-img-wrap {
        width: 100%;
        aspect-ratio: 1/1;
        background: #F8FAFC;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .rec-card-img {
        width: 80%;
        height: 80%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .rec-card:hover .rec-card-img {
        transform: scale(1.08);
    }
    .rec-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: 0.04em;
        padding: 3px 8px;
        border-radius: 99px;
    }
    .rec-card-body {
        padding: 14px 14px 16px;
    }
    .rec-cat-tag {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #6366F1;
        margin-bottom: 4px;
    }
    .rec-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.3;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 36px;
    }
    .rec-price {
        font-size: 16px;
        font-weight: 900;
        color: #4F46E5;
    }
    .rec-price-old {
        font-size: 12px;
        color: #94A3B8;
        text-decoration: line-through;
        margin-left: 4px;
    }
    .rec-add-btn {
        width: 100%;
        margin-top: 10px;
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: #fff;
        border: none;
        border-radius: 99px;
        font-size: 12.5px;
        font-weight: 700;
        padding: 9px 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(.16,1,.3,1);
        white-space: nowrap;
    }
    .rec-add-btn:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 20px rgba(79,70,229,0.4);
    }
    .rec-add-btn:active {
        transform: scale(0.97);
    }
    .rec-add-btn.adding {
        background: linear-gradient(135deg, #059669 0%, #10B981 100%);
    }
    .rec-scroll {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
    }
    @media (min-width: 576px) {
        .rec-scroll { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
    }
</style>
@endsection


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
                <div class="card p-0 border shadow-xs rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table cart-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 260px;">{{ __('Product') }}</th>
                                    <th style="min-width: 90px;">{{ __('Price') }}</th>
                                    <th class="text-center" style="min-width: 140px;">{{ __('Quantity') }}</th>
                                    <th class="text-end" style="min-width: 100px;">{{ __('Total') }}</th>
                                    <th class="text-end" style="min-width: 140px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                    <tr id="cartRow-{{ $id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item['image'] ? asset($item['image']) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="cart-prod-img">
                                                <div style="max-width: 320px;">
                                                    <h6 class="cart-prod-name">{{ $item['name'] }}</h6>
                                                    <span class="badge bg-light text-muted border small font-monospace">SKU: {{ $item['sku'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="cart-price-text">${{ number_format($item['price'], 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="cart-qty-stepper">
                                                <button type="button" class="cart-qty-btn" onclick="changeQty({{ $id }}, -1)" title="{{ __('Decrease') }}">
                                                    <i class="bx bx-minus"></i>
                                                </button>
                                                <input type="text" class="cart-qty-val" id="cartQty-{{ $id }}" value="{{ $item['qty'] }}" readonly>
                                                <button type="button" class="cart-qty-btn" onclick="changeQty({{ $id }}, 1)" title="{{ __('Increase') }}">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="cart-total-text" id="rowTotal-{{ $id }}">${{ number_format($item['price'] * $item['qty'], 2) }}</span>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 px-2.5 py-1 text-nowrap small" onclick="saveForLaterAjax({{ $id }})" title="{{ __('Save for Later') }}">
                                                <i class="bx bx-bookmark align-middle"></i> {{ __('Save for Later') }}
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="removeCartItem({{ $id }})" title="{{ __('Remove') }}">
                                                <i class="bx bx-trash fs-5 align-middle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Saved for Later Shelf -->
                @if(!empty($savedForLater) && count($savedForLater) > 0)
                    <div class="card p-4 border shadow-xs rounded-4 mt-4">
                        <h5 class="fw-bold mb-3"><i class="bx bx-bookmark-heart text-primary me-2"></i> {{ __('Saved for Later') }} ({{ count($savedForLater) }} {{ __('items') }})</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($savedForLater as $sId => $sItem)
                                        <tr id="savedRow-{{ $sId }}">
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $sItem['image'] ? asset($sItem['image']) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $sItem['name'] }}" width="45" height="45" class="rounded object-fit-contain bg-light p-1">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $sItem['name'] }}</h6>
                                                        <small class="text-muted">SKU: {{ $sItem['sku'] ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-primary">${{ number_format($sItem['price'], 2) }}</td>
                                            <td class="text-end text-nowrap">
                                                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 me-1 small" onclick="moveToCartFromSavedAjax({{ $sId }})">
                                                    <i class="bx bx-cart-add me-1 align-middle"></i> {{ __('Move to Cart') }}
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeSavedAjax({{ $sId }})" title="{{ __('Delete') }}">
                                                    <i class="bx bx-trash fs-5"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Summary Column -->
            <div class="col-lg-4">
                <div class="card p-4 border shadow-xs rounded-4 mb-3">
                    <h5 class="fw-bold mb-3">{{ __('Order Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Subtotal') }}</span>
                        <strong id="cartSubtotal">${{ number_format($subtotal, 2) }}</strong>
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
                        <span class="text-muted">{{ __('Estimated Delivery') }}</span>
                        <span class="text-success fw-bold">{{ __('FREE') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">{{ __('Estimated Total') }}</span>
                        <span class="fs-5 fw-bold text-primary" id="cartTotal">${{ number_format($finalTotal, 2) }}</span>
                    </div>

                    <!-- Coupon Code & Smart Offers Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label class="form-label small fw-semibold text-muted mb-0">{{ __('Have a promo code?') }}</label>
                            <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-bold small" onclick="openAvailableCouponsModal()">
                                <i class="bx bxs-coupon me-0.5 align-middle"></i> {{ __('View Available Offers') }}
                            </button>
                        </div>
                        <div class="input-group">
                            <input type="text" id="couponCodeInput" class="form-control text-uppercase font-monospace" placeholder="e.g. WELCOME10" value="{{ $coupon['code'] ?? '' }}">
                            <button class="btn btn-outline-primary fw-semibold" type="button" onclick="applyCouponAjax()">{{ __('Apply') }}</button>
                        </div>
                        <div id="couponFeedback" class="small mt-1"></div>
                        
                        @if(empty($coupon))
                            <div class="mt-2.5 p-2 rounded-3 bg-light bg-opacity-70 border d-flex justify-content-between align-items-center small">
                                <span class="text-muted"><i class="bx bx-sparkles text-warning me-1"></i>{{ __('Want the best discount?') }}</span>
                                <button type="button" class="btn btn-xs btn-primary rounded-pill px-2.5 py-0.5 fw-bold" onclick="autoApplyBestCouponAjax()" style="font-size: 11px;">
                                    {{ __('Auto-Apply Best') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('storefront.checkout') }}" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold">
                        {{ __('Proceed to Checkout') }} <i class="bx bx-right-arrow-alt align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         CART RECOMMENDATIONS SECTION
         ═══════════════════════════════════════════════════════════ --}}
    @if(!empty($recEnabled) && $recommendedProducts->count() > 0)
    <div class="mt-5 pt-2" id="cartRecommendationsSection">
        {{-- Section Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px;height:42px;background:linear-gradient(135deg,#4F46E5,#7C3AED);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                    <i class="bx bx-sparkles text-white fs-5"></i>
                </div>
                <div>
                    <h4 class="rec-section-title mb-0">{{ $recTitle ?? __('You Might Also Like') }}</h4>
                    <small class="text-muted">{{ __('Handpicked to pair with your order') }}</small>
                </div>
            </div>
            <a href="{{ route('storefront.shop') }}" class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-semibold">
                <i class="bx bx-grid me-1"></i>{{ __('Browse All') }}
            </a>
        </div>

        {{-- Product Grid --}}
        <div class="rec-scroll">
            @foreach($recommendedProducts as $rp)
            @php
                $rpImg    = $rp->image ? asset($rp->image) : asset('assets/img/illustrations/boy-with-rocket-light.png');
                $rpCat    = $rp->category->name ?? '';
                $rpIsDeal = $rp->deal_of_the_day ?? false;
                $rpIsTrend= $rp->is_trending ?? false;
                $rpIsBest = $rp->is_best_seller ?? false;
                $rpIsNew  = $rp->is_new_arrival ?? false;
            @endphp
            <div class="rec-card" onclick="window.location='{{ route('storefront.product', $rp->id) }}'">
                <div class="rec-card-img-wrap">
                    <img src="{{ $rpImg }}" alt="{{ $rp->name }}" class="rec-card-img">
                    @if($rpIsDeal)
                        <span class="rec-badge bg-danger text-white">🔥 {{ __('Deal') }}</span>
                    @elseif($rpIsBest)
                        <span class="rec-badge bg-warning text-dark">⭐ {{ __('Bestseller') }}</span>
                    @elseif($rpIsTrend)
                        <span class="rec-badge bg-info text-white">📈 {{ __('Trending') }}</span>
                    @elseif($rpIsNew)
                        <span class="rec-badge bg-success text-white">✨ {{ __('New') }}</span>
                    @endif
                </div>
                <div class="rec-card-body">
                    @if($rpCat)
                        <div class="rec-cat-tag">{{ $rpCat }}</div>
                    @endif
                    <div class="rec-name" title="{{ $rp->name }}">{{ $rp->name }}</div>
                    <div class="d-flex align-items-baseline">
                        <span class="rec-price">${{ number_format($rp->price, 2) }}</span>
                        @if(isset($rp->compare_price) && $rp->compare_price > $rp->price)
                            <span class="rec-price-old">${{ number_format($rp->compare_price, 2) }}</span>
                        @endif
                    </div>
                    <button type="button" class="rec-add-btn" id="recBtn-{{ $rp->id }}"
                        onclick="event.stopPropagation(); addRecToCart({{ $rp->id }}, this)">
                        <i class="bx bx-cart-add" style="font-size:16px;"></i>
                        <span>{{ __('Add to Cart') }}</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@include('storefront.partials.coupon-drawer')
@endsection

@section('scripts')
<script>
function applyCouponAjax() {
    const code = document.getElementById('couponCodeInput').value.trim();
    const feedback = document.getElementById('couponFeedback');
    if (!code) {
        feedback.innerHTML = '<span class="text-danger small">{{ __("Please enter a code.") }}</span>';
        return;
    }

    fetch('{{ route("storefront.coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: code })
    })
    .then(r => r.json().then(data => ({ status: r.status, body: data })))
    .then(({ status, body }) => {
        if (body.success) {
            showToast(body.message, 'success');
            setTimeout(() => window.location.reload(), 600);
        } else {
            feedback.innerHTML = `<span class="text-danger small">${body.message || 'Invalid coupon'}</span>`;
            showToast(body.message || 'Invalid coupon', 'primary');
        }
    })
    .catch(err => {
        feedback.innerHTML = '<span class="text-danger small">{{ __("Error applying coupon.") }}</span>';
    });
}

function changeQty(productId, delta) {
    const input = document.getElementById('cartQty-' + productId);
    let current = parseInt(input.value) || 1;
    let qty = current + delta;
    if (qty <= 0) {
        removeCartItem(productId);
        return;
    }
    input.value = qty;
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

function saveForLaterAjax(productId) {
    fetch('{{ route("storefront.cart.save_for_later") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'primary');
            setTimeout(() => window.location.reload(), 400);
        }
    });
}

function moveToCartFromSavedAjax(productId) {
    fetch('{{ route("storefront.cart.move_to_cart") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 400);
        }
    });
}

function removeSavedAjax(productId) {
    fetch('{{ route("storefront.cart.remove_saved") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'secondary');
            setTimeout(() => window.location.reload(), 400);
        }
    });
}

function addRecToCart(productId, btn) {
    // Visual: loading state
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin" style="font-size:16px;"></i><span>{{ __("Adding...") }}</span>';
    btn.disabled = true;

    fetch('{{ route("storefront.cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, qty: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Success state
            btn.classList.add('adding');
            btn.innerHTML = '<i class="bx bx-check-circle" style="font-size:16px;"></i><span>{{ __("Added!") }}</span>';

            // Update cart count badge in header if present
            const badge = document.querySelector('.cart-count-badge, [data-cart-count], .header-cart-count');
            if (badge && data.cartCount !== undefined) {
                badge.textContent = data.cartCount;
            }

            // Bump cart subtotal display if present on page
            if (data.cartSubtotal !== undefined) {
                const sub = document.getElementById('cartSubtotal');
                if (sub) sub.textContent = '$' + parseFloat(data.cartSubtotal).toFixed(2);
            }

            showToast(data.message || '{{ __("Added to cart!") }}', 'success');

            // Slide out the card after a short delay
            setTimeout(() => {
                const card = btn.closest('.rec-card');
                if (card) {
                    card.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        // If grid is empty, hide the whole section
                        const grid = document.querySelector('.rec-scroll');
                        if (grid && grid.children.length === 0) {
                            const section = document.getElementById('cartRecommendationsSection');
                            if (section) section.style.display = 'none';
                        }
                    }, 350);
                }
            }, 900);
        } else {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            showToast(data.message || '{{ __("Could not add to cart.") }}', 'danger');
        }
    })
    .catch(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        showToast('{{ __("Error adding to cart.") }}', 'danger');
    });
}
</script>
@endsection

