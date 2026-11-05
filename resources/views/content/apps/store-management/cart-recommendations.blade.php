@extends('layouts/contentNavbarLayout')

@section('title', 'Cart Recommendations — Store Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Page Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h4 class="fw-bold mb-0"><i class="bx bx-cart-add text-primary me-2"></i>Cart Recommendations</h4>
      <small class="text-muted">Configure what products appear as suggestions on the shopping cart page.</small>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('storefront.cart') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bx bx-link-external me-1"></i> Preview Cart
      </a>
      <a href="{{ route('app-merchandising') }}" class="btn btn-outline-primary btn-sm rounded-pill">
        <i class="bx bx-badge-check me-1"></i> Merchandising
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
      <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form method="POST" action="{{ route('app-cart-recommendations-save') }}">
    @csrf

    <div class="row g-4">

      {{-- LEFT: Main Settings --}}
      <div class="col-lg-8">

        {{-- Enable Toggle Card --}}
        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h6 class="fw-bold mb-1"><i class="bx bx-power-off text-success me-2"></i>Enable Cart Recommendations</h6>
                <small class="text-muted">Show a "You Might Also Like" section below the cart.</small>
              </div>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" name="enabled" id="enabledToggle" style="width:3rem;height:1.5rem;"
                  {{ ($config['enabled'] ?? true) ? 'checked' : '' }}>
              </div>
            </div>
          </div>
        </div>

        {{-- Title & Strategy --}}
        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-header border-bottom px-4 py-3">
            <h6 class="fw-bold mb-0"><i class="bx bx-heading text-primary me-2"></i>Display Settings</h6>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label fw-semibold">Section Title</label>
                <input type="text" name="title" class="form-control rounded-3"
                  value="{{ old('title', $config['title'] ?? 'You Might Also Like') }}"
                  placeholder="e.g. You Might Also Like, Frequently Bought Together">
                <small class="text-muted">Displayed as the heading above recommended products.</small>
              </div>
              <div class="col-md-5">
                <label class="form-label fw-semibold">Max Products to Show</label>
                <select name="limit" class="form-select rounded-3">
                  @foreach([4,6,8,10,12] as $n)
                    <option value="{{ $n }}" {{ ($config['limit'] ?? 6) == $n ? 'selected' : '' }}>{{ $n }} products</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>

        {{-- Recommendation Strategy --}}
        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-header border-bottom px-4 py-3">
            <h6 class="fw-bold mb-0"><i class="bx bx-brain text-warning me-2"></i>Recommendation Strategy</h6>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              @php
                $strategies = [
                  ['auto',        'bx bx-magic-wand',   'text-primary', 'Smart Auto (Recommended)', 'Combines manual relations, pinned items, same-category, trending, then random fallback.'],
                  ['manual',      'bx bx-link',         'text-info',    'Manual Relations Only',    'Only shows products manually linked via Product Relations (cross-sells, suggested, related).'],
                  ['category',    'bx bx-category',     'text-success', 'Same Category',            'Shows products from the same categories as items currently in the cart.'],
                  ['trending',    'bx bx-trending-up',  'text-danger',  'Trending & Best Sellers',  'Shows globally trending, best-selling, or featured products regardless of cart contents.'],
                  ['bestsellers', 'bx bx-trophy',       'text-warning', 'Best Sellers Only',        'Only surfaces products marked as best sellers.'],
                ];
              @endphp
              @foreach($strategies as [$val, $icon, $color, $label, $desc])
              <div class="col-md-6">
                <label class="d-flex align-items-start gap-3 border rounded-3 p-3 strategy-card {{ ($config['strategy'] ?? 'auto') === $val ? 'border-primary bg-primary bg-opacity-10' : '' }}" style="cursor:pointer;">
                  <input type="radio" name="strategy" value="{{ $val }}" class="strategy-radio mt-1"
                    {{ ($config['strategy'] ?? 'auto') === $val ? 'checked' : '' }}>
                  <div>
                    <div class="fw-bold small"><i class="{{ $icon }} {{ $color }} me-1"></i>{{ $label }}</div>
                    <small class="text-muted">{{ $desc }}</small>
                  </div>
                </label>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Pinned Products --}}
        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0"><i class="bx bx-pin text-danger me-2"></i>Always-Show (Pinned) Products</h6>
            <span class="badge bg-label-danger">{{ count($config['pinned_ids'] ?? []) }} pinned</span>
          </div>
          <div class="card-body p-4">
            <p class="text-muted small mb-3">These products always appear in recommendations regardless of strategy, unless already in the cart.</p>
            <select name="pinned_ids[]" id="pinnedProductsSelect" class="form-select" multiple style="min-height:180px;">
              @foreach($allProducts as $product)
                <option value="{{ $product->id }}"
                  {{ in_array($product->id, $config['pinned_ids'] ?? []) ? 'selected' : '' }}>
                  {{ $product->name }} — ${{ number_format($product->price, 2) }} (SKU: {{ $product->sku ?? 'N/A' }})
                </option>
              @endforeach
            </select>
            <small class="text-muted mt-2 d-block"><i class="bx bx-info-circle me-1"></i>Hold Ctrl / Cmd to select multiple products.</small>
          </div>
        </div>

      </div>

      {{-- RIGHT: Summary & Tips --}}
      <div class="col-lg-4">

        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-body p-4">
            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold mb-3">
              <i class="bx bx-save me-1"></i> Save Recommendations Settings
            </button>
            <a href="{{ route('storefront.cart') }}" target="_blank" class="btn btn-outline-secondary rounded-pill w-100 small">
              <i class="bx bx-link-external me-1"></i> View Live Cart Page
            </a>
          </div>
        </div>

        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-header border-bottom px-4 py-3">
            <h6 class="fw-bold mb-0"><i class="bx bx-info-circle text-info me-2"></i>Current Config</h6>
          </div>
          <div class="card-body p-4">
            <ul class="list-unstyled small mb-0">
              <li class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Status</span>
                <span class="badge {{ ($config['enabled'] ?? true) ? 'bg-label-success' : 'bg-label-secondary' }}">
                  {{ ($config['enabled'] ?? true) ? 'Enabled' : 'Disabled' }}
                </span>
              </li>
              <li class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Title</span>
                <strong class="text-truncate ms-2" style="max-width:140px;">{{ $config['title'] ?? 'You Might Also Like' }}</strong>
              </li>
              <li class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Strategy</span>
                <strong>{{ ucfirst($config['strategy'] ?? 'auto') }}</strong>
              </li>
              <li class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Limit</span>
                <strong>{{ $config['limit'] ?? 6 }} products</strong>
              </li>
              <li class="d-flex justify-content-between">
                <span class="text-muted">Pinned</span>
                <strong>{{ count($config['pinned_ids'] ?? []) }} items</strong>
              </li>
            </ul>
          </div>
        </div>

        @if($pinnedProducts->count())
        <div class="card border shadow-xs rounded-4 mb-4">
          <div class="card-header border-bottom px-4 py-3">
            <h6 class="fw-bold mb-0"><i class="bx bx-pin text-danger me-2"></i>Pinned Preview</h6>
          </div>
          <div class="card-body p-3">
            @foreach($pinnedProducts as $pp)
            <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3 bg-light">
              <img src="{{ $pp->image ? asset($pp->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}"
                width="36" height="36" class="rounded-2 object-fit-contain bg-white border p-1" alt="{{ $pp->name }}">
              <div class="overflow-hidden">
                <div class="fw-semibold small text-truncate" style="max-width:170px;">{{ $pp->name }}</div>
                <small class="text-muted">${{ number_format($pp->price, 2) }}</small>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endif

        <div class="card border shadow-xs rounded-4 bg-label-primary">
          <div class="card-body p-4">
            <h6 class="fw-bold text-primary mb-2"><i class="bx bx-bulb me-1"></i>Tips</h6>
            <ul class="small text-muted ps-3 mb-0">
              <li class="mb-1">Use <strong>Smart Auto</strong> for best coverage — tries all strategies in order.</li>
              <li class="mb-1">Pin high-margin or seasonal products to guarantee visibility.</li>
              <li class="mb-1">Set product cross-sells via <a href="{{ route('app-merchandising') }}">Merchandising</a>.</li>
              <li>Products already in cart are always excluded.</li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </form>
</div>
@endsection

@section('page-script')
<script>
document.querySelectorAll('.strategy-radio').forEach(function(radio) {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.strategy-card').forEach(c => {
      c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    if (this.checked) {
      this.closest('.strategy-card').classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    }
  });
});
</script>
@endsection
