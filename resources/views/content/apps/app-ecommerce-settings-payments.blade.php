@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'eCommerce Settings Payments - Apps')

@section('page-script')
@vite('resources/assets/js/app-ecommerce-settings.js')
@endsection

@section('content')
<div class="row g-6">
  <!-- Navigation -->
  <div class="col-12 col-lg-4">
    @include('content.apps._settings-sidebar')
  </div>
  <!-- /Navigation -->
  <!-- Options -->
  <div class="col-12 col-lg-8 pt-6 pt-lg-0">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    <form method="POST" action="{{ route('app-ecommerce-settings-payments-save') }}">
      @csrf
      <div class="tab-content p-0">
        <!-- Payments Tab -->
        <div class="tab-pane fade show active" id="payments" role="tabpanel">
          
          <!-- PayPal Configuration -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title m-0">PayPal Express Checkout</h5>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" name="paypal_enabled" value="1" id="paypal_enabled" {{ ($settings['paypal_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="paypal_enabled">Enable PayPal</label>
              </div>
            </div>
            <div class="card-body">
              <div class="row g-4">
                <div class="col-12 col-md-6">
                  <label class="form-label" for="paypal_email">PayPal Account Email</label>
                  <input type="email" class="form-control" id="paypal_email" name="paypal_email" value="{{ $settings['paypal_email'] ?? '' }}" placeholder="paypal@example.com">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label" for="paypal_client_id">Client ID</label>
                  <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}" placeholder="PayPal Client ID">
                </div>
              </div>
            </div>
          </div>

          <!-- Stripe Configuration -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title m-0">Stripe Credit Card Payments</h5>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" name="stripe_enabled" value="1" id="stripe_enabled" {{ ($settings['stripe_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="stripe_enabled">Enable Stripe</label>
              </div>
            </div>
            <div class="card-body">
              <div class="row g-4">
                <div class="col-12 col-md-6">
                  <label class="form-label" for="stripe_key">Stripe Publishable Key</label>
                  <input type="text" class="form-control" id="stripe_key" name="stripe_key" value="{{ $settings['stripe_key'] ?? '' }}" placeholder="pk_test_...">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label" for="stripe_secret">Stripe Secret Key</label>
                  <input type="password" class="form-control" id="stripe_secret" name="stripe_secret" value="{{ $settings['stripe_secret'] ?? '' }}" placeholder="sk_test_...">
                </div>
              </div>
            </div>
          </div>

          <!-- Manual / COD Payment Methods -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title m-0">Cash on Delivery (COD) & Manual Payments</h5>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" name="cod_enabled" value="1" id="cod_enabled" {{ ($settings['cod_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="cod_enabled">Enable COD</label>
              </div>
            </div>
            <div class="card-body">
              <div class="col-12">
                <label class="form-label" for="manual_payment_instruction">Payment Instructions for Customers</label>
                <textarea class="form-control" id="manual_payment_instruction" name="manual_payment_instruction" rows="3" placeholder="Provide instructions shown to customer at checkout">{{ $settings['manual_payment_instruction'] ?? '' }}</textarea>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-4">
            <button type="reset" class="btn btn-label-secondary">Discard</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!-- /Options-->
</div>

@include('_partials/_modals/modal-select-payment-providers')
@include('_partials/_modals/modal-select-payment-methods')

@endsection