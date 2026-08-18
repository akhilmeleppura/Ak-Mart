@extends('layouts/layoutMaster')

@section('title', 'Pick & Pack Sheet #' . $fulfillment->fulfillment_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print flex-wrap gap-2">
    <div>
        <a href="{{ route('app-fulfillment') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bx bx-arrow-back me-1"></i> Back to Fulfillments
        </a>
        <h4 class="fw-bold mb-0">Pick List & Packing Slip: {{ $fulfillment->fulfillment_number }}</h4>
    </div>
    <button class="btn btn-primary" onclick="window.print()">
        <i class="bx bx-printer me-1"></i> Print Packing Slip
    </button>
</div>

<div class="card p-4 shadow-sm border">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">AK-MART</h3>
            <p class="text-muted small mb-0">Fulfillment & Logistics Center</p>
            <p class="text-muted small">Origin Warehouse: <strong>{{ $fulfillment->warehouse?->name ?? 'Main Hub' }}</strong></p>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-1">PACKING SLIP</h5>
            <p class="mb-0"><strong>Fulfillment #:</strong> {{ $fulfillment->fulfillment_number }}</p>
            <p class="mb-0"><strong>Order #:</strong> {{ $fulfillment->order?->order_number }}</p>
            <p class="text-muted small">Date: {{ $fulfillment->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold mb-1 text-heading">Ship To Customer:</h6>
            <p class="mb-1"><strong>{{ $fulfillment->order?->customer?->name ?? 'Customer' }}</strong></p>
            <p class="text-muted small mb-0">{{ $fulfillment->order?->shipping_address }}</p>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold mb-1 text-heading">Carrier & Method:</h6>
            <p class="mb-1">Carrier: <strong>{{ $fulfillment->shipping_carrier ?: 'Standard Ground' }}</strong></p>
            <p class="text-muted small mb-0">Tracking: <code>{{ $fulfillment->tracking_number ?: 'Pending Label Scan' }}</code></p>
        </div>
    </div>

    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">Check</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Bin Location</th>
                    <th>Fulfill Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fulfillment->items as $item)
                    <tr>
                        <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                        <td><strong>{{ $item->orderItem?->product_name }}</strong></td>
                        <td><code>{{ $item->orderItem?->product?->sku ?? 'N/A' }}</code></td>
                        <td><span class="badge bg-label-secondary">Aisle 1 - Bin A</span></td>
                        <td class="fw-bold fs-6">{{ $item->qty }} Units</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
        <small class="text-muted">Packed By: _____________________</small>
        <small class="text-muted">Verified By: _____________________</small>
    </div>
</div>

<style>
@media print {
    .no-print, .layout-navbar, .layout-menu, .footer {
        display: none !important;
    }
    .layout-page {
        padding: 0 !important;
    }
}
</style>
@endsection
