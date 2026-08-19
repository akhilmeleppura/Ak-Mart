@extends('layouts/layoutMaster')

@section('title', __('Users & RBAC Settings') . ' — AK-Mart')

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
            <i class="bx bx-group text-secondary fs-4"></i>
            <span>{{ __('Users, Default Roles & Access Control (RBAC)') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Configure default registration role assignment and view active system permission levels.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'users-roles') }}">
          @csrf

          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-user-plus text-primary"></i>
            <span>{{ __('Default Role Assignments') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Role for New Staff Accounts') }}</label>
              <select name="default_staff_role" class="form-select">
                <option value="Cashier" {{ ($settings['default_staff_role'] ?? 'Cashier') === 'Cashier' ? 'selected' : '' }}>Cashier / Sales Associate</option>
                <option value="Store Manager" {{ ($settings['default_staff_role'] ?? '') === 'Store Manager' ? 'selected' : '' }}>Store / Branch Manager</option>
                <option value="Inventory Manager" {{ ($settings['default_staff_role'] ?? '') === 'Inventory Manager' ? 'selected' : '' }}>Inventory Coordinator</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Customer Group') }}</label>
              <select name="default_customer_group" class="form-select">
                <option value="Retail" {{ ($settings['default_customer_group'] ?? 'Retail') === 'Retail' ? 'selected' : '' }}>Standard Retail Shopper</option>
                <option value="Wholesale" {{ ($settings['default_customer_group'] ?? '') === 'Wholesale' ? 'selected' : '' }}>Wholesale / Commercial Buyer</option>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save User Role Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
