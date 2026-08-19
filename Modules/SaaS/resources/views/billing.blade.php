@extends('layouts/layoutMaster')

@section('title', __('SaaS Billing & Plans') . ' — AK-Mart')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Nav Pills -->
    <div class="nav-align-top mb-6">
      <ul class="nav nav-pills flex-column flex-md-row gap-md-0 gap-2">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('pages-profile-user') }}"><i class="icon-base bx bx-user icon-sm me-1_5"></i>{{ __('My Profile') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('pages-account-settings-account') }}"><i class="icon-base bx bx-cog icon-sm me-1_5"></i>{{ __('Account Settings') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('pages-account-settings-security') }}"><i class="icon-base bx bx-lock-alt icon-sm me-1_5"></i>{{ __('Security & Password') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="javascript:void(0);"><i class="icon-base bx bx-credit-card icon-sm me-1_5"></i>{{ __('Billing & Plans') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('pages-account-settings-notifications') }}"><i class="icon-base bx bx-bell icon-sm me-1_5"></i>{{ __('Notifications') }}</a>
        </li>
      </ul>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Current Plan & Usage Row -->
    <div class="row g-6 mb-6">
      <!-- Current Plan Card -->
      <div class="col-lg-6 col-12">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
              <i class="bx bx-crown text-warning fs-4"></i>
              <span>{{ __('Current Store Subscription') }}</span>
            </h5>
            @if($currentSubscription)
              @if($currentSubscription->status === 'active')
                <span class="badge bg-success px-3 py-1">{{ __('Active') }}</span>
              @elseif($currentSubscription->status === 'trialing')
                <span class="badge bg-info px-3 py-1">{{ __('Trial Period') }}</span>
              @elseif($currentSubscription->status === 'canceled')
                <span class="badge bg-danger px-3 py-1">{{ __('Canceled') }}</span>
              @else
                <span class="badge bg-warning px-3 py-1">{{ ucfirst($currentSubscription->status) }}</span>
              @endif
            @endif
          </div>
          <div class="card-body pt-5 d-flex flex-column justify-content-between">
            @if($currentSubscription && $currentSubscription->plan)
            <div>
              <div class="d-flex align-items-baseline gap-2 mb-2">
                <h3 class="mb-0 fw-bold text-primary">{{ $currentSubscription->plan->name }}</h3>
                <span class="text-muted fs-5">/ ${{ number_format($currentSubscription->plan->price, 2) }} {{ $currentSubscription->plan->currency ?: 'USD' }}</span>
              </div>
              <p class="text-muted mb-4">{{ $currentSubscription->plan->description }}</p>

              <div class="bg-label-primary p-4 rounded mb-4">
                <div class="row g-3">
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">{{ __('Billing Cycle:') }}</span>
                    <span class="fw-semibold text-heading">{{ $currentSubscription->plan->billing_cycle_days ?: 30 }} {{ __('Days') }}</span>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">{{ __('Renewal / Expiry Date:') }}</span>
                    <span class="fw-semibold text-heading">
                      {{ $currentSubscription->current_period_end ? $currentSubscription->current_period_end->format('M d, Y') : __('N/A') }}
                    </span>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">{{ __('Active Branch:') }}</span>
                    <span class="fw-semibold text-heading">{{ $branch ? $branch->name : __('Main Flagship Store') }}</span>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">{{ __('Payment Status:') }}</span>
                    <span class="badge bg-label-success">{{ __('Auto-Renew Active') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-3 flex-wrap pt-2">
              <a href="#available-plans" class="btn btn-primary shadow-sm">
                <i class="bx bx-up-arrow-alt me-1"></i>{{ __('Upgrade Plan') }}
              </a>
              @if($currentSubscription->status === 'canceled')
                <form action="{{ route('app-saas-resume') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-success">
                    <i class="bx bx-refresh me-1"></i>{{ __('Resume Subscription') }}
                  </button>
                </form>
              @else
                <button type="button" class="btn btn-label-danger" data-bs-toggle="modal" data-bs-target="#modalCancelSubscription">
                  <i class="bx bx-x me-1"></i>{{ __('Cancel Subscription') }}
                </button>
              @endif
            </div>
            @else
            <div class="text-center py-6">
              <i class="bx bx-store-alt fs-1 text-warning mb-3"></i>
              <h5>{{ __('No Active Plan Selected') }}</h5>
              <p class="text-muted mb-4">{{ __('Select a plan below to activate multi-branch operations and advanced commerce tools.') }}</p>
              <a href="#available-plans" class="btn btn-primary">{{ __('Explore Available Plans') }}</a>
            </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Real-Time Resource Usage Card -->
      <div class="col-lg-6 col-12">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
              <i class="bx bx-bar-chart-alt-2 text-primary fs-4"></i>
              <span>{{ __('Store Usage & Plan Limits') }}</span>
            </h5>
            <span class="badge bg-label-primary">{{ __('Real Database Metrics') }}</span>
          </div>
          <div class="card-body pt-5">
            <!-- Products Limit -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-medium text-heading d-flex align-items-center gap-2">
                  <i class="bx bx-box text-primary"></i> {{ __('Catalog Products') }}
                </span>
                <span class="fw-bold text-heading">
                  {{ $usage['products']['count'] }} / {{ $usage['products']['is_unlimited'] ? __('Unlimited') : $usage['products']['limit'] }}
                </span>
              </div>
              <div class="progress rounded" style="height: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $usage['products']['percentage'] }}%;" aria-valuenow="{{ $usage['products']['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="text-muted">{{ __('Calculated from active inventory items in your store.') }}</small>
            </div>

            <!-- Staff Accounts Limit -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-medium text-heading d-flex align-items-center gap-2">
                  <i class="bx bx-user-check text-info"></i> {{ __('Staff & Cashier Accounts') }}
                </span>
                <span class="fw-bold text-heading">
                  {{ $usage['staff']['count'] }} / {{ $usage['staff']['is_unlimited'] ? __('Unlimited') : $usage['staff']['limit'] }}
                </span>
              </div>
              <div class="progress rounded" style="height: 10px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $usage['staff']['percentage'] }}%;" aria-valuenow="{{ $usage['staff']['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="text-muted">{{ __('Assigned branch members and managers.') }}</small>
            </div>

            <!-- Orders Count -->
            <div class="row g-3 mt-2">
              <div class="col-6">
                <div class="p-3 bg-light rounded text-center">
                  <span class="text-muted d-block small mb-1">{{ __('Total Orders Processed') }}</span>
                  <h4 class="mb-0 fw-bold text-heading">{{ $usage['orders']['count'] }}</h4>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 bg-light rounded text-center">
                  <span class="text-muted d-block small mb-1">{{ __('Total Store Branches') }}</span>
                  <h4 class="mb-0 fw-bold text-heading">{{ $usage['branches']['count'] }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Available Plans Section -->
    <div class="card mb-6 shadow-sm border-0" id="available-plans">
      <div class="card-header border-bottom">
        <h4 class="card-title mb-1">{{ __('Available SaaS Plans') }}</h4>
        <p class="text-muted mb-0">{{ __('Upgrade or switch your subscription tier anytime. Plan changes apply instantly.') }}</p>
      </div>
      <div class="card-body pt-6">
        <div class="row g-6">
          @foreach($plans as $plan)
          @php
            $isCurrent = $currentSubscription && $currentSubscription->subscription_plan_id == $plan->id && $currentSubscription->isActive();
            $pFeatures = $plan->features ?? [];
          @endphp
          <div class="col-lg-4 col-md-6 col-12">
            <div class="card h-100 border @if($isCurrent) border-primary shadow @else border-light @endif position-relative">
              @if($isCurrent)
              <div class="position-absolute top-0 end-0 mt-3 me-3">
                <span class="badge bg-primary px-3 py-1">{{ __('Current Plan') }}</span>
              </div>
              @endif
              <div class="card-body p-5 d-flex flex-column justify-content-between text-center">
                <div>
                  <h4 class="fw-bold mb-2">{{ $plan->name }}</h4>
                  <p class="text-muted small mb-4">{{ $plan->description }}</p>
                  
                  <div class="my-4">
                    <h2 class="display-6 fw-bold text-primary mb-0">${{ number_format($plan->price, 2) }}</h2>
                    <span class="text-muted small">/ {{ __('month') }} ({{ $plan->billing_cycle_days ?: 30 }} {{ __('days') }})</span>
                  </div>

                  <ul class="list-unstyled text-start mb-5 ps-3">
                    <li class="mb-3 d-flex align-items-center">
                      <i class="bx bx-check-circle text-primary fs-5 me-2"></i>
                      <span>{{ ($pFeatures['products_limit'] ?? 100) == -1 ? __('Unlimited') : ($pFeatures['products_limit'] ?? 100) }} {{ __('Products') }}</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                      <i class="bx bx-check-circle text-primary fs-5 me-2"></i>
                      <span>{{ ($pFeatures['staff_accounts'] ?? 2) == -1 ? __('Unlimited') : ($pFeatures['staff_accounts'] ?? 2) }} {{ __('Staff Accounts') }}</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                      @if(!empty($pFeatures['custom_domain']))
                        <i class="bx bx-check-circle text-primary fs-5 me-2"></i>
                        <span>{{ __('Custom Store Domain') }}</span>
                      @else
                        <i class="bx bx-x text-muted fs-5 me-2"></i>
                        <span class="text-muted">{{ __('Custom Store Domain') }}</span>
                      @endif
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                      <i class="bx bx-check-circle text-primary fs-5 me-2"></i>
                      <span>{{ __('Advanced Reports & CSV Export') }}</span>
                    </li>
                    <li class="d-flex align-items-center">
                      <i class="bx bx-check-circle text-primary fs-5 me-2"></i>
                      <span>{{ __('AI Copilot & Smart Tools') }}</span>
                    </li>
                  </ul>
                </div>

                <div>
                  @if($isCurrent)
                    <button class="btn btn-outline-primary w-100 disabled py-2 fw-semibold">{{ __('Current Active Plan') }}</button>
                  @else
                    <button type="button" class="btn btn-primary w-100 py-2 shadow-sm btn-select-plan" data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}" data-plan-price="{{ number_format($plan->price, 2) }}">
                      <i class="bx bx-check me-1"></i>{{ __('Select :plan', ['plan' => $plan->name]) }}
                    </button>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Payment Gateways Status Card -->
    <div class="card mb-6 shadow-sm border-0">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="bx bx-credit-card text-success fs-4"></i>
          <span>{{ __('Configured Payment Gateways') }}</span>
        </h5>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('app-ecommerce-settings-payments') }}" class="btn btn-sm btn-outline-primary">
          <i class="bx bx-slider me-1"></i>{{ __('Payment Settings') }}
        </a>
        @endif
      </div>
      <div class="card-body pt-5">
        <div class="row g-4">
          <!-- Stripe -->
          <div class="col-md-6">
            <div class="p-4 border rounded d-flex justify-content-between align-items-center @if($stripeConfigured) bg-label-primary border-primary @else bg-light @endif">
              <div class="d-flex align-items-center gap-3">
                <i class="bx bxl-stripe fs-1 text-primary"></i>
                <div>
                  <h6 class="mb-0 fw-bold">Stripe Credit Cards</h6>
                  <small class="text-muted">{{ $stripeConfigured ? __('Live Stripe API Connected') : __('Sandbox / Mock Provider Mode') }}</small>
                </div>
              </div>
              <span class="badge @if($stripeConfigured) bg-success @else bg-label-secondary @endif">
                {{ $stripeConfigured ? __('Configured') : __('Sandbox Ready') }}
              </span>
            </div>
          </div>

          <!-- PayPal -->
          <div class="col-md-6">
            <div class="p-4 border rounded d-flex justify-content-between align-items-center @if($paypalConfigured) bg-label-info border-info @else bg-light @endif">
              <div class="d-flex align-items-center gap-3">
                <i class="bx bxl-paypal fs-1 text-info"></i>
                <div>
                  <h6 class="mb-0 fw-bold">PayPal Express</h6>
                  <small class="text-muted">{{ $paypalConfigured ? __('PayPal Client API Active') : __('Sandbox / Mock Provider Mode') }}</small>
                </div>
              </div>
              <span class="badge @if($paypalConfigured) bg-success @else bg-label-secondary @endif">
                {{ $paypalConfigured ? __('Configured') : __('Sandbox Ready') }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Billing History & Invoices Table -->
    <div class="card mb-6 shadow-sm border-0">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="bx bx-receipt text-info fs-4"></i>
          <span>{{ __('Billing History & Invoices') }}</span>
        </h5>
        <span class="badge bg-label-secondary">{{ $invoices->count() }} {{ __('Invoices') }}</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Invoice #') }}</th>
              <th>{{ __('Plan') }}</th>
              <th>{{ __('Billing Period') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Payment Method') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-center">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
            <tr>
              <td>
                <span class="fw-bold font-monospace text-primary">#{{ $inv->invoice_number }}</span>
              </td>
              <td>
                <span class="fw-semibold text-heading">{{ $inv->plan_name ?: 'Subscription Plan' }}</span>
              </td>
              <td>
                <small class="text-muted">
                  {{ $inv->billing_period_start ? $inv->billing_period_start->format('M d, Y') : '—' }} to {{ $inv->billing_period_end ? $inv->billing_period_end->format('M d, Y') : '—' }}
                </small>
              </td>
              <td>
                <span class="fw-bold">${{ number_format($inv->amount, 2) }} {{ $inv->currency }}</span>
              </td>
              <td>
                <span class="badge bg-label-secondary"><i class="bx bx-credit-card me-1"></i>{{ $inv->payment_method ?: 'Credit Card' }}</span>
              </td>
              <td>
                @if($inv->status === 'paid')
                  <span class="badge bg-success">{{ __('Paid') }}</span>
                @elseif($inv->status === 'pending')
                  <span class="badge bg-warning">{{ __('Pending') }}</span>
                @elseif($inv->status === 'failed')
                  <span class="badge bg-danger">{{ __('Failed') }}</span>
                @else
                  <span class="badge bg-secondary">{{ ucfirst($inv->status) }}</span>
                @endif
              </td>
              <td class="text-center">
                <a href="{{ route('billing-invoice-preview', $inv->id) }}" target="_blank" class="btn btn-sm btn-label-primary">
                  <i class="bx bx-printer me-1"></i>{{ __('Invoice') }}
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bx bx-receipt fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-0">{{ __('No billing history or invoices found for this branch.') }}</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Confirm Plan Upgrade / Change -->
<div class="modal fade" id="modalPlanSubscribe" tabindex="-1" aria-labelledby="modalPlanSubscribeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header border-bottom">
        <h5 class="modal-title d-flex align-items-center gap-2" id="modalPlanSubscribeLabel">
          <i class="bx bx-crown text-primary fs-4"></i>
          <span>{{ __('Confirm Subscription Plan') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formSubscribePlan" method="POST" action="{{ route('app-saas-subscribe') }}">
        @csrf
        <input type="hidden" name="plan_id" id="modal_plan_id" value="" />
        <div class="modal-body p-4">
          <div class="p-4 bg-label-primary rounded mb-4 text-center">
            <h4 class="mb-1 text-primary fw-bold" id="modal_plan_name_display">Plan Name</h4>
            <h2 class="display-6 fw-bold mb-0 text-heading" id="modal_plan_price_display">$0.00</h2>
            <small class="text-muted">{{ __('Billed monthly with instant access to all included features.') }}</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Payment Method') }}</label>
            <select name="payment_method" class="form-select">
              <option value="Stripe Credit Card (Live / Sandbox)">{{ __('Credit Card (Stripe)') }}</option>
              <option value="PayPal Express">{{ __('PayPal Express Checkout') }}</option>
              <option value="Direct Merchant Billing">{{ __('Direct Merchant Store Billing') }}</option>
            </select>
          </div>

          <div class="text-muted small">
            <i class="bx bx-lock-alt text-success me-1"></i>
            {{ __('Your transaction is encrypted. Billing is calculated server-side and recorded in your invoices.') }}
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary" id="btnConfirmSubscribe">
            <i class="bx bx-check me-1"></i>{{ __('Confirm & Activate Plan') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Cancel Subscription -->
<div class="modal fade" id="modalCancelSubscription" tabindex="-1" aria-labelledby="modalCancelSubscriptionLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header border-bottom">
        <h5 class="modal-title text-danger d-flex align-items-center gap-2" id="modalCancelSubscriptionLabel">
          <i class="bx bx-error-circle fs-4"></i>
          <span>{{ __('Cancel Subscription?') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('app-saas-cancel') }}">
        @csrf
        <div class="modal-body p-4">
          <p class="mb-3">
            {{ __('Are you sure you want to cancel your store subscription? Your current plan will remain active until the end of the current billing cycle:') }}
          </p>
          <div class="bg-label-warning p-3 rounded mb-3">
            <strong>{{ __('Active Period End:') }}</strong> {{ $currentSubscription && $currentSubscription->current_period_end ? $currentSubscription->current_period_end->format('M d, Y') : __('End of cycle') }}
          </div>
          <p class="text-muted small mb-0">{{ __('You can resume your subscription anytime before expiration.') }}</p>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Keep Subscription') }}</button>
          <button type="submit" class="btn btn-danger">{{ __('Yes, Cancel Subscription') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const planButtons = document.querySelectorAll('.btn-select-plan');
    const modalPlanId = document.getElementById('modal_plan_id');
    const modalPlanName = document.getElementById('modal_plan_name_display');
    const modalPlanPrice = document.getElementById('modal_plan_price_display');
    const modalElement = document.getElementById('modalPlanSubscribe');
    let modalInstance = null;

    if (modalElement && typeof bootstrap !== 'undefined') {
      modalInstance = new bootstrap.Modal(modalElement);
    }

    planButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-plan-id');
        const name = this.getAttribute('data-plan-name');
        const price = this.getAttribute('data-plan-price');

        if (modalPlanId) modalPlanId.value = id;
        if (modalPlanName) modalPlanName.innerText = name;
        if (modalPlanPrice) modalPlanPrice.innerText = '$' + price;

        if (modalInstance) {
          modalInstance.show();
        }
      });
    });
  });
</script>
@endsection
