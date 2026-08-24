@extends('layouts/layoutMaster')

@section('title', __('Product Merchandising & Placement Board') . ' — AK-Mart')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
<style>
    .merch-kpi-card {
        border-radius: 18px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
    }
    .merch-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08);
    }
    .toggle-pill-btn {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        font-weight: 700;
        font-size: 11.5px;
        letter-spacing: 0.02em;
        min-width: 68px;
    }
    .toggle-pill-btn:hover {
        transform: scale(1.06);
    }
    .merch-img-preview {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: contain;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        padding: 2px;
        transition: transform 0.25s ease;
    }
    .merch-img-preview:hover {
        transform: scale(1.15);
    }
</style>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-star text-warning me-2"></i>{{ __('Product Merchandising & Placement Control') }}
            </h4>
            <p class="text-muted small mb-0">{{ __('Control homepage showcases, trending collections, best sellers, and daily deal flags in real time.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('storefront.home') }}" target="_blank" class="btn btn-outline-primary rounded-pill fw-semibold">
                <i class="bx bx-globe me-1"></i> {{ __('View Live Storefront') }}
            </a>
        </div>
    </div>

    <!-- Merchandising Metrics KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card p-3 merch-kpi-card text-center shadow-xs">
                <div class="d-flex align-items-center justify-content-center gap-1.5 text-warning mb-1">
                    <i class="bx bxs-star fs-4"></i>
                    <span class="text-muted small fw-bold text-uppercase">{{ __('Featured Items') }}</span>
                </div>
                <h2 class="fw-bolder text-dark my-1" id="countFeatured">{{ $stats['total_featured'] }}</h2>
                <small class="text-muted">{{ __('Homepage Showcase') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 merch-kpi-card text-center shadow-xs">
                <div class="d-flex align-items-center justify-content-center gap-1.5 text-danger mb-1">
                    <i class="bx bxs-flame fs-4"></i>
                    <span class="text-muted small fw-bold text-uppercase">{{ __('Trending Deals') }}</span>
                </div>
                <h2 class="fw-bolder text-danger my-1" id="countTrending">{{ $stats['total_trending'] }}</h2>
                <small class="text-muted">{{ __('High Conversion Aisle') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 merch-kpi-card text-center shadow-xs">
                <div class="d-flex align-items-center justify-content-center gap-1.5 text-warning mb-1">
                    <i class="bx bxs-trophy fs-4"></i>
                    <span class="text-muted small fw-bold text-uppercase">{{ __('Best Sellers') }}</span>
                </div>
                <h2 class="fw-bolder text-warning my-1" id="countBestseller">{{ $stats['total_bestseller'] }}</h2>
                <small class="text-muted">{{ __('Top Ranked SKUs') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 merch-kpi-card text-center shadow-xs">
                <div class="d-flex align-items-center justify-content-center gap-1.5 text-success mb-1">
                    <i class="bx bxs-zap fs-4"></i>
                    <span class="text-muted small fw-bold text-uppercase">{{ __('Deal of the Day') }}</span>
                </div>
                <h2 class="fw-bolder text-success my-1" id="countDeals">{{ $stats['total_deals'] }}</h2>
                <small class="text-muted">{{ __('24-Hour Special Pricing') }}</small>
            </div>
        </div>
    </div>

    <!-- Products Merchandising Control Board -->
    <div class="card border shadow-xs rounded-4 overflow-hidden">
        <div class="card-header bg-light py-3 border-bottom">
            <form action="{{ route('app-merchandising') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="{{ __('Search SKU, product title, barcode...') }}" value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="flag" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('All Merchandising Badges') }}</option>
                        <option value="featured" {{ request('flag') === 'featured' ? 'selected' : '' }}>⭐ Featured Only</option>
                        <option value="trending" {{ request('flag') === 'trending' ? 'selected' : '' }}>🔥 Trending Only</option>
                        <option value="bestseller" {{ request('flag') === 'bestseller' ? 'selected' : '' }}>🏆 Best Sellers Only</option>
                        <option value="deals" {{ request('flag') === 'deals' ? 'selected' : '' }}>⚡ Daily Deals Only</option>
                    </select>
                </div>

                <div class="col-6 col-md-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ __('Filter Board') }}</button>
                    @if(request()->hasAny(['q', 'flag']))
                        <a href="{{ route('app-merchandising') }}" class="btn btn-label-secondary">{{ __('Reset') }}</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th class="text-center">{{ __('Featured ⭐') }}</th>
                        <th class="text-center">{{ __('Trending 🔥') }}</th>
                        <th class="text-center">{{ __('Best Seller 🏆') }}</th>
                        <th class="text-center">{{ __('Daily Deal ⚡') }}</th>
                        <th class="text-end">{{ __('Related Cross-Sells 🔗') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                        <tr id="product-row-{{ $prod->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $prod->image ? asset($prod->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" class="merch-img-preview" alt="{{ $prod->name }}" onerror="this.src='{{ asset('assets/img/illustrations/boy-with-rocket-light.png') }}'">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-truncate text-dark" style="max-width: 260px;" title="{{ $prod->name }}">{{ $prod->name }}</h6>
                                        <small class="text-muted font-monospace">SKU: {{ $prod->sku }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary rounded-pill">{{ $prod->category?->name ?? 'Unassigned' }}</span>
                            </td>
                            <td>
                                <strong class="text-dark">${{ number_format($prod->price, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm toggle-pill-btn {{ $prod->is_featured ? 'btn-warning text-dark' : 'btn-label-secondary' }} rounded-pill" 
                                        onclick="toggleFlag({{ $prod->id }}, 'is_featured', this)">
                                    <i class="bx {{ $prod->is_featured ? 'bxs-star' : 'bx-star' }} me-0.5"></i>
                                    <span>{{ $prod->is_featured ? 'Active' : 'Off' }}</span>
                                </button>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm toggle-pill-btn {{ $prod->is_trending ? 'btn-danger' : 'btn-label-secondary' }} rounded-pill" 
                                        onclick="toggleFlag({{ $prod->id }}, 'is_trending', this)">
                                    <i class="bx {{ $prod->is_trending ? 'bxs-flame' : 'bx-flame' }} me-0.5"></i>
                                    <span>{{ $prod->is_trending ? 'Active' : 'Off' }}</span>
                                </button>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm toggle-pill-btn {{ $prod->is_best_seller ? 'btn-primary' : 'btn-label-secondary' }} rounded-pill" 
                                        onclick="toggleFlag({{ $prod->id }}, 'is_best_seller', this)">
                                    <i class="bx {{ $prod->is_best_seller ? 'bxs-trophy' : 'bx-trophy' }} me-0.5"></i>
                                    <span>{{ $prod->is_best_seller ? 'Active' : 'Off' }}</span>
                                </button>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm toggle-pill-btn {{ $prod->deal_of_the_day ? 'btn-success' : 'btn-label-secondary' }} rounded-pill" 
                                        onclick="toggleFlag({{ $prod->id }}, 'deal_of_the_day', this)">
                                    <i class="bx {{ $prod->deal_of_the_day ? 'bxs-zap' : 'bx-zap' }} me-0.5"></i>
                                    <span>{{ $prod->deal_of_the_day ? 'Active' : 'Off' }}</span>
                                </button>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('app-product-relations', $prod->id) }}" class="btn btn-sm btn-label-primary rounded-pill px-3 fw-semibold">
                                    <i class="bx bx-git-merge me-1"></i> {{ __('Linked Items') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bx bx-package fs-1 mb-2 d-block text-secondary"></i>
                                <h6>{{ __('No products matching merchandising filters.') }}</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end py-2 bg-light">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
function toggleFlag(productId, flag, btn) {
    const isCurrentlyActive = btn.classList.contains('btn-warning') || 
                              btn.classList.contains('btn-danger') || 
                              btn.classList.contains('btn-primary') || 
                              btn.classList.contains('btn-success');
    
    // Disable button temporarily during AJAX
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span>`;

    fetch(`/store-management/merchandising/${productId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ flag: flag })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            const newState = !isCurrentlyActive;
            
            // Define badge styling per flag
            let activeClass = 'btn-warning text-dark';
            let iconClass = 'bxs-star';
            if (flag === 'is_trending') { activeClass = 'btn-danger'; iconClass = 'bxs-flame'; }
            if (flag === 'is_best_seller') { activeClass = 'btn-primary'; iconClass = 'bxs-trophy'; }
            if (flag === 'deal_of_the_day') { activeClass = 'btn-success'; iconClass = 'bxs-zap'; }

            if (newState) {
                btn.className = `btn btn-sm toggle-pill-btn ${activeClass} rounded-pill`;
                btn.innerHTML = `<i class="bx ${iconClass} me-0.5"></i><span>Active</span>`;
            } else {
                btn.className = `btn btn-sm toggle-pill-btn btn-label-secondary rounded-pill`;
                btn.innerHTML = `<i class="bx ${iconClass.replace('bxs-', 'bx-')} me-0.5"></i><span>Off</span>`;
            }

            // Show lightweight toast notification
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Placement updated for ${flag.replace(/_/g, ' ')}`,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        } else {
            btn.innerHTML = `<span>Error</span>`;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = `<span>Retry</span>`;
    });
}
</script>
@endsection
