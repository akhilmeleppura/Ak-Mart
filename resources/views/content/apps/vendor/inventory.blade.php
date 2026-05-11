@extends('layouts/layoutMaster')

@section('title', 'Advanced Inventory Management')

@section('content')
<div class="row g-6 mb-6">
    {{-- Summary Widgets --}}
    <div class="col-md-4">
        <div class="card bg-label-primary">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-primary"><i class="bx bx-package"></i></span>
                </div>
                <h4 class="mb-1 fw-bold">{{ $products->count() }}</h4>
                <p class="mb-0">Total Products</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-label-warning">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-warning"><i class="bx bx-error"></i></span>
                </div>
                <h4 class="mb-1 fw-bold">{{ $lowStockProducts->count() }}</h4>
                <p class="mb-0">Low Stock Items</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-label-danger">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-danger"><i class="bx bx-block"></i></span>
                </div>
                <h4 class="mb-1 fw-bold">{{ $outOfStockProducts->count() }}</h4>
                <p class="mb-0">Out of Stock Items</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Inventory Ledger</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary"><i class="bx bx-export me-1"></i> Export</button>
            <button class="btn btn-sm btn-primary"><i class="bx bx-scan me-1"></i> Barcode Scanner</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU / Barcode</th>
                        <th>Current Stock</th>
                        <th>Alert Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $product->image ?? 'https://via.placeholder.com/50' }}" class="rounded me-3" width="40" height="40" alt="">
                                    <div>
                                        <span class="text-body fw-semibold">{{ $product->name }}</span>
                                        <div class="text-muted small">{{ $product->category->name ?? 'General' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-dark">{{ $product->sku ?? 'N/A' }}</code><br>
                                <small class="text-muted">{{ $product->barcode ?? '' }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" class="form-control form-control-sm w-px-75 stock-input" value="{{ $product->qty }}" data-id="{{ $product->id }}">
                                    <span class="text-muted small">Units</span>
                                </div>
                            </td>
                            <td>{{ $product->stock_alert_level }}</td>
                            <td>
                                @if($product->qty <= 0)
                                    <span class="badge bg-label-danger">Out of Stock</span>
                                @elseif($product->qty <= $product->stock_alert_level)
                                    <span class="badge bg-label-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-label-success">Healthy</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-icon btn-label-primary save-stock" style="display: none;">
                                    <i class="bx bx-save"></i>
                                </button>
                                <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-label-secondary">
                                    <i class="bx bx-history"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">No products found in inventory.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockInputs = document.querySelectorAll('.stock-input');
    
    stockInputs.forEach(input => {
        input.addEventListener('input', function() {
            const btn = this.parentElement.parentElement.parentElement.querySelector('.save-stock');
            btn.style.display = 'inline-flex';
        });
    });

    document.querySelectorAll('.save-stock').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const input = row.querySelector('.stock-input');
            const productId = input.dataset.id;
            const qty = input.value;

            fetch('{{ route("app-vendor-inventory-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, qty: qty })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.style.display = 'none';
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Stock level updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });
        });
    });
});
</script>
@endsection
