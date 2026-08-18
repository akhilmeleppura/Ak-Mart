@extends('layouts/layoutMaster')

@section('title', __('POS Shift Register') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-calculator text-primary me-2"></i> {{ __('POS Cash Drawer & Shift Reconciliation') }}</h4>
        <p class="text-muted small mb-0">{{ __('Manage cashier shifts, track expected cash drawer totals, and record closing variance reconciliations') }}</p>
    </div>
    @if(!$activeSession)
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalOpenShift">
            <i class="bx bx-play-circle me-1"></i> {{ __('Open Cash Drawer Shift') }}
        </button>
    @else
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalCloseShift">
            <i class="bx bx-stop-circle me-1"></i> {{ __('Close Active Shift') }} (#{{ $activeSession->id }})
        </button>
    @endif
</div>

<!-- Active Session Alert -->
@if($activeSession)
    <div class="alert alert-primary d-flex align-items-center mb-4 border" role="alert">
        <i class="bx bx-info-circle fs-3 me-3"></i>
        <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-1">{{ __('Active Register Session') }} #{{ $activeSession->id }} {{ __('Open') }}</h6>
            <span>{{ __('Opened at') }} {{ $activeSession->opened_at->format('d M Y, H:i') }} {{ __('with') }} <strong>${{ number_format($activeSession->opening_amount, 2) }}</strong> {{ __('opening cash float. Cashier:') }} <strong>{{ $activeSession->user?->name }}</strong></span>
        </div>
        <button class="btn btn-sm btn-danger ms-3" data-bs-toggle="modal" data-bs-target="#modalCloseShift">
            {{ __('Close Shift & Reconcile') }}
        </button>
    </div>
@endif

<!-- Sessions Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('POS Register Shifts Ledger') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Shift #') }}</th>
                    <th>{{ __('Cashier') }}</th>
                    <th>{{ __('Opening Float') }}</th>
                    <th>{{ __('Cash Sales') }}</th>
                    <th>{{ __('Card / Digital') }}</th>
                    <th>{{ __('Closing Cash Counted') }}</th>
                    <th>{{ __('Difference') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Shift Time') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $s)
                    <tr>
                        <td><strong>#{{ $s->id }}</strong></td>
                        <td>{{ $s->user?->name }}</td>
                        <td>${{ number_format($s->opening_amount, 2) }}</td>
                        <td class="text-primary fw-bold">${{ number_format($s->cash_sales, 2) }}</td>
                        <td>${{ number_format($s->card_sales + $s->upi_sales, 2) }}</td>
                        <td class="fw-bold fs-6">${{ number_format($s->closing_amount ?? 0, 2) }}</td>
                        <td>
                            @if($s->status === 'closed')
                                <span class="badge {{ $s->difference == 0 ? 'bg-label-success' : ($s->difference > 0 ? 'bg-label-info' : 'bg-label-danger') }} fs-6">
                                    {{ $s->difference > 0 ? '+' : '' }}${{ number_format($s->difference, 2) }}
                                </span>
                            @else
                                <span class="text-muted small">{{ __('In Progress') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $s->status === 'open' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($s->status) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $s->opened_at->format('d M, H:i') }} — {{ $s->closed_at ? $s->closed_at->format('H:i') : __('Active') }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">{{ __('No POS shifts recorded. Click \'Open Cash Drawer Shift\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $sessions->links() }}
    </div>
</div>

<!-- Modal Open Shift -->
<div class="modal fade" id="modalOpenShift" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-pos-register-open') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bx bx-play-circle text-primary me-1"></i> {{ __('Open Register Shift') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Opening Cash Float ($)') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="opening_amount" class="form-control" value="100.00" min="0" required>
                        <small class="text-muted">{{ __('Initial cash balance inside physical drawer') }}</small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Start Shift') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($activeSession)
<!-- Modal Close Shift -->
<div class="modal fade" id="modalCloseShift" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-pos-register-close') }}" method="POST">
                @csrf
                <input type="hidden" name="session_id" value="{{ $activeSession->id }}">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger"><i class="bx bx-stop-circle me-1"></i> {{ __('Close POS Shift & Reconcile') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 bg-light rounded mb-3">
                        <p class="mb-1 small">{{ __('Opening Float:') }} <strong>${{ number_format($activeSession->opening_amount, 2) }}</strong></p>
                        <p class="mb-0 small">{{ __('Cash Sales:') }} <strong>${{ number_format($activeSession->cash_sales, 2) }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Actual Counted Physical Cash ($)') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="closing_amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Reconciliation Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Explain any discrepancy or cash drop') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Close & Reconcile Shift') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
