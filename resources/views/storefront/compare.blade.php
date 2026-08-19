@extends('layouts.storefrontMaster')

@section('title', __('Product Comparison') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="bx bx-git-compare text-primary me-2"></i> {{ __('Side-by-Side Product Comparison') }}</h3>
            <p class="text-muted mb-0">{{ __('Compare prices, specifications, and nutritional values across up to 4 items.') }}</p>
        </div>
        @if($products->isNotEmpty())
            <form action="{{ route('storefront.compare.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                    <i class="bx bx-trash me-1"></i> {{ __('Clear Comparison') }}
                </button>
            </form>
        @endif
    </div>

    @if($products->isEmpty())
        <div class="card p-5 text-center border shadow-xs rounded-4">
            <i class="bx bx-git-compare fs-1 text-muted mb-3"></i>
            <h4 class="fw-bold text-muted">{{ __('No products selected for comparison') }}</h4>
            <p class="text-muted">{{ __('Browse the supermarket catalog and click the compare icon on products to see their detailed side-by-side specifications.') }}</p>
            <div>
                <a href="{{ route('storefront.shop') }}" class="btn btn-primary rounded-pill px-4 mt-2">{{ __('Browse Catalog') }}</a>
            </div>
        </div>
    @else
        <div class="card border shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start" style="width: 220px;">{{ __('Attribute / Feature') }}</th>
                            @foreach($products as $p)
                                <th style="min-width: 240px; width: {{ 100 / ($products->count() + 1) }}%;">
                                    <div class="position-relative p-2">
                                        <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 shadow-xs border-0 p-1" onclick="removeCompareItem({{ $p->id }})" title="{{ __('Remove from Compare') }}">
                                            <i class="bx bx-x fs-5 text-muted"></i>
                                        </button>
                                        <img src="{{ $p->image ? asset($p->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $p->name }}" width="90" height="90" class="object-fit-contain bg-light rounded-3 p-2 mb-2">
                                        <h6 class="fw-bold mb-1 text-truncate">
                                            <a href="{{ route('storefront.product', $p->id) }}" class="text-dark text-decoration-none">{{ $p->name }}</a>
                                        </h6>
                                        <span class="fs-5 fw-bold text-primary">${{ number_format($p->price, 2) }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('Brand / Producer') }}</td>
                            @foreach($products as $p)
                                <td><span class="badge bg-label-secondary">{{ $p->brand ?: __('General') }}</span></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('Category / Aisle') }}</td>
                            @foreach($products as $p)
                                <td>{{ $p->category?->name ?? __('Supermarket') }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('Stock Availability') }}</td>
                            @foreach($products as $p)
                                <td>
                                    <span class="badge {{ $p->qty > 0 ? 'bg-label-success' : 'bg-label-danger' }}">
                                        {{ $p->qty > 0 ? "In Stock ({$p->qty} units)" : __('Sold Out') }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('Customer Rating') }}</td>
                            @foreach($products as $p)
                                <td>
                                    <span class="text-warning fw-bold"><i class="bx bxs-star"></i> {{ $p->rating_cache ?: '5.0' }}</span>
                                    <small class="text-muted">({{ $p->reviews->count() }} reviews)</small>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('SKU / Barcode') }}</td>
                            @foreach($products as $p)
                                <td class="small text-muted font-monospace">{{ $p->sku ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('Product Description') }}</td>
                            @foreach($products as $p)
                                <td class="small text-muted text-start p-3" style="max-height: 100px; overflow-y: auto;">
                                    {{ Str::limit($p->description, 140, '...') }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="text-start fw-bold bg-light">{{ __('Action') }}</td>
                            @foreach($products as $p)
                                <td>
                                    <button class="btn btn-primary btn-sm rounded-pill w-100" onclick="quickAddToCart({{ $p->id }})" {{ $p->qty <= 0 ? 'disabled' : '' }}>
                                        <i class="bx bx-cart-add me-1"></i> {{ __('Add to Cart') }}
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function removeCompareItem(productId) {
    fetch('{{ route("storefront.compare.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId })
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
