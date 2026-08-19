@extends('layouts/layoutMaster')

@section('title', __('Inventory & Stock') . ' - AK-Mart')

@section('content')
{{-- Summary Widgets --}}
<div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-primary">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-heading">{{ __('Total Products') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $totalProducts }}</h4>
                        </div>
                        <small class="mb-0">{{ number_format($totalStockQty) }} {{ __('Total Units') }}</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-package bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-success">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-heading">{{ __('Total Valuation') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">${{ number_format($totalValuation, 2) }}</h4>
                        </div>
                        <small class="mb-0">{{ __('Estimated Retail Value') }}</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-dollar bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-warning">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-heading">{{ __('Low Stock Alerts') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $lowStockProducts->count() }}</h4>
                        </div>
                        <small class="mb-0">{{ __('Action Recommended') }}</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-error bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-label-danger">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-heading">{{ __('Out of Stock') }}</span>
                        <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $outOfStockProducts->count() }}</h4>
                        </div>
                        <small class="mb-0">{{ __('Requires Restocking') }}</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-block bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Card with Tabs --}}
<div class="nav-align-top mb-6">
    <ul class="nav nav-pills mb-4 gap-2" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-inventory" aria-controls="tab-inventory" aria-selected="true">
                <i class="bx bx-cube me-1"></i> {{ __('Stock Ledger') }}
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-movements" aria-controls="tab-movements" aria-selected="false">
                <i class="bx bx-history me-1"></i> {{ __('Stock Movement Audit') }} ({{ $movements->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-transfers" aria-controls="tab-transfers" aria-selected="false">
                <i class="bx bx-transfer-alt me-1"></i> {{ __('Branch Transfers') }} ({{ $transfers->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        {{-- Tab 1: Inventory Table --}}
        <div class="tab-pane fade show active" id="tab-inventory" role="tabpanel">
            <div class="card border-0 shadow-none">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">{{ __('Product Inventory & Stock Controls') }}</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                            <i class="bx bx-adjust me-1"></i> {{ __('Quick Adjust Stock') }}
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                            <i class="bx bx-transfer me-1"></i> {{ __('New Branch Transfer') }}
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product Details') }}</th>
                                <th>{{ __('SKU / Barcode') }}</th>
                                <th>{{ __('Unit Price') }}</th>
                                <th>{{ __('In Stock') }}</th>
                                <th>{{ __('Thresholds (Min / Max)') }}</th>
                                <th>{{ __('Health Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($product->image ?: 'assets/img/ecommerce-images/product-1.png') }}" class="rounded me-3" width="40" height="40" alt="" onerror="this.src='https://via.placeholder.com/40'">
                                            <div>
                                                <span class="text-body fw-semibold">{{ $product->name }}</span>
                                                <div class="text-muted small">{{ $product->category->name ?? __('General') }} {{ $product->brand ? '• ' . $product->brand : '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $product->sku ?: 'N/A' }}</code><br>
                                        <small class="text-muted">{{ $product->barcode ?: '' }}</small>
                                    </td>
                                    <td><strong>${{ number_format($product->price, 2) }}</strong></td>
                                    <td>
                                        <span class="fw-bold fs-6 {{ $product->qty <= 0 ? 'text-danger' : ($product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                            {{ $product->qty }} {{ __('Units') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ __('Min:') }} {{ $product->min_stock ?? 5 }} | {{ __('Max:') }} {{ $product->max_stock ?? 100 }}</small>
                                    </td>
                                    <td>
                                        @if($product->qty <= 0)
                                            <span class="badge bg-label-danger">{{ __('Out of Stock') }}</span>
                                        @elseif($product->isLowStock())
                                            <span class="badge bg-label-warning">{{ __('Low Stock') }} ({{ __('Reorder') }} {{ $product->recommendedPurchaseQty() }})</span>
                                        @elseif($product->qty > ($product->max_stock ?? 100))
                                            <span class="badge bg-label-info">{{ __('Overstocked') }}</span>
                                        @else
                                            <span class="badge bg-label-success">{{ __('Optimal') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-icon btn-label-primary btn-quick-adjust" 
                                            data-id="{{ $product->id }}" 
                                            data-name="{{ $product->name }}" 
                                            data-qty="{{ $product->qty }}"
                                            title="{{ __('Adjust Stock') }}">
                                            <i class="bx bx-slider"></i>
                                        </button>
                                        <a href="{{ route('app-ecommerce-product-edit', $product->id) }}" class="btn btn-sm btn-icon btn-label-secondary" title="{{ __('Edit Product') }}">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">{{ __('No products found in inventory.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 2: Stock Movement Audit Log --}}
        <div class="tab-pane fade" id="tab-movements" role="tabpanel">
            <div class="card border-0 shadow-none">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">{{ __('Traceable Stock Movements & Audit Ledger') }}</h5>
                    <small class="text-muted">{{ __('Complete audit trail of every stock increase, deduction, adjustment, and transfer.') }}</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Timestamp') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Movement Type') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Stock Before & After') }}</th>
                                <th>{{ __('Reason / Reference') }}</th>
                                <th>{{ __('Operator') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $m)
                                <tr>
                                    <td><small>{{ $m->created_at->format('d M Y, h:i A') }}</small></td>
                                    <td><strong>{{ $m->product?->name ?? __('Deleted Product') }}</strong></td>
                                    <td>
                                        @if(in_array($m->type, ['stock_in', 'purchase', 'transfer_in', 'return']))
                                            <span class="badge bg-label-success"><i class="bx bx-plus me-1"></i>{{ ucfirst(str_replace('_', ' ', $m->type)) }}</span>
                                        @elseif(in_array($m->type, ['stock_out', 'sale', 'transfer_out']))
                                            <span class="badge bg-label-danger"><i class="bx bx-minus me-1"></i>{{ ucfirst(str_replace('_', ' ', $m->type)) }}</span>
                                        @else
                                            <span class="badge bg-label-warning"><i class="bx bx-adjust me-1"></i>{{ ucfirst(str_replace('_', ' ', $m->type)) }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $m->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $m->before_qty }}</span> 
                                        <i class="bx bx-right-arrow-alt text-primary mx-1"></i> 
                                        <span class="fw-bold text-heading">{{ $m->after_qty }}</span>
                                    </td>
                                    <td><small>{{ $m->reason ?: ($m->reference_type . ' #' . $m->reference_id) }}</small></td>
                                    <td><small class="badge bg-label-secondary">{{ $m->user?->name ?? __('System') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">{{ __('No stock movements recorded yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 3: Branch Transfers --}}
        <div class="tab-pane fade" id="tab-transfers" role="tabpanel">
            <div class="card border-0 shadow-none">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Inter-Branch Stock Transfers') }}</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                        <i class="bx bx-plus me-1"></i> {{ __('New Transfer') }}
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Transfer #') }}</th>
                                <th>{{ __('From Branch') }}</th>
                                <th>{{ __('To Branch') }}</th>
                                <th>{{ __('Total Items') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created Date') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $t)
                                <tr>
                                    <td><strong class="text-primary">{{ $t->transfer_number }}</strong></td>
                                    <td><span class="badge bg-label-info">{{ $t->fromBranch?->name ?? __('Main Branch') }}</span></td>
                                    <td><span class="badge bg-label-primary">{{ $t->toBranch?->name ?? __('Target Branch') }}</span></td>
                                    <td>{{ $t->items->sum('quantity') }} {{ __('Units') }} ({{ $t->items->count() }} SKUs)</td>
                                    <td>
                                        @if($t->status === 'completed')
                                            <span class="badge bg-label-success"><i class="bx bx-check me-1"></i>{{ __('Completed') }}</span>
                                        @elseif($t->status === 'in_transit')
                                            <span class="badge bg-label-warning"><i class="bx bx-car me-1"></i>{{ __('In Transit') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ ucfirst($t->status) }}</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $t->created_at->format('d M Y, h:i A') }}</small></td>
                                    <td class="text-end">
                                        @if($t->status === 'in_transit')
                                            <form action="{{ route('app-inventory-transfer-receive', $t->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bx bx-check-double me-1"></i> {{ __('Receive & Stock') }}
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-label-secondary" disabled>{{ __('Received') }}</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">{{ __('No branch transfers on record.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Adjust Stock Modal --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="adjustStockForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Adjust Product Stock') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Select Product') }}</label>
                        <select name="product_id" id="modal_product_id" class="form-select" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-qty="{{ $p->qty }}">{{ $p->name }} ({{ __('Current:') }} {{ $p->qty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Adjustment Type') }}</label>
                        <select name="type" id="modal_type" class="form-select" required>
                            <option value="stock_in">{{ __('Stock-In (+) Purchase / Restock') }}</option>
                            <option value="stock_out">{{ __('Stock-Out (-) Internal Use / Write-off') }}</option>
                            <option value="adjustment">{{ __('Count Correction (+ / -)') }}</option>
                            <option value="damaged">{{ __('Damaged Goods (-)') }}</option>
                            <option value="expired">{{ __('Expired Goods (-)') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Quantity to Adjust') }}</label>
                        <input type="number" name="qty" id="modal_qty" class="form-control" placeholder="10" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reason / Notes') }}</label>
                        <input type="text" name="reason" id="modal_reason" class="form-control" placeholder="{{ __('Reason / Notes') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Adjustment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- New Branch Transfer Modal --}}
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-inventory-transfer-store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create Inter-Branch Stock Transfer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('From Branch (Source)') }}</label>
                            <select name="from_branch_id" class="form-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('To Branch (Destination)') }}</label>
                            <select name="to_branch_id" class="form-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Items to Transfer') }}</label>
                        <div id="transfer-items-container">
                            <div class="row g-2 mb-2">
                                <div class="col-8">
                                    <select name="items[0][product_id]" class="form-select" required>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }} ({{ __('Available:') }} {{ $p->qty }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <input type="number" name="items[0][qty]" class="form-control" placeholder="{{ __('Qty') }}" min="1" value="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Dispatch Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Transfer dispatch instructions or courier details...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Dispatch Stock Transfer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-quick-adjust').forEach(btn => {
        btn.addEventListener('click', function() {
            const pId = this.dataset.id;
            document.getElementById('modal_product_id').value = pId;
            const modal = new bootstrap.Modal(document.getElementById('adjustStockModal'));
            modal.show();
        });
    });

    document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            product_id: document.getElementById('modal_product_id').value,
            type: document.getElementById('modal_type').value,
            qty: document.getElementById('modal_qty').value,
            reason: document.getElementById('modal_reason').value,
        };

        fetch('{{ route("app-vendor-inventory-update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (typeof window.AKNotify !== 'undefined') {
                if (res.success) {
                    AKNotify.success(res.message, @json(__('Success')));
                    setTimeout(() => location.reload(), 1500);
                } else {
                    AKNotify.error(res.message || @json(__('Failed to update stock')), @json(__('Error')));
                }
            } else {
                if (res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            }
        });
    });
});
</script>
@endsection
