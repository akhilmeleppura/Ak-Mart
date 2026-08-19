@extends('layouts/layoutMaster')

@section('title', __('WhatsApp Business API Configuration') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bxl-whatsapp text-success me-2"></i> {{ __('Official WhatsApp Business Cloud API') }}</h4>
        <p class="text-muted small mb-0">{{ __('Configure Meta WhatsApp Cloud API credentials for automated order dispatch alerts and OTPs') }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border shadow-sm rounded-4 p-4">
            <form action="{{ route('app-whatsapp-config-save') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('WhatsApp Phone Number ID') }}</label>
                        <input type="text" name="whatsapp_phone_number_id" class="form-control" value="{{ $phoneId }}" required>
                        <small class="text-muted">{{ __('Found in Meta Developers Dashboard under WhatsApp > API Setup') }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('WhatsApp Business Account ID (WABA ID)') }}</label>
                        <input type="text" name="whatsapp_business_account_id" class="form-control" value="{{ $wabaId }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('System User Permanent Access Token') }}</label>
                    <input type="password" name="whatsapp_cloud_token" class="form-control font-monospace" value="{{ $token }}" placeholder="EAAB... (Leave as is to keep existing token)">
                    <small class="text-muted">{{ __('Generated via Meta Business Manager with whatsapp_business_messaging permission') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Webhook Verification Token') }}</label>
                    <input type="text" name="whatsapp_webhook_verify_token" class="form-control font-monospace" value="{{ $webhookToken }}" required>
                </div>

                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="whatsapp_auto_order_alerts" value="1" {{ $autoOrderAlerts == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold">{{ __('Auto-Send Instant WhatsApp Notifications for New Orders & Shipping') }}</label>
                    </div>
                    <small class="text-muted d-block mt-1">{{ __('Sends approved Meta WhatsApp notification template when order status changes to Confirmed / Shipped') }}</small>
                </div>

                <button type="submit" class="btn btn-success rounded-pill px-4">
                    <i class="bx bx-check-shield me-1"></i> {{ __('Save WhatsApp Cloud API Settings') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
