@extends('layouts/layoutMaster')

@section('title', 'Payment Gateway Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Payment Gateway Connections</h5>
                <span class="text-muted small">Configure your own gateway to receive direct payments.</span>
            </div>
            <div class="card-body pt-6">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('app-vendor-payment-settings-save') }}" method="POST">
                    @csrf
                    
                    {{-- Stripe --}}
                    <div class="d-flex align-items-center mb-4">
                        <i class="bx bxl-stripe display-5 text-primary me-3"></i>
                        <h6 class="mb-0">Stripe Integration</h6>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Publishable Key</label>
                            <input type="text" name="stripe_key" class="form-control" value="{{ $settings['stripe_key'] ?? '' }}" placeholder="pk_test_...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secret Key</label>
                            <input type="password" name="stripe_secret" class="form-control" value="{{ $settings['stripe_secret'] ?? '' }}" placeholder="sk_test_...">
                        </div>
                    </div>

                    <hr class="my-6">

                    {{-- PayPal --}}
                    <div class="d-flex align-items-center mb-4">
                        <i class="bx bxl-paypal display-5 text-info me-3"></i>
                        <h6 class="mb-0">PayPal Integration</h6>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" name="paypal_client_id" class="form-control" value="{{ $settings['paypal_client_id'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Secret</label>
                            <input type="password" name="paypal_secret" class="form-control" value="{{ $settings['paypal_secret'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mode</label>
                            <select name="paypal_mode" class="form-select">
                                <option value="sandbox" {{ ($settings['paypal_mode'] ?? '') == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                <option value="live" {{ ($settings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>Live</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-6">

                    {{-- PhonePe --}}
                    <div class="d-flex align-items-center mb-4">
                        <i class="bx bx-mobile-alt display-5 text-warning me-3"></i>
                        <h6 class="mb-0">PhonePe (India) Integration</h6>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Merchant ID</label>
                            <input type="text" name="phonepe_merchant_id" class="form-control" value="{{ $settings['phonepe_merchant_id'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Salt Key</label>
                            <input type="password" name="phonepe_salt_key" class="form-control" value="{{ $settings['phonepe_salt_key'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Salt Index</label>
                            <input type="text" name="phonepe_salt_index" class="form-control" value="{{ $settings['phonepe_salt_index'] ?? '' }}" placeholder="1">
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-10">
                            <i class="bx bx-check me-1"></i> Update Gateways
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
