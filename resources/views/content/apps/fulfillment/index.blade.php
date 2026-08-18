@extends('layouts/layoutMaster')

@section('title', __('Advanced Fulfillment') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-package text-primary me-2"></i> {{ __('Advanced Fulfillment & Split Shipments') }}</h4>
        <p class="text-muted small mb-0">{{ __('Manage multi-warehouse split fulfillment, pick lists, packing slips, carrier tracking, and store pickups') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateFulfillment">
        <i class="bx bx-plus me-1"></i> {{ __('Create Fulfillment Order') }}
    </button>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm bg-label-warning">
            <span class="text-muted small">{{ __('Pending Fulfillment') }}</span>
            <h3 class="fw-bold text-warning my-1">{{ $pendingCount }} {{ __('Orders') }}</h3>
            <small class="text-muted">{{ __('Awaiting pick & pack') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm bg-label-info">
            <span class="text-muted small">{{ __('In Transit / Shipped') }}</span>
            <h3 class="fw-bold text-info my-1">{{ $inTransitCount }} {{ __('Shipments') }}</h3>
            <small class="text-muted">{{ __('En route to customers') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm bg-label-success">
            <span class="text-muted small">{{ __('Delivered / Completed') }}</span>
            <h3 class="fw-bold text-success my-1">{{ $deliveredCount }} {{ __('Orders') }}</h3>
            <small class="text-muted">{{ __('Successfully fulfilled') }}</small>
        </div>
    </div>
</div>

<!-- Fulfillment Orders Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Fulfillment Queue') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Fulfillment #') }}</th>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Warehouse / Origin') }}</th>
                    <th>{{ __('Carrier & Tracking') }}</th>
                    <th>{{ __('Items') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fulfillments as $f)
                    <tr>
                        <td><strong>{{ $f->fulfillment_number }}</strong></td>
                        <td>
                            <a href="{{ url('/orders/' . $f->order_id) }}">#{{ $f->order?->order_number ?? $f->order_id }}</a>
                        </td>
                        <td>{{ $f->warehouse?->name ?? __('Primary Storefront') }}</td>
                        <td>
                            @if($f->tracking_number)
                                <small><strong>{{ $f->shipping_carrier }}</strong>: <code>{{ $f->tracking_number }}</code></small>
                            @else
                                <span class="text-muted small">{{ __('Unassigned') }}</span>
                            @endif
                        </td>
                        <td><span class="badge bg-label-primary">{{ $f->items->count() }} {{ __('Line Items') }}</span></td>
                        <td>
                            <span class="badge {{ $f->status === 'delivered' ? 'bg-success' : ($f->status === 'shipped' ? 'bg-info' : ($f->status === 'packed' ? 'bg-primary' : 'bg-warning')) }}">
                                {{ ucfirst($f->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('app-fulfillment-pickpack', $f->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Print Pick & Pack Slip') }}">
                                <i class="bx bx-printer"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus-{{ $f->id }}">
                                <i class="bx bx-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Update Status -->
                    <div class="modal fade" id="modalUpdateStatus-{{ $f->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('app-fulfillment-status', $f->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold">{{ __('Update Fulfillment') }} #{{ $f->fulfillment_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Fulfillment Status') }}</label>
                                            <select name="status" class="form-select" required>
                                                <option value="unfulfilled" {{ $f->status === 'unfulfilled' ? 'selected' : '' }}>{{ __('Unfulfilled / Queued') }}</option>
                                                <option value="picking" {{ $f->status === 'picking' ? 'selected' : '' }}>{{ __('Picking from Warehouse') }}</option>
                                                <option value="packed" {{ $f->status === 'packed' ? 'selected' : '' }}>{{ __('Packed & Label Printed') }}</option>
                                                <option value="shipped" {{ $f->status === 'shipped' ? 'selected' : '' }}>{{ __('Shipped / In Transit') }}</option>
                                                <option value="delivered" {{ $f->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered to Customer') }}</option>
                                                <option value="cancelled" {{ $f->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                            </select>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label fw-bold">{{ __('Carrier') }}</label>
                                                <input type="text" name="shipping_carrier" class="form-control" value="{{ $f->shipping_carrier }}" placeholder="e.g. FedEx / DHL / BlueDart">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold">{{ __('Tracking #') }}</label>
                                                <input type="text" name="tracking_number" class="form-control" value="{{ $f->tracking_number }}" placeholder="Tracking number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">{{ __('No fulfillment orders queued. Click \'Create Fulfillment Order\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $fulfillments->links() }}
    </div>
</div>

<!-- Modal Create Fulfillment -->
<div class="modal fade" id="modalCreateFulfillment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-fulfillment-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bx bx-package text-primary me-1"></i> {{ __('Create Fulfillment Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Select Order') }} <span class="text-danger">*</span></label>
                        <select name="order_id" id="selectOrderFulfill" class="form-select" required onchange="populateOrderItems(this)">
                            <option value="">{{ __('Choose Order...') }}</option>
                            @foreach($unfulfilledOrders as $uo)
                                <option value="{{ $uo->id }}" data-items='@json($uo->items)'>
                                    #{{ $uo->order_number }} — ${{ number_format($uo->total_amount, 2) }} ({{ $uo->items->count() }} {{ __('items') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Fulfillment Warehouse Origin') }}</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">{{ __('Default Storefront / Branch') }}</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="orderItemsContainer" class="mb-3">
                        <!-- Dynamic items -->
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Dispatch Fulfillment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function populateOrderItems(select) {
    const selectedOption = select.options[select.selectedIndex];
    const itemsJson = selectedOption.getAttribute('data-items');
    const container = document.getElementById('orderItemsContainer');
    container.innerHTML = '';

    if (itemsJson) {
        const items = JSON.parse(itemsJson);
        let html = '<label class="form-label fw-bold">' + @json(__('Items to Fulfill in this Package:')) + '</label>';
        items.forEach((item, index) => {
            html += `
                <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                    <span class="small fw-bold">${item.product_name}</span>
                    <div class="d-flex align-items-center gap-2">
                        <input type="hidden" name="items[${index}][order_item_id]" value="${item.id}">
                        <input type="number" name="items[${index}][qty]" class="form-control form-control-sm" style="width: 70px;" value="${item.qty}" min="1" max="${item.qty}">
                        <small class="text-muted">qty</small>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }
}
</script>
@endsection
