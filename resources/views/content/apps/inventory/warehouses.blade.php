@extends('layouts/layoutMaster')

@section('title', 'Multi-Warehouse Management — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-buildings text-primary me-2"></i> Multi-Warehouse Management</h4>
        <p class="text-muted small mb-0">Manage regional distribution centers, fulfillment warehouses, and bin stock allocations</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddWarehouse">
        <i class="bx bx-plus me-1"></i> Add Warehouse
    </button>
</div>

<!-- KPI Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Total Active Warehouses</span>
            <h3 class="fw-bold text-primary my-1">{{ $totalWarehouses }}</h3>
            <small class="text-success"><i class="bx bx-check-circle me-1"></i> Multi-node routing active</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Stock Allocations</span>
            <h3 class="fw-bold text-info my-1">{{ $warehouses->sum('stocks_count') }} SKUs</h3>
            <small class="text-muted">Distributed inventory</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Stock Reservation Engine</span>
            <h3 class="fw-bold text-success my-1">Active</h3>
            <small class="text-muted">Prevents checkout overselling</small>
        </div>
    </div>
</div>

<!-- Warehouses Grid -->
<div class="row g-4">
    @forelse($warehouses as $wh)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-label-primary mb-1">{{ $wh->code }}</span>
                            <h5 class="card-title mb-0 fw-bold">{{ $wh->name }}</h5>
                        </div>
                        <span class="badge {{ $wh->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $wh->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <p class="text-muted small mb-2">
                        <i class="bx bx-map-pin me-1"></i> {{ $wh->address ?: 'Address not set' }} {{ $wh->city ? ', ' . $wh->city : '' }}
                    </p>
                    <p class="text-muted small mb-3">
                        <i class="bx bx-user me-1"></i> Contact: {{ $wh->contact_person ?: 'Manager' }} ({{ $wh->phone ?: 'N/A' }})
                    </p>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="badge bg-label-info">{{ $wh->stocks_count }} Stocked Products</span>
                        <a href="{{ route('app-warehouses-show', $wh->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-box me-1"></i> Manage Stock
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center shadow-sm">
                <i class="bx bx-buildings display-4 text-muted mb-2"></i>
                <h5 class="fw-bold">No Warehouses Registered</h5>
                <p class="text-muted">Create your first regional distribution warehouse to enable multi-location fulfillment.</p>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddWarehouse">
                        <i class="bx bx-plus me-1"></i> Create Warehouse
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Modal Add Warehouse -->
<div class="modal fade" id="modalAddWarehouse" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-warehouses-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bx bx-plus-circle me-1 text-primary"></i> Add New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Central Metro Warehouse" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Warehouse Code</label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. WH-LON-01">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. London">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Street address"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="Manager Name">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Contact Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+44 123 4567">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Warehouse</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
