@extends('layouts/layoutMaster')

@section('title', 'SaaS Billing')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Current Subscription</h5>
      </div>
      <div class="card-body pt-6">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($currentSubscription && $currentSubscription->isActive())
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="bg-label-primary p-4 rounded">
              <h4 class="mb-2">{{ $currentSubscription->plan->name }}</h4>
              <p class="mb-0">Your plan is currently <strong>{{ ucfirst($currentSubscription->status) }}</strong>.</p>
              <p>Next billing date: <strong>{{ $currentSubscription->current_period_end->format('M d, Y') }}</strong></p>
              
              <form action="{{ route('app-saas-cancel') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-label-danger" onclick="return confirm('Are you sure you want to cancel? You will lose access at the end of the billing period.')">Cancel Subscription</button>
              </form>
            </div>
          </div>
        </div>
        @else
        <div class="alert alert-warning mb-4">
          <h5 class="alert-heading mb-1">No Active Subscription</h5>
          <span>Please select a plan below to activate your store and gain access to the dashboard.</span>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 mt-4">
    <h4 class="mb-4">Available Plans</h4>
    <div class="row">
      @foreach($plans as $plan)
      <div class="col-md-4 mb-4">
        <div class="card h-100 border @if($currentSubscription && $currentSubscription->subscription_plan_id == $plan->id) border-primary shadow-sm @endif">
          <div class="card-body text-center">
            <h3 class="card-title mb-2">{{ $plan->name }}</h3>
            <p class="text-muted">{{ $plan->description }}</p>
            <div class="my-4">
              <h2 class="display-5 text-primary mb-0">${{ number_format($plan->price, 2) }}</h2>
              <span class="text-muted">/ month</span>
            </div>
            
            <ul class="list-unstyled text-start mb-4">
              <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> {{ $plan->features['products_limit'] == -1 ? 'Unlimited' : $plan->features['products_limit'] }} Products</li>
              <li class="mb-2"><i class="bx bx-check text-primary me-2"></i> {{ $plan->features['staff_accounts'] == -1 ? 'Unlimited' : $plan->features['staff_accounts'] }} Staff Accounts</li>
              <li class="mb-2">
                @if($plan->features['custom_domain'])
                  <i class="bx bx-check text-primary me-2"></i> Custom Domain
                @else
                  <i class="bx bx-x text-danger me-2"></i> Custom Domain
                @endif
              </li>
            </ul>

            @if($currentSubscription && $currentSubscription->subscription_plan_id == $plan->id && $currentSubscription->isActive())
              <button class="btn btn-outline-primary w-100 disabled">Current Plan</button>
            @else
              <button type="button" class="btn btn-primary w-100 btn-subscribe" data-plan-id="{{ $plan->id }}">Subscribe Now</button>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn-subscribe');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const planId = this.getAttribute('data-plan-id');
            const originalText = this.innerHTML;
            this.innerHTML = 'Processing...';
            this.disabled = true;

            // Mock Stripe Payment Flow via AJAX
            fetch('{{ route("app-saas-subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    plan_id: planId,
                    payment_method_id: 'pm_card_visa_mock' // Mocking Stripe PM
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Subscription Activated',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        alert(data.message);
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Subscription Failed',
                            text: data.error || 'Unable to process subscription.'
                        });
                    } else {
                        alert('Error: ' + data.error);
                    }
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });
});
</script>
@endsection
