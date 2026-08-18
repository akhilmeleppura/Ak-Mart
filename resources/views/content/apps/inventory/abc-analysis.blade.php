@extends('layouts/layoutMaster')

@section('title', 'ABC Inventory Analysis & Dead Stock — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-pie-chart-alt text-primary me-2"></i> ABC Inventory Analysis & Dead Stock</h4>
        <p class="text-muted small mb-0">Prioritize capital allocation by classifying inventory into Class A (Top 80% Revenue), Class B (Next 15%), and Class C (Bottom 5%)</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-success">
            <span class="text-muted small">Class A Items (High Value)</span>
            <h3 class="fw-bold text-success my-1">{{ $countClassA }} Products</h3>
            <small class="text-muted">Generates ~80% of store revenue</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-primary">
            <span class="text-muted small">Class B Items (Moderate)</span>
            <h3 class="fw-bold text-primary my-1">{{ $countClassB }} Products</h3>
            <small class="text-muted">Generates ~15% of store revenue</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-warning">
            <span class="text-muted small">Class C Items (Low Value)</span>
            <h3 class="fw-bold text-warning my-1">{{ $countClassC }} Products</h3>
            <small class="text-muted">Generates ~5% of store revenue</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-danger">
            <span class="text-muted small">Tied-Up Capital in Dead Stock</span>
            <h3 class="fw-bold text-danger my-1">${{ number_format($totalTiedUpCapital, 2) }}</h3>
            <small class="text-muted">{{ count($deadStock) }} zero-sales SKUs (60+ days)</small>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3 gap-2" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-abc" role="tab">
                <i class="bx bx-list-check me-1"></i> ABC Classification Matrix
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-deadstock" role="tab">
                <i class="bx bx-alarm-exclamation me-1"></i> Dead Stock / Stagnant Inventory ({{ count($deadStock) }})
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        {{-- Tab 1: ABC Matrix --}}
        <div class="tab-pane fade show active" id="tab-abc" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Inventory ABC Classification</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Classification</th>
                                <th>Product</th>
                                <th>Total Revenue Generated</th>
                                <th>Revenue Share</th>
                                <th>Units Sold</th>
                                <th>Current Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abcItems as $item)
                                <tr>
                                    <td>
                                        <span class="badge {{ $item['abc_category'] === 'A' ? 'bg-success' : ($item['abc_category'] === 'B' ? 'bg-primary' : 'bg-warning') }} fs-6">
                                            Class {{ $item['abc_category'] }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $item['product']->name }}</strong></td>
                                    <td class="fw-bold text-success">${{ number_format($item['revenue'], 2) }}</td>
                                    <td>{{ $item['revenue_share'] }}%</td>
                                    <td>{{ $item['units_sold'] }} Units</td>
                                    <td><span class="badge bg-label-secondary">{{ $item['stock'] }} On Hand</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No sales data available for ABC classification yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 2: Dead Stock --}}
        <div class="tab-pane fade" id="tab-deadstock" role="tabpanel">
            <div class="card shadow-sm border">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Dead Stock & Stagnant Items (Zero Sales in 60+ Days)</h5>
                    <span class="badge bg-danger">Clearance Recommended</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>On Hand Quantity</th>
                                <th>Retail Unit Price</th>
                                <th>Tied-Up Capital Value</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deadStock as $ds)
                                <tr>
                                    <td><strong>{{ $ds['product']->name }}</strong></td>
                                    <td><code>{{ $ds['product']->sku }}</code></td>
                                    <td><span class="badge bg-label-danger fs-6">{{ $ds['qty'] }} Units</span></td>
                                    <td>${{ number_format($ds['product']->price, 2) }}</td>
                                    <td class="fw-bold text-danger">${{ number_format($ds['tied_up_capital'], 2) }}</td>
                                    <td>
                                        <a href="{{ route('app-ecommerce-product-edit', $ds['product']->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bx bx-tag me-1"></i> Apply Clearance Discount
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-success">
                                        <i class="bx bx-check-circle fs-3 d-block mb-1"></i>
                                        No dead stock detected! All inventoried items have active sales velocity.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
