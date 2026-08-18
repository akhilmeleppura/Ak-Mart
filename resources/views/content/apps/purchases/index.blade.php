@extends('layouts/layoutMaster')

@section('title', __('Purchase Management') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-package text-primary me-2"></i> {{ __('Purchase Order Management') }}</h4>
        <p class="text-muted small mb-0">{{ __('Manage vendor purchase orders, receive supplier shipments, and auto-sync inventory') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPOModal">
        <i class="bx bx-plus me-1"></i> {{ __('New Purchase Order') }}
    </button>
</div>

{{-- Summary cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-primary">
            <div class="card-body">
                <span class="text-heading">{{ __('Total POs') }}</span>
                <h4 class="my-1">{{ $orders->total() }}</h4>
                <small>{{ __('All purchase orders') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-success">
            <div class="card-body">
                <span class="text-heading">{{ __('Active Suppliers') }}</span>
                <h4 class="my-1">{{ $suppliers->count() }}</h4>
                <small>{{ __('Registered vendors') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-warning">
            <div class="card-body">
                <span class="text-heading">{{ __('Pending Deliveries') }}</span>
                <h4 class="my-1">{{ $orders->where('status', '!=', 'received')->count() }}</h4>
                <small>{{ __('Awaiting receipt') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-info">
            <div class="card-body">
                <span class="text-heading">{{ __('Total Purchase Volume') }}</span>
                <h4 class="my-1">${{ number_format($orders->sum('total_amount'), 2) }}</h4>
                <small>{{ __('Gross procurement') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Purchase Orders Ledger') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('PO Number') }}</th>
                    <th>{{ __('Supplier Details') }}</th>
                    <th>{{ __('Line Items') }}</th>
                    <th>{{ __('Total Cost') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Order Date') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $po)
                <tr>
                    <td class="fw-bold text-primary">{{ $po->po_number }}</td>
                    <td>
                        <strong>{{ $po->supplier?->name ?? __('Unknown Supplier') }}</strong>
                        <div class="text-muted small">{{ $po->supplier?->company_name ?? '' }}</div>
                    </td>
                    <td>
                        @if($po->items && $po->items->count() > 0)
                            <span class="badge bg-label-primary">{{ $po->items->sum('quantity') }} {{ __('Units') }} ({{ $po->items->count() }} {{ __('SKUs') }})</span>
                        @else
                            <span class="text-muted small">{{ __('Standard PO') }}</span>
                        @endif
                    </td>
                    <td class="fw-bold text-heading">${{ number_format($po->total_amount, 2) }}</td>
                    <td>
                        @if($po->status === 'received')
                            <span class="badge bg-label-success"><i class="bx bx-check-double me-1"></i> {{ __('Received & Stocked') }}</span>
                        @elseif($po->status === 'cancelled')
                            <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                        @else
                            <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> {{ __('Ordered / In Transit') }}</span>
                        @endif
                    </td>
                    <td><small>{{ $po->created_at->format('d M Y, h:i A') }}</small></td>
                    <td class="text-end">
                        @if($po->status !== 'received')
                        <button class="btn btn-sm btn-success" onclick="markReceived({{ $po->id }}, '{{ $po->po_number }}')">
                            <i class="bx bx-check me-1"></i> {{ __('Receive & Stock') }}
                        </button>
                        @else
                        <span class="badge bg-label-secondary"><i class="bx bx-check me-1"></i> {{ __('Completed') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bx bx-package fs-1 d-block mb-2 opacity-50"></i>
                        {{ __('No purchase orders created yet.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer border-top py-3">
        {{ $orders->links() }}
    </div>
</div>

<!-- Modal: New PO with Line Items -->
<div class="modal fade" id="createPOModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-purchases-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">{{ __('Create New Purchase Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Select Supplier') }} *</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- {{ __('Choose Supplier') }} --</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->company_name }}) — {{ __('Balance:') }} ${{ number_format($sup->balance, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Order Notes') }}</label>
                            <input type="text" name="notes" class="form-control" placeholder="{{ __('PO Notes or reference number...') }}" />
                        </div>
                    </div>

                    <h6 class="border-top pt-3 mb-2">{{ __('Order Line Items') }}</h6>
                    <div id="po-items-container">
                        <div class="row g-2 mb-2 po-item-row">
                            <div class="col-6">
                                <label class="form-label small">{{ __('Product') }}</label>
                                <select name="items[0][product_id]" class="form-select po-product-select" required>
                                    <option value="">-- {{ __('Select Product') }} --</option>
                                    @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" data-cost="{{ $prod->price * 0.7 }}">{{ $prod->name }} ({{ __('Current:') }} {{ $prod->qty }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label small">{{ __('Quantity') }}</label>
                                <input type="number" name="items[0][quantity]" class="form-control po-qty-input" placeholder="{{ __('Qty') }}" min="1" value="10" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label small">{{ __('Unit Cost ($)') }}</label>
                                <input type="number" step="0.01" name="items[0][unit_cost]" class="form-control po-cost-input" placeholder="{{ __('Cost') }}" min="0" value="10.00" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Purchase Order') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function markReceived(id, poNumber) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: @json(__('Receive Purchase Order?')),
            text: @json(__('This will mark order as received, update supplier records, and increment stock.')),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: @json(__('Yes, Receive & Stock')),
            cancelButtonText: @json(__('Cancel'))
        }).then((res) => {
            if (res.isConfirmed) {
                fetch(`{{ url('purchases') }}/${id}/received`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: @json(__('Stock Updated!')),
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire(@json(__('Notice')), data.message || @json(__('Error receiving PO')), 'error');
                    }
                }).catch(e => {
                    location.reload();
                });
            }
        });
    }
}
</script>
@endsection
