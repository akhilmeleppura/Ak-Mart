@extends('layouts/layoutMaster')

@section('title', $company->name . ' — ' . __('B2B Account Overview'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('app-b2b-companies') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to B2B Directory') }}
        </a>
        <h4 class="fw-bold mb-0">{{ $company->name }} <span class="badge bg-label-primary fs-6">{{ $company->company_code }}</span></h4>
        <small class="text-muted"><i class="bx bx-envelope me-1"></i> {{ $company->contact_email }} | {{ __('Tax ID:') }} {{ $company->tax_id ?: __('Not Set') }}</small>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddTierPrice">
            <i class="bx bx-tag me-1"></i> {{ __('Add Wholesale Tier Price') }}
        </button>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddBuyer">
            <i class="bx bx-user-plus me-1"></i> {{ __('Add Buyer') }}
        </button>
    </div>
</div>

<!-- Balance Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Approved Credit Limit') }}</span>
            <h3 class="fw-bold text-primary my-1">${{ number_format($company->credit_limit, 2) }}</h3>
            <small class="text-muted">{{ __('Payment Terms:') }} {{ strtoupper(str_replace('_', ' ', $company->payment_terms)) }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Current Balance Due') }}</span>
            <h3 class="fw-bold text-danger my-1">${{ number_format($company->current_balance, 2) }}</h3>
            <small class="text-muted">{{ __('Invoiced outstanding') }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Available Credit') }}</span>
            <h3 class="fw-bold text-success my-1">${{ number_format($company->available_credit, 2) }}</h3>
            <small class="text-muted">{{ __('Remaining purchasing capacity') }}</small>
        </div>
    </div>
</div>

<!-- Tabs for Tier Prices & Buyers -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3 gap-2" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tiers" role="tab">
                <i class="bx bx-tag me-1"></i> {{ __('Negotiated Price Tiers') }} ({{ $company->tierPrices->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-buyers" role="tab">
                <i class="bx bx-user me-1"></i> {{ __('Authorized Corporate Buyers') }} ({{ $company->buyers->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        {{-- Tab 1: Tier Prices --}}
        <div class="tab-pane fade show active" id="tab-tiers" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">{{ __('Contracted Wholesale Pricing & Quantity Breaks') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Catalog Price') }}</th>
                                <th>{{ __('Minimum Order Qty (MOQ)') }}</th>
                                <th>{{ __('Negotiated B2B Unit Price') }}</th>
                                <th>{{ __('Discount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($company->tierPrices as $tp)
                                <tr>
                                    <td><strong>{{ $tp->product?->name }}</strong></td>
                                    <td>${{ number_format($tp->product?->price, 2) }}</td>
                                    <td><span class="badge bg-label-info">{{ $tp->min_qty }}+ {{ __('Units') }}</span></td>
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
                                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No custom price tiers negotiated. Click \'Add Wholesale Tier Price\' above.') }}</td>
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
                    <h5 class="card-title mb-0">{{ __('Authorized Purchasing Agents') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Buyer Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Corporate Role') }}</th>
                                <th>{{ __('Spending Limit') }}</th>
                                <th>{{ __('Order Approval Rights') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($company->buyers as $buyer)
                                <tr>
                                    <td><strong>{{ $buyer->user?->name }}</strong></td>
                                    <td>{{ $buyer->user?->email }}</td>
                                    <td><span class="badge bg-label-primary">{{ ucfirst($buyer->role) }}</span></td>
                                    <td>{{ $buyer->spending_limit ? '$' . number_format($buyer->spending_limit, 2) : __('Unlimited') }}</td>
                                    <td>
                                        <span class="badge {{ $buyer->can_approve_orders ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $buyer->can_approve_orders ? __('Yes') : __('No') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No corporate buyers assigned yet.') }}</td>
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
                    <h5 class="modal-title fw-bold">{{ __('Add Wholesale Tier Price') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Select Product') }}</label>
                        <select name="product_id" class="form-select" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ __('Retail:') }} ${{ number_format($p->price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Min Quantity (MOQ)') }}</label>
                            <input type="number" name="min_qty" class="form-control" value="10" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('B2B Unit Price ($)') }}</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Tier Price') }}</button>
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
                    <h5 class="modal-title fw-bold">{{ __('Add Corporate Buyer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Select User Account') }}</label>
                        <select name="user_id" class="form-select" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Corporate Role') }}</label>
                        <select name="role" class="form-select" required>
                            <option value="buyer">{{ __('Purchasing Buyer') }}</option>
                            <option value="approver">{{ __('Order Approver / Manager') }}</option>
                            <option value="admin">{{ __('Company Administrator') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Spending Limit ($)') }}</label>
                        <input type="number" name="spending_limit" class="form-control" placeholder="{{ __('Leave empty for unlimited') }}">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_approve_orders" value="1" id="canApprove">
                        <label class="form-check-label" for="canApprove">
                            {{ __('Allow this buyer to approve company purchase orders') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Assign Buyer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
