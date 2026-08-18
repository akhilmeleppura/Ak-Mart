@extends('layouts/layoutMaster')

@section('title', __('Audit Sheet') . ' ' . $stockCount->count_number . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('app-stock-counts') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Audit Sessions') }}
        </a>
        <h4 class="fw-bold mb-0">{{ __('Stock Audit:') }} {{ $stockCount->count_number }}</h4>
        <small class="text-muted">{{ __('Type:') }} {{ ucfirst($stockCount->type) }} {{ __('Count') }} | {{ __('Location:') }} {{ $stockCount->warehouse?->name ?? __('Global') }} | {{ __('Status:') }} <span class="badge {{ $stockCount->status === 'reconciled' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($stockCount->status) }}</span></small>
    </div>
    @if($stockCount->status !== 'reconciled')
        <form action="{{ route('app-stock-counts-reconcile', $stockCount->id) }}" method="POST" onsubmit="return confirm('{{ __('Reconcile inventory? Live product quantities will be updated to match the counted values.') }}');">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bx bx-check-double me-1"></i> {{ __('Reconcile & Update Inventory') }}
            </button>
        </form>
    @endif
</div>

<!-- Items Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Product Count Sheet') }} ({{ $stockCount->items->count() }} SKUs)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Product Name') }}</th>
                    <th>{{ __('SKU') }}</th>
                    <th>{{ __('System Expected Qty') }}</th>
                    <th>{{ __('Physical Counted Qty') }}</th>
                    <th>{{ __('Variance / Difference') }}</th>
                    <th>{{ __('Remarks') }}</th>
                    @if($stockCount->status !== 'reconciled')
                        <th>{{ __('Update') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($stockCount->items as $item)
                    <tr>
                        <td><strong>{{ $item->product?->name ?? __('Product') }}</strong></td>
                        <td><code>{{ $item->product?->sku ?? 'N/A' }}</code></td>
                        <td><span class="badge bg-label-secondary fs-6">{{ $item->expected_qty }}</span></td>
                        <td>
                            @if($stockCount->status !== 'reconciled')
                                <input type="number" id="counted-{{ $item->id }}" class="form-control form-control-sm" style="width: 100px;" value="{{ $item->counted_qty }}" min="0">
                            @else
                                <span class="fw-bold fs-6">{{ $item->counted_qty }}</span>
                            @endif
                        </td>
                        <td>
                            <span id="diff-{{ $item->id }}" class="badge {{ $item->difference == 0 ? 'bg-label-success' : ($item->difference > 0 ? 'bg-label-info' : 'bg-label-danger') }} fs-6">
                                {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                            </span>
                        </td>
                        <td>
                            @if($stockCount->status !== 'reconciled')
                                <input type="text" id="remarks-{{ $item->id }}" class="form-control form-control-sm" value="{{ $item->remarks }}" placeholder="{{ __('Discrepancy reason') }}">
                            @else
                                <small class="text-muted">{{ $item->remarks ?: __('No remarks') }}</small>
                            @endif
                        </td>
                        @if($stockCount->status !== 'reconciled')
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="saveCountItem({{ $item->id }})">
                                    <i class="bx bx-save"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function saveCountItem(itemId) {
    const countedQty = document.getElementById('counted-' + itemId).value;
    const remarks = document.getElementById('remarks-' + itemId).value;

    fetch(`{{ url('/inventory/stock-counts/' . $stockCount->id . '/item') }}/${itemId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ counted_qty: countedQty, remarks: remarks })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            const diffElem = document.getElementById('diff-' + itemId);
            diffElem.textContent = (data.difference > 0 ? '+' : '') + data.difference;
            if (typeof window.AKNotify !== 'undefined') {
                AKNotify.success(@json(__('Count Entry Saved')), @json(__('Success')));
            } else {
                alert('Count entry saved!');
            }
        }
    });
}
</script>
@endsection
