@extends('layouts/layoutMaster')

@section('title', __('Quotes & Estimates') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-file-blank text-primary me-2"></i> {{ __('B2B Quotes & Estimates') }}</h4>
        <p class="text-muted small mb-0">{{ __('Review custom RFQs, calculate bulk tiered discounts, and approve corporate quote estimates') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateQuote">
        <i class="bx bx-plus me-1"></i> {{ __('Create Quote Request') }}
    </button>
</div>

<!-- Quotes Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Quote Requests & Negotiations') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Quote #') }}</th>
                    <th>{{ __('Company') }}</th>
                    <th>{{ __('Subtotal') }}</th>
                    <th>{{ __('Discount') }}</th>
                    <th>{{ __('Total Quote Value') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Valid Until') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotes as $q)
                    <tr>
                        <td><strong>{{ $q->quote_number }}</strong></td>
                        <td>{{ $q->company?->name ?? __('Direct B2B') }}</td>
                        <td>${{ number_format($q->subtotal, 2) }}</td>
                        <td class="text-success">-${{ number_format($q->discount, 2) }}</td>
                        <td class="fw-bold fs-6 text-primary">${{ number_format($q->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $q->status === 'approved' ? 'bg-success' : ($q->status === 'submitted' ? 'bg-warning' : ($q->status === 'converted' ? 'bg-primary' : 'bg-secondary')) }}">
                                {{ ucfirst($q->status) }}
                            </span>
                        </td>
                        <td><small>{{ $q->valid_until ? $q->valid_until->format('d M Y') : 'N/A' }}</small></td>
                        <td>
                            @if($q->status === 'submitted')
                                <form action="{{ route('app-b2b-quotes-status', $q->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-check"></i> {{ __('Approve') }}</button>
                                </form>
                                <form action="{{ route('app-b2b-quotes-status', $q->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-x"></i> {{ __('Reject') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">{{ __('No quote requests recorded. Click \'Create Quote Request\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create Quote -->
<div class="modal fade" id="modalCreateQuote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-b2b-quotes-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">{{ __('Create B2B Quote Request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Select Corporate Client') }} <span class="text-danger">*</span></label>
                        <select name="b2b_company_id" class="form-select" required>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Select Product') }} <span class="text-danger">*</span></label>
                        <select name="items[0][product_id]" class="form-select" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ __('Retail:') }} ${{ number_format($p->price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Quantity (MOQ)') }}</label>
                            <input type="number" name="items[0][qty]" class="form-control" value="25" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Negotiated Discount %') }}</label>
                            <input type="number" name="discount" class="form-control" value="15" min="0" max="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Valid Until') }}</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ now()->addDays(14)->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Notes / Terms') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('e.g. Free pallet delivery included') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Generate Quote') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
