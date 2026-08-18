@extends('layouts/layoutMaster')

@section('title', 'POS Quick Sale Terminal — AK-Mart')

@section('vendor-style')
<style>
    .pos-product-card {
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-radius: var(--ak-radius);
    }
    .pos-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: var(--ak-primary) !important;
    }
    .pos-cart {
        height: calc(100vh - 360px);
        min-height: 280px;
        overflow-y: auto;
    }
    .qty-btn {
        width: 24px;
        height: 24px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .category-pill {
        cursor: pointer;
        transition: all 0.2s;
    }
    .category-pill.active {
        background-color: var(--ak-primary);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bx bx-terminal text-primary me-2"></i> {{ __('POS Quick Sale Terminal') }}</h4>
        <small class="text-muted">{{ __('Barcode scanner ready • Real-time inventory sync • Instant receipt generation') }}</small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-outline-warning btn-sm" onclick="resumeHeldSale()" id="btn-held-sales">
            <i class="bx bx-pause-circle me-1"></i> {{ __('Held Sales') }} (<span id="held-count">0</span>)
        </button>
        <span class="badge bg-label-primary px-3 py-2 fs-6"><i class="bx bx-store me-1"></i> {{ auth()->user()->branch->name ?? __('AK-Mart Store') }}</span>
    </div>
</div>

<div class="row g-3">
    {{-- Product Catalog & Barcode Scanner Area --}}
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border">
            <div class="card-header border-bottom bg-surface py-3">
                <div class="input-group input-group-lg mb-2">
                    <span class="input-group-text bg-white border-end-0"><i class="bx bx-barcode-reader text-primary fs-3"></i></span>
                    <input type="text" id="barcode-search" class="form-control border-start-0 ps-0" placeholder="{{ __('Scan Barcode / SKU / Product Name... (Press Enter)') }}" autofocus>
                </div>

                {{-- Category Filter Pills --}}
                <div class="d-flex gap-1 overflow-auto py-1" id="category-filter">
                    <button class="btn btn-xs btn-outline-primary category-pill active" onclick="filterCategory('')">{{ __('All Products') }}</button>
                    @foreach($categories as $cat)
                        <button class="btn btn-xs btn-outline-primary category-pill" onclick="filterCategory('{{ $cat->id }}')">{{ $cat->name }}</button>
                    @endforeach
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row g-3" id="product-grid">
                    @forelse($products as $p)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 product-item" data-category="{{ $p->category_id }}" data-name="{{ strtolower($p->name) }}" data-sku="{{ strtolower($p->sku ?? '') }}" data-barcode="{{ $p->barcode ?? '' }}">
                        <div class="card border pos-product-card h-100" onclick="addToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $p->qty }})">
                            <div class="position-relative">
                                <img src="{{ asset($p->image ?: 'assets/img/ecommerce-images/product-1.png') }}" class="card-img-top" height="100" style="object-fit: cover;" onerror="this.src='{{ asset('assets/img/ecommerce-images/product-1.png') }}'">
                                <span class="position-absolute top-0 end-0 badge bg-dark opacity-75 m-1 small">{{ $p->category?->name ?? __('General') }}</span>
                            </div>
                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                <h6 class="mb-1 text-truncate fw-bold text-heading small" title="{{ $p->name }}">{{ $p->name }}</h6>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-primary fw-bold">${{ number_format($p->price, 2) }}</span>
                                        <small class="badge {{ $p->qty > 5 ? 'bg-label-success' : ($p->qty > 0 ? 'bg-label-warning' : 'bg-label-danger') }}">
                                            {{ __('Stock') }}: {{ $p->qty }}
                                        </small>
                                    </div>
                                    <small class="text-muted d-block small">{{ __('SKU') }}: {{ $p->sku ?: 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bx bx-package fs-1 opacity-50 mb-2 d-block"></i>
                        {{ __('No active products found in inventory.') }}
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Cart, Customer & Checkout Sidebar --}}
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border">
            {{-- Customer Selector --}}
            <div class="card-header border-bottom bg-light py-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold mb-0"><i class="bx bx-user text-primary me-1"></i> {{ __('Customer') }}</label>
                    <a href="javascript:void(0);" class="small text-primary" onclick="clearCart()"><i class="bx bx-trash me-1"></i> {{ __('Clear Cart') }}</a>
                </div>
                <select id="pos-customer-select" class="form-select form-select-sm">
                    <option value="">{{ __('Walk-in Customer (General)') }}</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Cart List --}}
            <div class="card-body p-0 pos-cart">
                <ul class="list-group list-group-flush" id="cart-list">
                    {{-- Populated by JS --}}
                </ul>
            </div>

            {{-- Discount, Tax & Totals Footer --}}
            <div class="card-footer border-top p-3 bg-light">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">{{ __('Disc $') }}</span>
                            <input type="number" id="cart-discount" class="form-control" value="0" min="0" oninput="renderCart()">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">{{ __('Tax %') }}</span>
                            <input type="number" id="cart-tax-rate" class="form-control" value="5" min="0" oninput="renderCart()">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-1 text-muted small">
                    <span>{{ __('Subtotal') }}</span>
                    <span id="subtotal" class="fw-semibold">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted small">
                    <span>{{ __('Discount') }}</span>
                    <span id="discount-amount" class="text-danger">-$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted small">
                    <span>{{ __('Tax Amount') }}</span>
                    <span id="tax-amount" class="fw-semibold">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-top pt-2">
                    <h5 class="fw-bold mb-0">{{ __('Total Payable') }}</h5>
                    <h4 id="total" class="fw-bold text-primary mb-0">$0.00</h4>
                </div>

                {{-- Action Buttons --}}
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <button class="btn btn-outline-warning w-100 btn-sm" onclick="holdCurrentSale()">
                            <i class="bx bx-pause me-1"></i> {{ __('Hold Sale') }}
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-info w-100 btn-sm" onclick="checkout('upi')">
                            <i class="bx bx-qr me-1"></i> {{ __('UPI / QR') }}
                        </button>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-success w-100 py-2 fw-bold" onclick="checkout('cash')">
                            <i class="bx bx-money me-1"></i> {{ __('Cash Checkout') }}
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-primary w-100 py-2 fw-bold" onclick="checkout('card')">
                            <i class="bx bx-credit-card me-1"></i> {{ __('Card Checkout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-bottom pb-2">
                <h5 class="modal-title fw-bold" id="receiptModalLabel">{{ __('Sales Receipt') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body py-3" id="receiptContent">
                <!-- Dynamic Receipt Content -->
            </div>
            <div class="modal-footer border-top pt-2">
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">{{ __('Done') }}</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer me-1"></i> {{ __('Print Receipt') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let heldSales = [];

function addToCart(id, name, price, maxStock) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.qty < maxStock) {
            existing.qty++;
        } else {
            Swal.fire({ icon: 'warning', title: 'Stock Limit Reached', text: `Only ${maxStock} units in stock.`, timer: 1200, showConfirmButton: false });
        }
    } else {
        if (maxStock <= 0) {
            return Swal.fire({ icon: 'error', title: 'Out of Stock', text: 'This product is out of stock.', timer: 1200, showConfirmButton: false });
        }
        cart.push({ id, name, price: parseFloat(price), qty: 1, maxStock });
    }
    renderCart();
}

function updateQty(index, delta) {
    if (cart[index]) {
        const newQty = cart[index].qty + delta;
        if (newQty <= 0) {
            removeFromCart(index);
        } else if (newQty <= cart[index].maxStock) {
            cart[index].qty = newQty;
            renderCart();
        } else {
            Swal.fire({ icon: 'warning', title: 'Stock Limit', text: `Maximum available is ${cart[index].maxStock}.`, timer: 1200, showConfirmButton: false });
        }
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cart-list');
    list.innerHTML = '';
    let subtotal = 0;

    if (cart.length === 0) {
        list.innerHTML = `
            <li class="list-group-item text-center py-5 text-muted border-0">
                <i class="bx bx-cart fs-1 opacity-25 d-block mb-2"></i>
                Cart is empty.<br>Scan barcode or click items to add.
            </li>
        `;
    } else {
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.qty;
            subtotal += itemTotal;
            list.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <div class="me-2 text-truncate" style="max-width: 140px;">
                        <span class="small fw-bold text-heading d-block text-truncate">${item.name}</span>
                        <small class="text-muted">$${item.price.toFixed(2)} ea</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-outline-secondary btn-xs qty-btn" onclick="updateQty(${index}, -1)">-</button>
                        <span class="px-1 fw-bold small">${item.qty}</span>
                        <button class="btn btn-outline-secondary btn-xs qty-btn" onclick="updateQty(${index}, 1)">+</button>
                        <span class="fw-bold ms-2 small text-primary" style="min-width: 45px; text-align: right;">$${itemTotal.toFixed(2)}</span>
                    </div>
                </li>
            `;
        });
    }

    const discountVal = parseFloat(document.getElementById('cart-discount').value) || 0;
    const taxRate = parseFloat(document.getElementById('cart-tax-rate').value) || 0;

    const afterDiscount = Math.max(0, subtotal - discountVal);
    const taxAmount = (afterDiscount * (taxRate / 100));
    const total = afterDiscount + taxAmount;

    document.getElementById('subtotal').innerText = '$' + subtotal.toFixed(2);
    document.getElementById('discount-amount').innerText = '-$' + discountVal.toFixed(2);
    document.getElementById('tax-amount').innerText = '$' + taxAmount.toFixed(2);
    document.getElementById('total').innerText = '$' + total.toFixed(2);
}

function checkout(method) {
    if (cart.length === 0) {
        return Swal.fire({ icon: 'warning', title: 'Cart is empty', text: 'Please add items before checking out.' });
    }

    const subtotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    const discountVal = parseFloat(document.getElementById('cart-discount').value) || 0;
    const taxRate = parseFloat(document.getElementById('cart-tax-rate').value) || 0;
    const afterDiscount = Math.max(0, subtotal - discountVal);
    const taxAmount = (afterDiscount * (taxRate / 100));
    const total = afterDiscount + taxAmount;
    const customerId = document.getElementById('pos-customer-select').value || null;

    Swal.fire({
        title: 'Complete POS Sale?',
        text: `Total: $${total.toFixed(2)} via ${method.toUpperCase()}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Complete Sale'
    }).then((res) => {
        if (res.isConfirmed) {
            fetch('{{ route("app-vendor-pos-checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    items: cart,
                    subtotal: subtotal,
                    discount_amount: discountVal,
                    tax_amount: taxAmount,
                    total: total,
                    payment_method: method,
                    customer_id: customerId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showReceipt(data.receipt);
                    clearCart();
                } else {
                    Swal.fire('Error', data.message || 'Checkout failed', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Server connection error', 'error');
            });
        }
    });
}

function showReceipt(rc) {
    let itemsHtml = '';
    rc.items.forEach(i => {
        itemsHtml += `<div class="d-flex justify-content-between small py-1"><span>${i.qty}x ${i.name}</span><span class="fw-bold">$${i.subtotal}</span></div>`;
    });

    const html = `
        <div class="text-center mb-3">
            <h5 class="fw-bold mb-0">AK-Mart Mini-Mart</h5>
            <small class="text-muted">Smart Retail In-Store Receipt</small>
            <div class="small fw-semibold text-primary mt-1">#${rc.order_number}</div>
            <div class="small text-muted">${rc.date}</div>
            <div class="small text-muted">Cashier: ${rc.cashier} • Customer: ${rc.customer}</div>
        </div>
        <hr class="my-2">
        ${itemsHtml}
        <hr class="my-2">
        <div class="d-flex justify-content-between small"><span>Subtotal:</span><span>$${rc.subtotal}</span></div>
        <div class="d-flex justify-content-between small text-danger"><span>Discount:</span><span>-$${rc.discount}</span></div>
        <div class="d-flex justify-content-between small"><span>Tax:</span><span>$${rc.tax}</span></div>
        <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-2 mt-1">
            <span>TOTAL:</span>
            <span class="text-primary">$${rc.total}</span>
        </div>
        <div class="d-flex justify-content-between small text-muted mt-1">
            <span>Payment Method:</span>
            <span>${rc.payment_method}</span>
        </div>
        <div class="text-center mt-3 pt-2 border-top small text-muted">
            Thank you for shopping at AK-Mart!<br>Visit us again.
        </div>
    `;

    document.getElementById('receiptContent').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
    modal.show();
}

function holdCurrentSale() {
    if (cart.length === 0) return Swal.fire('Notice', 'Cart is empty, nothing to hold.', 'info');
    heldSales.push([...cart]);
    clearCart();
    document.getElementById('held-count').innerText = heldSales.length;
    Swal.fire({ icon: 'success', title: 'Sale Held', text: 'Current sale saved to held orders.', timer: 1200, showConfirmButton: false });
}

function resumeHeldSale() {
    if (heldSales.length === 0) return Swal.fire('Notice', 'No held sales in queue.', 'info');
    cart = heldSales.pop();
    document.getElementById('held-count').innerText = heldSales.length;
    renderCart();
}

function filterCategory(catId) {
    document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
    event.target.classList.add('active');

    document.querySelectorAll('.product-item').forEach(item => {
        if (!catId || item.dataset.category === catId) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Live Barcode Input Listener
document.getElementById('barcode-search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const val = this.value.trim();
        if (!val) return;
        this.value = '';

        fetch(`{{ url('vendor/pos/search') }}?q=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                addToCart(data.product.id, data.product.name, data.product.price, data.product.qty);
            } else {
                Swal.fire({ icon: 'warning', title: 'Not Found', text: `Product "${val}" not recognized.`, timer: 1200, showConfirmButton: false });
            }
        });
    }
});

renderCart();
</script>
@endsection
