@extends('layouts/layoutMaster')

@section('title', __('Workflow Automation Settings') . ' — AK-Mart')

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
            <i class="bx bx-git-repo-forked text-primary fs-4"></i>
            <span>{{ __('Event-Driven Workflow Automation Rules') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Automate multi-step business actions based on real-time commerce events.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'automation') }}">
          @csrf

          <div class="list-group list-group-flush mb-5">
            <!-- Automation Rule 1 -->
            <div class="list-group-item p-4 border rounded mb-3 bg-light-subtle">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary">{{ __('Trigger') }}</span>
                    <h6 class="mb-0 fw-bold">{{ __('Order Created & Confirmed') }}</h6>
                  </div>
                  <p class="text-muted small mb-2">{{ __('WHEN a customer successfully places an order:') }}</p>
                  <ul class="small ps-3 mb-0 text-muted">
                    <li>{{ __('Deduct inventory from corresponding branch.') }}</li>
                    <li>{{ __('Generate printable PDF invoice.') }}</li>
                    <li>{{ __('Send email receipt & WhatsApp notification.') }}</li>
                  </ul>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="auto_workflow_order_placed" value="0">
                  <input class="form-check-input" type="checkbox" name="auto_workflow_order_placed" value="1" {{ ($settings['auto_workflow_order_placed'] ?? '1') === '1' ? 'checked' : '' }}>
                </div>
              </div>
            </div>

            <!-- Automation Rule 2 -->
            <div class="list-group-item p-4 border rounded mb-3 bg-light-subtle">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-warning">{{ __('Trigger') }}</span>
                    <h6 class="mb-0 fw-bold">{{ __('Inventory Drops Below Safety Threshold') }}</h6>
                  </div>
                  <p class="text-muted small mb-2">{{ __('WHEN available stock <= Low Stock Threshold:') }}</p>
                  <ul class="small ps-3 mb-0 text-muted">
                    <li>{{ __('Flag product with low-stock badge in admin dashboard.') }}</li>
                    <li>{{ __('Dispatch urgent email & WhatsApp alert to store manager.') }}</li>
                  </ul>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="auto_workflow_low_stock" value="0">
                  <input class="form-check-input" type="checkbox" name="auto_workflow_low_stock" value="1" {{ ($settings['auto_workflow_low_stock'] ?? '1') === '1' ? 'checked' : '' }}>
                </div>
              </div>
            </div>

            <!-- Automation Rule 3 -->
            <div class="list-group-item p-4 border rounded bg-light-subtle">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-danger">{{ __('Trigger') }}</span>
                    <h6 class="mb-0 fw-bold">{{ __('Payment Gateway Transaction Failed') }}</h6>
                  </div>
                  <p class="text-muted small mb-2">{{ __('WHEN card or UPI payment attempt fails:') }}</p>
                  <ul class="small ps-3 mb-0 text-muted">
                    <li>{{ __('Send recovery email with retry payment link.') }}</li>
                    <li>{{ __('Log failed gateway response for security audit.') }}</li>
                  </ul>
                </div>
                <div class="form-check form-switch">
                  <input type="hidden" name="auto_workflow_payment_failed" value="0">
                  <input class="form-check-input" type="checkbox" name="auto_workflow_payment_failed" value="1" {{ ($settings['auto_workflow_payment_failed'] ?? '1') === '1' ? 'checked' : '' }}>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Automation Rules') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
