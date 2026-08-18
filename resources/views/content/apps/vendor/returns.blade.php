@extends('layouts/layoutMaster')

@section('title', __('Returns & Refunds') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-revision text-primary me-2"></i> {{ __('Returns & Refund Resolution') }}</h4>
        <p class="text-muted small mb-0">{{ __('Review customer return requests, process refunds, and manage restocking movements') }}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Return Requests Ledger') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Reason & Details') }}</th>
                    <th>{{ __('Order Total') }}</th>
                    <th>{{ __('Refund Status') }}</th>
                    <th>{{ __('Refund Amount') }}</th>
                    <th>{{ __('Date Requested') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>
                            <strong class="text-primary">{{ $req->order?->order_number ?? ('ORD-' . $req->order_id) }}</strong>
                            <div class="small text-muted">{{ $req->order?->items?->count() ?? 0 }} {{ __('Items') }}</div>
                        </td>
                        <td>{{ $req->order?->customer?->name ?? __('Customer') }}</td>
                        <td>
                            <div class="fw-semibold text-heading">{{ $req->reason }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($req->details, 40) }}</small>
                        </td>
                        <td><strong>${{ number_format($req->order?->total_amount ?? 0, 2) }}</strong></td>
                        <td>
                            @php $colors = ['pending'=>'warning', 'approved'=>'info', 'rejected'=>'danger', 'refunded'=>'success']; @endphp
                            <span class="badge bg-label-{{ $colors[$req->status] ?? 'secondary' }}">{{ ucfirst($req->status) }}</span>
                        </td>
                        <td><strong class="text-success">{{ $req->refund_amount ? '$' . number_format($req->refund_amount, 2) : '—' }}</strong></td>
                        <td><small>{{ $req->created_at->format('d M Y, h:i A') }}</small></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editReturnModal{{ $req->id }}">
                                <i class="bx bx-slider me-1"></i> {{ __('Resolve') }}
                            </button>
                        </td>
                    </tr>

                    {{-- Edit Return Modal --}}
                    <div class="modal fade" id="editReturnModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-bottom">
                                    <h5 class="modal-title fw-bold">{{ __('Resolve Return Request') }} #{{ $req->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('app-vendor-returns-update', $req->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">{{ __('Resolution Status') }}</label>
                                            <select name="status" class="form-select" required>
                                                <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>{{ __('Pending Review') }}</option>
                                                <option value="approved" {{ $req->status == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                                <option value="rejected" {{ $req->status == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                                <option value="refunded" {{ $req->status == 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">{{ __('Refund Amount ($)') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="refund_amount" class="form-control" value="{{ $req->refund_amount ?? $req->order?->total_amount ?? 0 }}" min="0">
                                            </div>
                                            <small class="text-muted">{{ __('Original Order Total:') }} ${{ number_format($req->order?->total_amount ?? 0, 2) }}</small>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="restock_items" id="restock{{ $req->id }}" value="1" checked>
                                                <label class="form-check-label fw-semibold" for="restock{{ $req->id }}">
                                                    {{ __('Restock returned items into inventory (log StockMovement)') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">{{ __('Action Notes / Reason') }}</label>
                                            <textarea name="action_notes" class="form-control" rows="2" placeholder="{{ __('e.g., Refund issued via original payment gateway...') }}"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Apply Resolution') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bx bx-check-circle fs-1 d-block mb-2 opacity-50"></i>
                            {{ __('No return requests on file.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer border-top py-3">
        {{ $requests->links() }}
    </div>
</div>
@endsection
