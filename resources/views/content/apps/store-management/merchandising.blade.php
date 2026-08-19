@extends('layouts/layoutMaster')

@section('title', __('Product Merchandising & Placement Board') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-star text-primary me-2"></i> {{ __('Product Merchandising & Placement Control') }}</h4>
        <p class="text-muted small mb-0">{{ __('Control homepage showcases, trending collections, best sellers, and daily deal flags in real time') }}</p>
    </div>
</div>

<!-- Merchandising Metrics -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm rounded-3 text-center">
            <span class="text-muted small">{{ __('⭐ Featured Items') }}</span>
            <h3 class="fw-bold text-primary my-1">{{ $stats['total_featured'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm rounded-3 text-center">
            <span class="text-muted small">{{ __('🔥 Trending Deals') }}</span>
            <h3 class="fw-bold text-danger my-1">{{ $stats['total_trending'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm rounded-3 text-center">
            <span class="text-muted small">{{ __('🏆 Best Sellers') }}</span>
            <h3 class="fw-bold text-warning my-1">{{ $stats['total_bestseller'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border shadow-sm rounded-3 text-center">
            <span class="text-muted small">{{ __('⚡ Deal of the Day') }}</span>
            <h3 class="fw-bold text-success my-1">{{ $stats['total_deals'] }}</h3>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card border shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form action="{{ route('app-merchandising') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm rounded-pill" placeholder="{{ __('Search SKU, product...') }}" value="{{ request('q') }}">
            <button class="btn btn-sm btn-primary rounded-pill px-3" type="submit"><i class="bx bx-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th class="text-center">{{ __('Featured ⭐') }}</th>
                    <th class="text-center">{{ __('Trending 🔥') }}</th>
                    <th class="text-center">{{ __('Best Seller 🏆') }}</th>
                    <th class="text-center">{{ __('Daily Deal ⚡') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $prod)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="45" height="45" class="rounded object-fit-contain bg-light p-1">
                                <div>
                                    <h6 class="mb-0 fw-bold text-truncate" style="max-width: 250px;">{{ $prod->name }}</h6>
                                    <small class="text-muted">SKU: {{ $prod->sku }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">{{ $prod->category?->name ?? 'Unassigned' }}</span></td>
                        <td><strong>${{ number_format($prod->price, 2) }}</strong></td>
                        <td class="text-center">
                            <button class="btn btn-sm {{ $prod->is_featured ? 'btn-warning' : 'btn-outline-secondary' }} rounded-pill" onclick="toggleFlag({{ $prod->id }}, 'is_featured', this)">
                                {{ $prod->is_featured ? 'Active' : 'Off' }}
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm {{ $prod->is_trending ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill" onclick="toggleFlag({{ $prod->id }}, 'is_trending', this)">
                                {{ $prod->is_trending ? 'Active' : 'Off' }}
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm {{ $prod->is_best_seller ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill" onclick="toggleFlag({{ $prod->id }}, 'is_best_seller', this)">
                                {{ $prod->is_best_seller ? 'Active' : 'Off' }}
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm {{ $prod->deal_of_the_day ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill" onclick="toggleFlag({{ $prod->id }}, 'deal_of_the_day', this)">
                                {{ $prod->deal_of_the_day ? 'Active' : 'Off' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">{{ __('No products found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('page-script')
<script>
function toggleFlag(productId, flag, btn) {
    fetch(`/store-management/merchandising/${productId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ flag: flag })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}
</script>
@endsection
