@extends('layouts/layoutMaster')

@section('title', 'Customer Account Portal — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-user-pin text-primary me-2"></i> Customer Account Portal</h4>
        <p class="text-muted small mb-0">Manage orders, wishlist items, saved carts, store credit balances, and return requests</p>
    </div>
</div>

<!-- Account Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Store Credit Balance</span>
            <h3 class="fw-bold text-success my-1">${{ number_format($storeCredit->balance, 2) }}</h3>
            <small class="text-muted">Available at checkout</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Total Orders Placed</span>
            <h3 class="fw-bold text-primary my-1">{{ $orders->count() }}</h3>
            <small class="text-muted">Lifetime transactions</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Wishlist Items</span>
            <h3 class="fw-bold text-info my-1">{{ $wishlist->count() }}</h3>
            <small class="text-muted">Saved for later</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Saved Cart Sessions</span>
            <h3 class="fw-bold text-warning my-1">{{ $savedCarts->count() }}</h3>
            <small class="text-muted">1-Click re-order ready</small>
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3 gap-2" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-orders" role="tab">
                <i class="bx bx-package me-1"></i> Order History & Tracking
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-wishlist" role="tab">
                <i class="bx bx-heart me-1"></i> My Wishlist ({{ $wishlist->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-credit" role="tab">
                <i class="bx bx-wallet me-1"></i> Store Credit History
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-returns" role="tab">
                <i class="bx bx-undo me-1"></i> Return Requests ({{ $returnRequests->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        {{-- Tab 1: Orders --}}
        <div class="tab-pane fade show active" id="tab-orders" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Recent Order History</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr>
                                    <td><strong>#{{ $o->order_number }}</strong></td>
                                    <td>{{ $o->items->count() }} Products</td>
                                    <td class="fw-bold">${{ number_format($o->total_amount, 2) }}</td>
                                    <td><span class="badge bg-label-success">{{ ucfirst($o->payment_status) }}</span></td>
                                    <td><span class="badge bg-label-primary">{{ ucfirst($o->order_status) }}</span></td>
                                    <td><small>{{ $o->created_at->format('d M Y') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 2: Wishlist --}}
        <div class="tab-pane fade" id="tab-wishlist" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Saved Wishlist Items</h5>
                </div>
                <div class="row g-3 p-3">
                    @forelse($wishlist as $w)
                        <div class="col-md-4">
                            <div class="card border p-3 h-100">
                                <h6 class="fw-bold mb-1">{{ $w->product?->name }}</h6>
                                <p class="text-success fw-bold fs-6 mb-2">${{ number_format($w->product?->price, 2) }}</p>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary w-100"><i class="bx bx-cart-add me-1"></i> Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">Your wishlist is empty.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tab 3: Store Credit --}}
        <div class="tab-pane fade" id="tab-credit" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Store Credit Transactions</h5>
                    <span class="badge bg-success fs-6">Balance: ${{ number_format($storeCredit->balance, 2) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($storeCredit->transactions as $txn)
                                <tr>
                                    <td>
                                        <span class="badge {{ $txn->type === 'credit' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($txn->type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $txn->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $txn->type === 'credit' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td>{{ $txn->reference_type ?: 'Manual adjustment' }}</td>
                                    <td>{{ $txn->notes ?: 'N/A' }}</td>
                                    <td><small>{{ $txn->created_at->format('d M Y, H:i') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No store credit transactions on record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 4: Return Requests --}}
        <div class="tab-pane fade" id="tab-returns" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Self-Service Return Requests</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Reason</th>
                                <th>Refund Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returnRequests as $rr)
                                <tr>
                                    <td>#{{ $rr->order?->order_number }}</td>
                                    <td>{{ $rr->reason }}</td>
                                    <td class="fw-bold text-success">${{ number_format($rr->refund_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $rr->status === 'refunded' ? 'bg-success' : ($rr->status === 'approved' ? 'bg-primary' : 'bg-warning') }}">
                                            {{ ucfirst($rr->status) }}
                                        </span>
                                    </td>
                                    <td><small>{{ $rr->created_at->format('d M Y') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No return requests recorded.</td>
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
