@extends('layouts/layoutMaster')

@section('title', $warehouse->name . ' — ' . __('Warehouse Details'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('app-warehouses') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Warehouses') }}
        </a>
        <h4 class="fw-bold mb-0"><i class="bx bx-building text-primary me-2"></i> {{ $warehouse->name }} ({{ $warehouse->code }})</h4>
        <small class="text-muted"><i class="bx bx-map-pin me-1"></i> {{ $warehouse->address ?: __('Address not set') }} {{ $warehouse->city ? ', ' . $warehouse->city : '' }}</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAllocateStock">
        <i class="bx bx-plus me-1"></i> {{ __('Allocate Product Stock') }}
    </button>
</div>

<!-- Stock Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Warehouse Inventory Allocations') }}</h5>
        <span class="badge bg-label-primary">{{ $warehouse->stocks->count() }} {{ __('Products Assigned') }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('SKU') }}</th>
                    <th>{{ __('Bin Location') }}</th>
                    <th>{{ __('On Hand Qty') }}</th>
                    <th>{{ __('Committed / Reserved') }}</th>
                    <th>{{ __('Available Qty') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouse->stocks as $stock)
                    <tr>
                        <td>
                            <strong>{{ $stock->product?->name ?? __('Product') }}</strong>
                        </td>
                        <td><code>{{ $stock->product?->sku ?? 'N/A' }}</code></td>
                        <td>
                            <span class="badge bg-label-secondary">{{ $stock->bin_location ?: __('Unassigned') }}</span>
                        </td>
                        <td class="fw-bold fs-6">{{ $stock->qty }}</td>
                        <td>
                            <span class="badge bg-label-warning">{{ $stock->reserved_qty + $stock->committed_qty }}</span>
                        </td>
                        <td class="fw-bold text-success fs-6">{{ $stock->available_qty }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditStock-{{ $stock->id }}">
                                <i class="bx bx-edit-alt"></i> {{ __('Adjust') }}
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="modalEditStock-{{ $stock->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('app-warehouses-stock-update', $warehouse->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $stock->product_id }}">
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold">{{ __('Adjust Warehouse Allocation') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-3">{{ __('Product:') }} <strong>{{ $stock->product?->name }}</strong></p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('On Hand Quantity') }}</label>
                                            <input type="number" name="qty" class="form-control" value="{{ $stock->qty }}" min="0" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Bin Location Code') }}</label>
                                            <input type="text" name="bin_location" class="form-control" value="{{ $stock->bin_location }}" placeholder="e.g. AISLE-2-SHELF-B">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Save Adjustment') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">{{ __('No products allocated to this warehouse yet. Click \'Allocate Product Stock\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Allocate Stock -->
<div class="modal fade" id="modalAllocateStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-warehouses-stock-update', $warehouse->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">{{ __('Allocate Product Stock') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Select Product') }}</label>
                        <select name="product_id" class="form-select" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ __('Total Global Stock:') }} {{ $p->qty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Warehouse Quantity') }}</label>
                        <input type="number" name="qty" class="form-control" value="10" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Bin Location Code') }}</label>
                        <input type="text" name="bin_location" class="form-control" placeholder="e.g. BIN-A101">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Allocation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
