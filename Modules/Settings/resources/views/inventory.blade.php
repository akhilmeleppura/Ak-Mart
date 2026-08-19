@extends('layouts/layoutMaster')

@section('title', __('Inventory Management Settings') . ' — AK-Mart')

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
            <i class="bx bx-layer text-danger fs-4"></i>
            <span>{{ __('Inventory Control & Stock Rules') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure stock tracking, low stock alert thresholds, stock reservation timeouts, and auto-deduction triggers.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'inventory') }}">
          @csrf

          <!-- Stock Tracking Rules -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-box text-primary"></i>
            <span>{{ __('Inventory Tracking & Availability') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Real-Time Inventory Tracking') }}</h6>
                <small class="text-muted">{{ __('Automatically track on-hand quantities across all branches and warehouses.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="inventory_tracking_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="inventory_tracking_enabled" value="1" {{ ($settings['inventory_tracking_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Allow Negative Stock / Overselling') }}</h6>
                <small class="text-muted">{{ __('Permit sales even when item quantity falls below zero.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="allow_negative_stock" value="0">
                <input class="form-check-input" type="checkbox" name="allow_negative_stock" value="1" {{ ($settings['allow_negative_stock'] ?? '0') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Automatically Restock on Order Cancellation') }}</h6>
                <small class="text-muted">{{ __('Return reserved or deducted quantities back into available inventory when an order is cancelled.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="restock_on_cancel" value="0">
                <input class="form-check-input" type="checkbox" name="restock_on_cancel" value="1" {{ ($settings['restock_on_cancel'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <!-- Stock Thresholds -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-alarm-exclamation text-primary"></i>
            <span>{{ __('Thresholds & Stock Reservation') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Low Stock Warning Threshold (Units)') }}</label>
              <input type="number" name="inventory_low_stock_threshold" class="form-control" value="{{ $settings['inventory_low_stock_threshold'] ?? '5' }}" />
              <small class="text-muted">{{ __('Triggers yellow warning badge and admin email.') }}</small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Critical Stock Threshold (Units)') }}</label>
              <input type="number" name="inventory_critical_stock_threshold" class="form-control" value="{{ $settings['inventory_critical_stock_threshold'] ?? '2' }}" />
              <small class="text-muted">{{ __('Triggers red alert and urgent WhatsApp notification.') }}</small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Stock Reservation Timeout (Minutes)') }}</label>
              <input type="number" name="inventory_reservation_timeout_minutes" class="form-control" value="{{ $settings['inventory_reservation_timeout_minutes'] ?? '15' }}" />
              <small class="text-muted">{{ __('Temporary hold during customer checkout.') }}</small>
            </div>
          </div>

          <!-- Stock Deduction Stage -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-transfer text-primary"></i>
            <span>{{ __('Stock Deduction Event Stage') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Deduct Inventory Quantity When:') }}</label>
              <select name="inventory_deduct_stage" class="form-select">
                <option value="order_placed" {{ ($settings['inventory_deduct_stage'] ?? 'order_placed') === 'order_placed' ? 'selected' : '' }}>{{ __('When Order is Placed (Immediate Hold)') }}</option>
                <option value="payment_completed" {{ ($settings['inventory_deduct_stage'] ?? '') === 'payment_completed' ? 'selected' : '' }}>{{ __('When Payment is Completed / Verified') }}</option>
                <option value="order_shipped" {{ ($settings['inventory_deduct_stage'] ?? '') === 'order_shipped' ? 'selected' : '' }}>{{ __('When Order is Dispatched / Shipped') }}</option>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Inventory Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
