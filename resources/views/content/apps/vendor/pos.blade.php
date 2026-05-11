@extends('layouts/layoutMaster')

@section('title', 'POS Terminal')

@section('vendor-style')
<style>
    .pos-product-card { cursor: pointer; transition: transform 0.2s; }
    .pos-product-card:hover { transform: scale(1.02); }
    .pos-cart { height: calc(100vh - 250px); overflow-y: auto; }
</style>
@endsection

@section('content')
<div class="row">
    {{-- Product Selection --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header border-bottom">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="barcode-search" class="form-control" placeholder="Scan Barcode or Search Product..." autofocus>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row g-4" id="product-grid">
                    @foreach($products as $p)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card border pos-product-card h-100" onclick="addToCart({{ $p->id }}, '{{ $p->name }}', {{ $p->price }})">
                            <img src="{{ $p->image ?? 'https://via.placeholder.com/150' }}" class="card-img-top" height="120" style="object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="mb-1 small fw-bold">{{ $p->name }}</h6>
                                <p class="mb-0 text-primary fw-bold">${{ number_format($p->price, 2) }}</p>
                                <small class="text-muted">Stock: {{ $p->qty }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Cart & Checkout --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header border-bottom bg-light">
                <h5 class="mb-0 fw-bold">Current Order</h5>
            </div>
            <div class="card-body p-0 pos-cart">
                <ul class="list-group list-group-flush" id="cart-list">
                    {{-- Cart items dynamic --}}
                </ul>
            </div>
            <div class="card-footer border-top p-4 bg-light">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span id="subtotal" class="fw-bold">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <h4 class="fw-bold">Total</h4>
                    <h4 id="total" class="fw-bold text-primary">$0.00</h4>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-success btn-lg" onclick="checkout('cash')">
                        <i class="bx bx-money me-2"></i> Cash Payment
                    </button>
                    <button class="btn btn-primary btn-lg" onclick="checkout('card')">
                        <i class="bx bx-credit-card me-2"></i> Card / Digital
                    </button>
                    <button class="btn btn-outline-danger" onclick="clearCart()">Clear Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

function addToCart(id, name, price) {
    const existing = cart.find(i => i.id === id);
    if(existing) {
        existing.qty++;
    } else {
        cart.push({ id, name, price, qty: 1 });
    }
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cart-list');
    list.innerHTML = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        total += item.price * item.qty;
        list.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 small">${item.name}</h6>
                    <small class="text-muted">$${item.price} x ${item.qty}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-xs btn-icon btn-label-danger" onclick="removeFromCart(${index})"><i class="bx bx-trash"></i></button>
                </div>
            </li>
        `;
    });

    document.getElementById('subtotal').innerText = '$' + total.toFixed(2);
    document.getElementById('total').innerText = '$' + total.toFixed(2);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

function checkout(method) {
    if(cart.length === 0) return Swal.fire('Error', 'Cart is empty', 'error');
    
    Swal.fire({
        title: 'Complete Sale?',
        text: `Total Amount: ${document.getElementById('total').innerText}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Complete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("app-vendor-pos-checkout") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ items: cart, total: 0, payment_method: method })
            }).then(res => res.json()).then(data => {
                Swal.fire('Success', 'Sale completed successfully', 'success');
                clearCart();
            });
        }
    });
}

// Barcode Search
document.getElementById('barcode-search').addEventListener('keypress', function(e) {
    if(e.key === 'Enter') {
        const val = this.value;
        this.value = '';
        fetch(`{{ url('app/vendor/pos/search') }}?q=${val}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                addToCart(data.product.id, data.product.name, data.product.price);
            } else {
                Toast.fire({ icon: 'error', title: 'Product not found' });
            }
        });
    }
});
</script>
@endsection
