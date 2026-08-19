@extends('layouts/layoutMaster')

@section('title', __('Branches & Warehouses Settings') . ' — AK-Mart')

@section('content')
<div class="row g-6">
  <div class="col-12 col-lg-4 col-xl-3">
    @include('content.apps._settings-sidebar')
  </div>

  <div class="col-12 col-lg-8 col-xl-9">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-map-pin text-danger fs-4"></i>
            <span>{{ __('Branches, Warehouses & Order Routing') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure default fulfillment facilities, stock routing preferences, and inter-branch transfers.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'locations') }}">
          @csrf

          <!-- Default Facilities -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-buildings text-primary"></i>
            <span>{{ __('Primary Fulfillment Facilities') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Store Branch') }}</label>
              <select name="default_branch" class="form-select">
                @foreach($branches as $b)
                  <option value="{{ $b->id }}" {{ ($settings['default_branch'] ?? '1') == $b->id ? 'selected' : '' }}>
                    {{ $b->name }} ({{ $b->city ?? 'Main' }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Central Warehouse') }}</label>
              <select name="default_warehouse" class="form-select">
                @forelse($warehouses as $w)
                  <option value="{{ $w->id }}" {{ ($settings['default_warehouse'] ?? '') == $w->id ? 'selected' : '' }}>
                    {{ $w->name }}
                  </option>
                @empty
                  <option value="1">{{ __('Central Distribution Warehouse #1') }}</option>
                @endforelse
              </select>
            </div>
          </div>

          <!-- Order Routing Strategy -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-navigation text-primary"></i>
            <span>{{ __('Automated Order Fulfillment Routing') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Order Routing Logic') }}</label>
              <select name="order_routing_strategy" class="form-select">
                <option value="stock_availability" {{ ($settings['order_routing_strategy'] ?? 'stock_availability') === 'stock_availability' ? 'selected' : '' }}>
                  {{ __('Highest Stock Availability First') }}
                </option>
                <option value="nearest_branch" {{ ($settings['order_routing_strategy'] ?? '') === 'nearest_branch' ? 'selected' : '' }}>
                  {{ __('Nearest Geolocation Branch to Customer') }}
                </option>
                <option value="primary_branch_only" {{ ($settings['order_routing_strategy'] ?? '') === 'primary_branch_only' ? 'selected' : '' }}>
                  {{ __('Always Route to Default Primary Branch') }}
                </option>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Location Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
