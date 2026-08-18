@extends('layouts/layoutMaster')

@section('title', __('Duplicate Scanner') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-copy-alt text-warning me-2"></i> {{ __('Smart Duplicate Detection') }}</h4>
        <p class="text-muted small mb-0">{{ __('Identify duplicate products based on name similarity, shared SKUs, and identical barcodes') }}</p>
    </div>
    <a href="{{ route('app-catalog-scanner') }}" class="btn btn-label-secondary">
        <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Scanner') }}
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Potential Duplicates Found') }} ({{ count($duplicates) }})</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Product A') }}</th>
                    <th>{{ __('Product B') }}</th>
                    <th>{{ __('Similarity Score') }}</th>
                    <th>{{ __('Match Reasons') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($duplicates as $dup)
                    <tr>
                        <td>
                            <strong>{{ $dup['product_a']->name }}</strong>
                            <div class="small text-muted">{{ __('SKU:') }} {{ $dup['product_a']->sku ?: 'None' }} • ${{ number_format($dup['product_a']->price, 2) }}</div>
                        </td>
                        <td>
                            <strong>{{ $dup['product_b']->name }}</strong>
                            <div class="small text-muted">{{ __('SKU:') }} {{ $dup['product_b']->sku ?: 'None' }} • ${{ number_format($dup['product_b']->price, 2) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $dup['similarity'] >= 95 ? 'bg-danger' : 'bg-warning' }} fs-6">
                                {{ $dup['similarity'] }}% {{ __('Match') }}
                            </span>
                        </td>
                        <td><small>{{ $dup['reasons'] }}</small></td>
                        <td class="text-end">
                            <a href="{{ route('app-ecommerce-product-edit', $dup['product_b']->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-edit me-1"></i> {{ __('Review / Edit') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-success">
                            <i class="bx bx-check-circle fs-1 d-block mb-2"></i>
                            <strong>{{ __('No duplicate products detected in your catalog!') }}</strong>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
