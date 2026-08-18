@extends('layouts/layoutMaster')

@section('title', $company->name . ' — B2B Account Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('app-b2b-companies') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bx bx-arrow-back me-1"></i> Back to B2B Directory
        </a>
        <h4 class="fw-bold mb-0">{{ $company->name }} <span class="badge bg-label-primary fs-6">{{ $company->company_code }}</span></h4>
        <small class="text-muted"><i class="bx bx-envelope me-1"></i> {{ $company->contact_email }} | Tax ID: {{ $company->tax_id ?: 'Not Set' }}</small>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddTierPrice">
            <i class="bx bx-tag me-1"></i> Add Wholesale Tier Price
        </button>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddBuyer">
            <i class="bx bx-user-plus me-1"></i> Add Buyer
        </button>
    </div>
</div>

<!-- Balance Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Approved Credit Limit</span>
            <h3 class="fw-bold text-primary my-1">${{ number_format($company->credit_limit, 2) }}</h3>
            <small class="text-muted">Payment Terms: {{ strtoupper(str_replace('_', ' ', $company->payment_terms)) }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Current Balance Due</span>
            <h3 class="fw-bold text-danger my-1">${{ number_format($company->current_balance, 2) }}</h3>
            <small class="text-muted">Invoiced outstanding</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Available Credit</span>
            <h3 class="fw-bold text-success my-1">${{ number_format($company->available_credit, 2) }}</h3>
            <small class="text-muted">Remaining purchasing capacity</small>
        </div>
    </div>
</div>

<!-- Tabs for Tier Prices & Buyers -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3 gap-2" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tiers" role="tab">
                <i class="bx bx-tag me-1"></i> Negotiated Price Tiers ({{ $company->tierPrices->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-buyers" role="tab">
                <i class="bx bx-user me-1"></i> Authorized Corporate Buyers ({{ $company->buyers->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        {{-- Tab 1: Tier Prices --}}
        <div class="tab-pane fade show active" id="tab-tiers" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Contracted Wholesale Pricing & Quantity Breaks</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Catalog Price</th>
                                <th>Minimum Order Qty (MOQ)</th>
                                <th>Negotiated B2B Unit Price</th>
                                <th>Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($company->tierPrices as $tp)
                                <tr>
                                    <td><strong>{{ $tp->product?->name }}</strong></td>
                                    <td>${{ number_format($tp->product?->price, 2) }}</td>
                                    <td><span class="badge bg-label-info">{{ $tp->min_qty }}+ Units</span></td>
                                    <td class="fw-bold text-success fs-6">${{ number_format($tp->unit_price, 2) }}</td>
                                    <td>
                                        @if($tp->product && $tp->product->price > 0)
                                            <span class="badge bg-label-success">
                                                -{{ round((($tp->product->price - $tp->unit_price) / $tp->product->price) * 100) }}% Off
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No custom price tiers negotiated. Click 'Add Wholesale Tier Price' above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 2: Buyers --}}
        <div class="tab-pane fade" id="tab-buyers" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Authorized Purchasing Agents</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Buyer Name</th>
                                <th>Email</th>
                                <th>Corporate Role</th>
                                <th>Spending Limit</th>
                                <th>Order Approval Rights</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($company->buyers as $buyer)
                                <tr>
                                    <td><strong>{{ $buyer->user?->name }}</strong></td>
                                    <td>{{ $buyer->user?->email }}</td>
                                    <td><span class="badge bg-label-primary">{{ ucfirst($buyer->role) }}</span></td>
                                    <td>{{ $buyer->spending_limit ? '$' . number_format($buyer->spending_limit, 2) : 'Unlimited' }}</td>
                                    <td>
                                        <span class="badge {{ $buyer->can_approve_orders ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $buyer->can_approve_orders ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No corporate buyers assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Tier Price -->
<div class="modal fade" id="modalAddTierPrice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-b2b-companies-tier-price', $company->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Add Wholesale Tier Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Product</label>
                        <select name="product_id" class="form-select" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Retail: ${{ number_format($p->price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold">Min Quantity (MOQ)</label>
                            <input type="number" name="min_qty" class="form-control" value="10" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">B2B Unit Price ($)</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Tier Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Buyer -->
<div class="modal fade" id="modalAddBuyer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-b2b-companies-buyer', $company->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Add Corporate Buyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select User Account</label>
                        <select name="user_id" class="form-select" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Corporate Role</label>
                        <select name="role" class="form-select" required>
                            <option value="buyer">Purchasing Buyer</option>
                            <option value="approver">Order Approver / Manager</option>
                            <option value="admin">Company Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Spending Limit ($)</label>
                        <input type="number" name="spending_limit" class="form-control" placeholder="Leave empty for unlimited">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_approve_orders" value="1" id="canApprove">
                        <label class="form-check-label" for="canApprove">
                            Allow this buyer to approve company purchase orders
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Buyer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
