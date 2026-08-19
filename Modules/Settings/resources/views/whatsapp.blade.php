@extends('layouts/layoutMaster')

@section('title', __('WhatsApp Management Hub') . ' — AK-Mart')

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

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-x-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- WhatsApp Cloud API Configuration -->
    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bxl-whatsapp text-success fs-4"></i>
            <span>{{ __('WhatsApp Business Cloud API Configuration') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Connect Meta WhatsApp Business API for instant transactional notifications, delivery alerts, and order tracking.') }}</p>
        </div>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTestWhatsApp">
          <i class="bx bx-paper-plane me-1"></i>{{ __('Test WhatsApp Message') }}
        </button>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'whatsapp') }}">
          @csrf

          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-cog text-primary"></i>
            <span>{{ __('API Credentials & Phone Identity') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Provider') }}</label>
              <select name="whatsapp_provider" class="form-select">
                <option value="meta" {{ ($settings['whatsapp_provider'] ?? 'meta') === 'meta' ? 'selected' : '' }}>Meta WhatsApp Cloud API (Official)</option>
                <option value="twilio" {{ ($settings['whatsapp_provider'] ?? '') === 'twilio' ? 'selected' : '' }}>Twilio WhatsApp</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('WhatsApp Business Account ID') }}</label>
              <input type="text" name="whatsapp_business_account_id" class="form-control" value="{{ $settings['whatsapp_business_account_id'] ?? '' }}" placeholder="e.g. 1092837465" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Phone Number ID') }}</label>
              <input type="text" name="whatsapp_phone_number_id" class="form-control" value="{{ $settings['whatsapp_phone_number_id'] ?? '' }}" placeholder="e.g. 1039485726" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Permanent Access Token (Encrypted)') }}</label>
              <input type="password" name="whatsapp_access_token" class="form-control" placeholder="EAAB••••••••••••••••••••••••" />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Webhook Verify Token') }}</label>
              <input type="text" name="whatsapp_verify_token" class="form-control" value="{{ $settings['whatsapp_verify_token'] ?? 'akmart_meta_webhook_secret' }}" />
            </div>

            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Webhook Endpoint URL (Provide to Meta Dashboard)') }}</label>
              <div class="input-group">
                <input type="text" class="form-control bg-light" readonly value="{{ url('api/whatsapp/webhook') }}" />
                <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ url('api/whatsapp/webhook') }}')">
                  <i class="bx bx-copy me-1"></i>{{ __('Copy') }}
                </button>
              </div>
            </div>
          </div>

          <!-- WhatsApp Automations -->
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-broadcast text-primary"></i>
            <span>{{ __('Automated WhatsApp Triggers') }}</span>
          </h6>
          <div class="list-group list-group-flush mb-5">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Order Confirmation Notification') }}</h6>
                <small class="text-muted">{{ __('Sends order breakdown & tracking link to customer WhatsApp when order is confirmed.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="whatsapp_notify_order_created" value="0">
                <input class="form-check-input" type="checkbox" name="whatsapp_notify_order_created" value="1" {{ ($settings['whatsapp_notify_order_created'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Order Shipped & Out-for-Delivery Alert') }}</h6>
                <small class="text-muted">{{ __('Sends courier name, driver phone and tracking URL upon dispatch.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="whatsapp_notify_order_shipped" value="0">
                <input class="form-check-input" type="checkbox" name="whatsapp_notify_order_shipped" value="1" {{ ($settings['whatsapp_notify_order_shipped'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
              <div>
                <h6 class="mb-0 fw-semibold">{{ __('Order Delivery Completion & Review Request') }}</h6>
                <small class="text-muted">{{ __('Congratulates customer on delivery and invites product star rating.') }}</small>
              </div>
              <div class="form-check form-switch">
                <input type="hidden" name="whatsapp_notify_order_delivered" value="0">
                <input class="form-check-input" type="checkbox" name="whatsapp_notify_order_delivered" value="1" {{ ($settings['whatsapp_notify_order_delivered'] ?? '1') === '1' ? 'checked' : '' }}>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save WhatsApp Settings') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Recent WhatsApp Activity Table -->
    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="bx bx-history text-success fs-4"></i>
          <span>{{ __('Recent WhatsApp Message Logs') }}</span>
        </h5>
        <span class="badge bg-label-success">{{ $communicationLogs->where('channel', 'whatsapp')->count() }} {{ __('Logged') }}</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Recipient') }}</th>
              <th>{{ __('Template / Purpose') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Timestamp') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($communicationLogs->where('channel', 'whatsapp') as $log)
            <tr>
              <td>
                <span class="fw-semibold text-heading font-monospace">{{ $log->recipient }}</span>
              </td>
              <td>
                <span class="badge bg-label-primary">{{ $log->template_code ?: 'Custom Notification' }}</span>
              </td>
              <td>
                @if($log->status === 'delivered' || $log->status === 'sent')
                  <span class="badge bg-success">{{ ucfirst($log->status) }}</span>
                @else
                  <span class="badge bg-danger">{{ ucfirst($log->status) }}</span>
                @endif
              </td>
              <td>
                <small class="text-muted">{{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}</small>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">
                {{ __('No recent WhatsApp dispatches found.') }}
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Test WhatsApp -->
<div class="modal fade" id="modalTestWhatsApp" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bx bxl-whatsapp text-success fs-4"></i>
          <span>{{ __('Dispatch Test WhatsApp Message') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('settings.whatsapp.test') }}">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Recipient Phone Number (with Country Code)') }} <span class="text-danger">*</span></label>
            <input type="text" name="test_phone" class="form-control" placeholder="+15550192834" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Test Message Content') }}</label>
            <textarea name="test_message" class="form-control" rows="3">Hello! This is a verified test dispatch from your AK-Mart WhatsApp Business Cloud API integration.</textarea>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-success">
            <i class="bx bx-send me-1"></i>{{ __('Send Test WhatsApp') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
