@extends('layouts/layoutMaster')

@section('title', __('Customer & Loyalty Settings') . ' — AK-Mart')

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
            <i class="bx bx-user-check text-success fs-4"></i>
            <span>{{ __('Customer Accounts & Loyalty Rewards') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Manage customer registration verification, account activation rules, and the reward points loyalty program.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'customers') }}">
          @csrf

          <!-- Account Rules -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-shield text-primary"></i>
            <span>{{ __('Account Registration & Verification') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Require Email Verification on Registration') }}</h6>
                <small class="text-muted">{{ __('Customers must click a verification link before accessing their account.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="customer_require_email_verification" value="0">
                <input class="form-check-input" type="checkbox" name="customer_require_email_verification" value="1" {{ ($settings['customer_require_email_verification'] ?? '0') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Require Manual Administrator Approval for B2B / New Accounts') }}</h6>
                <small class="text-muted">{{ __('Admins must approve new accounts before customers can place orders.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="customer_require_admin_approval" value="0">
                <input class="form-check-input" type="checkbox" name="customer_require_admin_approval" value="1" {{ ($settings['customer_require_admin_approval'] ?? '0') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <!-- Loyalty Program -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-gift text-primary"></i>
            <span>{{ __('AK-Mart Loyalty & Reward Points Program') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-4">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Enable Store Loyalty Points Program') }}</h6>
                <small class="text-muted">{{ __('Reward repeat shoppers with points redeemable for discounts on future orders.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="loyalty_program_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="loyalty_program_enabled" value="1" {{ ($settings['loyalty_program_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Points Earned Per $1 Spent') }}</label>
              <input type="number" name="loyalty_points_per_dollar" class="form-control" value="{{ $settings['loyalty_points_per_dollar'] ?? '1' }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Redemption Value Per 100 Points ($)') }}</label>
              <input type="number" step="0.01" name="loyalty_point_redemption_value" class="form-control" value="{{ $settings['loyalty_point_redemption_value'] ?? '5.00' }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Minimum Points Required to Redeem') }}</label>
              <input type="number" name="loyalty_min_points_to_redeem" class="form-control" value="{{ $settings['loyalty_min_points_to_redeem'] ?? '100' }}" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Customer Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
