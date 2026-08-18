@extends('layouts/layoutMaster')

@section('title', __('Corporate Accounts') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-briefcase-alt text-primary me-2"></i> {{ __('B2B Wholesale Accounts') }}</h4>
        <p class="text-muted small mb-0">{{ __('Manage corporate accounts, buyer hierarchies, credit limits, net terms, and negotiated wholesale price tiers') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddB2bCompany">
        <i class="bx bx-plus me-1"></i> {{ __('Register B2B Company') }}
    </button>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Active B2B Companies') }}</span>
            <h3 class="fw-bold text-primary my-1">{{ $companies->count() }}</h3>
            <small class="text-muted">{{ __('Corporate accounts') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Total Credit Lines Extended') }}</span>
            <h3 class="fw-bold text-success my-1">${{ number_format($totalCreditExtended, 2) }}</h3>
            <small class="text-muted">{{ __('Approved company limits') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Outstanding B2B Receivables') }}</span>
            <h3 class="fw-bold text-warning my-1">${{ number_format($totalOutstandingBalance, 2) }}</h3>
            <small class="text-muted">{{ __('Invoiced balance') }}</small>
        </div>
    </div>
</div>

<!-- Companies Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Registered Corporate Accounts') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Company Name') }}</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Contact') }}</th>
                    <th>{{ __('Payment Terms') }}</th>
                    <th>{{ __('Credit Limit') }}</th>
                    <th>{{ __('Balance Due') }}</th>
                    <th>{{ __('Available Credit') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $c)
                    <tr>
                        <td><strong>{{ $c->name }}</strong></td>
                        <td><code>{{ $c->company_code }}</code></td>
                        <td>
                            <small>{{ $c->contact_email }}<br>{{ $c->contact_phone }}</small>
                        </td>
                        <td><span class="badge bg-label-info">{{ strtoupper(str_replace('_', ' ', $c->payment_terms)) }}</span></td>
                        <td class="fw-bold">${{ number_format($c->credit_limit, 2) }}</td>
                        <td class="text-danger fw-bold">${{ number_format($c->current_balance, 2) }}</td>
                        <td class="text-success fw-bold">${{ number_format($c->available_credit, 2) }}</td>
                        <td>
                            <a href="{{ route('app-b2b-companies-show', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-cog me-1"></i> {{ __('Manage') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">{{ __('No B2B accounts registered. Click \'Register B2B Company\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add B2B Company -->
<div class="modal fade" id="modalAddB2bCompany" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-b2b-companies-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bx bx-briefcase text-primary me-1"></i> {{ __('Register B2B Company') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Company Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Acme Wholesale Enterprises" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Contact Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control" placeholder="purchasing@acme.com" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Phone Number') }}</label>
                            <input type="text" name="contact_phone" class="form-control" placeholder="+1 555 0199">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Tax ID / GSTIN') }}</label>
                            <input type="text" name="tax_id" class="form-control" placeholder="e.g. 29AAAAA0000A1Z5">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Payment Terms') }}</label>
                            <select name="payment_terms" class="form-select">
                                <option value="prepaid">{{ __('Prepaid') }}</option>
                                <option value="net_15">{{ __('Net 15 Days') }}</option>
                                <option value="net_30" selected>{{ __('Net 30 Days') }}</option>
                                <option value="net_60">{{ __('Net 60 Days') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Credit Limit ($)') }}</label>
                        <input type="number" name="credit_limit" class="form-control" value="5000" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Billing Address') }}</label>
                        <textarea name="billing_address" class="form-control" rows="2" placeholder="{{ __('Official registered billing address') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save B2B Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
