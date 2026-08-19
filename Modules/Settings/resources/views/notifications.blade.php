@extends('layouts/layoutMaster')

@section('title', __('Notification Matrix Settings') . ' — AK-Mart')

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
            <i class="bx bx-bell text-info fs-4"></i>
            <span>{{ __('Omnichannel Notification Center Matrix') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Toggle communication channels (In-App, Email, WhatsApp) for each system event type.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'notifications') }}">
          @csrf

          @php
            $events = [
              ['key' => 'order_placed', 'name' => 'New Order Placed', 'desc' => 'Triggered when a customer completes checkout.'],
              ['key' => 'order_paid', 'name' => 'Payment Succeeded', 'desc' => 'Triggered when payment gateway confirms charge.'],
              ['key' => 'order_shipped', 'name' => 'Order Dispatched / Shipped', 'desc' => 'Triggered when tracking number is assigned.'],
              ['key' => 'order_delivered', 'name' => 'Order Delivered', 'desc' => 'Triggered upon successful courier delivery.'],
              ['key' => 'order_cancelled', 'name' => 'Order Cancelled', 'desc' => 'Triggered when order is voided or cancelled.'],
              ['key' => 'order_refund', 'name' => 'Refund Processed', 'desc' => 'Triggered when money is refunded to buyer.'],
              ['key' => 'inventory_low', 'name' => 'Low Stock Warning', 'desc' => 'Triggered when item stock drops below safety limit.'],
              ['key' => 'customer_signup', 'name' => 'New Customer Registration', 'desc' => 'Triggered when a new user signs up.'],
              ['key' => 'security_alert', 'name' => 'Security & Login Alerts', 'desc' => 'Triggered on suspicious login attempts or profile changes.'],
            ];
          @endphp

          <div class="table-responsive border rounded mb-5">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 45%;">{{ __('System Event Type') }}</th>
                  <th class="text-center" style="width: 18%;">
                    <i class="bx bx-bell text-info me-1"></i>{{ __('In-App') }}
                  </th>
                  <th class="text-center" style="width: 18%;">
                    <i class="bx bx-envelope text-primary me-1"></i>{{ __('Email') }}
                  </th>
                  <th class="text-center" style="width: 18%;">
                    <i class="bx bxl-whatsapp text-success me-1"></i>{{ __('WhatsApp') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                @foreach($events as $ev)
                <tr>
                  <td>
                    <h6 class="mb-0 fw-semibold text-heading">{{ __($ev['name']) }}</h6>
                    <small class="text-muted">{{ __($ev['desc']) }}</small>
                  </td>
                  <td class="text-center align-middle">
                    <div class="form-check form-switch d-inline-block">
                      <input type="hidden" name="notify_{{ $ev['key'] }}_app" value="0">
                      <input class="form-check-input" type="checkbox" name="notify_{{ $ev['key'] }}_app" value="1" {{ ($settings["notify_{$ev['key']}_app"] ?? '1') === '1' ? 'checked' : '' }}>
                    </div>
                  </td>
                  <td class="text-center align-middle">
                    <div class="form-check form-switch d-inline-block">
                      <input type="hidden" name="notify_{{ $ev['key'] }}_email" value="0">
                      <input class="form-check-input" type="checkbox" name="notify_{{ $ev['key'] }}_email" value="1" {{ ($settings["notify_{$ev['key']}_email"] ?? '1') === '1' ? 'checked' : '' }}>
                    </div>
                  </td>
                  <td class="text-center align-middle">
                    <div class="form-check form-switch d-inline-block">
                      <input type="hidden" name="notify_{{ $ev['key'] }}_whatsapp" value="0">
                      <input class="form-check-input" type="checkbox" name="notify_{{ $ev['key'] }}_whatsapp" value="1" {{ ($settings["notify_{$ev['key']}_whatsapp"] ?? '1') === '1' ? 'checked' : '' }}>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Notification Matrix') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
