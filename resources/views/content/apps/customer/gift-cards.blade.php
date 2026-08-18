@extends('layouts/layoutMaster')

@section('title', __('Gift Cards') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-gift text-primary me-2"></i> {{ __('Digital Gift Cards & Vouchers') }}</h4>
        <p class="text-muted small mb-0">{{ __('Generate digital gift cards, track active voucher balances, and manage recipient distributions') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGenerateGiftCard">
        <i class="bx bx-plus me-1"></i> {{ __('Issue Gift Card') }}
    </button>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Total Lifetime Value Issued') }}</span>
            <h3 class="fw-bold text-primary my-1">${{ number_format($totalIssuedValue, 2) }}</h3>
            <small class="text-muted">{{ $giftCards->total() }} {{ __('Gift Cards') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Active Outstanding Balance') }}</span>
            <h3 class="fw-bold text-success my-1">${{ number_format($activeBalance, 2) }}</h3>
            <small class="text-muted">{{ __('Redeemable at checkout') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Redemption Rate') }}</span>
            <h3 class="fw-bold text-info my-1">
                {{ $totalIssuedValue > 0 ? round((($totalIssuedValue - $activeBalance) / $totalIssuedValue) * 100) : 0 }}%
            </h3>
            <small class="text-muted">{{ __('Utilized in store') }}</small>
        </div>
    </div>
</div>

<!-- Gift Cards Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Issued Gift Cards') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Card Code') }}</th>
                    <th>{{ __('Recipient') }}</th>
                    <th>{{ __('Initial Value') }}</th>
                    <th>{{ __('Remaining Balance') }}</th>
                    <th>{{ __('Expiry Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($giftCards as $gc)
                    <tr>
                        <td><code>{{ $gc->code }}</code></td>
                        <td>{{ $gc->recipient_email ?: __('Direct in-store buyer') }}</td>
                        <td>${{ number_format($gc->initial_balance, 2) }}</td>
                        <td class="fw-bold text-success fs-6">${{ number_format($gc->current_balance, 2) }}</td>
                        <td><small>{{ $gc->expiry_date ? $gc->expiry_date->format('d M Y') : __('Never') }}</small></td>
                        <td>
                            <span class="badge {{ $gc->isValid() ? 'bg-success' : 'bg-secondary' }}">
                                {{ $gc->isValid() ? __('Active') : __('Expired / Exhausted') }}
                            </span>
                        </td>
                        <td><small>{{ $gc->created_at->format('d M Y') }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">{{ __('No gift cards issued. Click \'Issue Gift Card\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $giftCards->links() }}
    </div>
</div>

<!-- Modal Issue Gift Card -->
<div class="modal fade" id="modalGenerateGiftCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-gift-cards-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bx bx-gift text-primary me-1"></i> {{ __('Issue New Gift Card') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Card Balance Amount ($)') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="50.00" value="50" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Recipient Email') }}</label>
                        <input type="email" name="recipient_email" class="form-control" placeholder="customer@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Validity Period (Days)') }}</label>
                        <input type="number" name="expiry_days" class="form-control" value="365" min="1">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Generate Gift Card') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
