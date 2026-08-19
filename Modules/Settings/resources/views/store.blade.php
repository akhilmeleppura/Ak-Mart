@extends('layouts/layoutMaster')

@section('title', __('Store Details') . ' — AK-Mart')

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
            <i class="bx bx-store-alt text-primary fs-4"></i>
            <span>{{ __('Store Details & Business Profile') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Manage your public store profile, contact information, legal credentials, and time formats.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <form method="POST" action="{{ route('settings.section.save', 'store') }}" enctype="multipart/form-data">
          @csrf
          
          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bx bx-info-circle text-primary"></i>
            <span>{{ __('General Store Information') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Store Name') }} <span class="text-danger">*</span></label>
              <input type="text" name="store_name" class="form-control" value="{{ $settings['store_name'] ?? 'AK-Mart Store' }}" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Legal Business / Company Name') }}</label>
              <input type="text" name="business_name" class="form-control" value="{{ $settings['business_name'] ?? 'AK-Mart Global Enterprises LLC' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Public Store Email') }}</label>
              <input type="email" name="store_email" class="form-control" value="{{ $settings['store_email'] ?? 'support@ak-mart.com' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Customer Support Email') }}</label>
              <input type="email" name="sender_email" class="form-control" value="{{ $settings['sender_email'] ?? 'help@ak-mart.com' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Store Phone Number') }}</label>
              <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] ?? '+1 (555) 019-2834' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Customer Support WhatsApp Number') }}</label>
              <input type="text" name="whatsapp_number" class="form-control" value="{{ $settings['whatsapp_number'] ?? '+15550192834' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Website / Store URL') }}</label>
              <input type="url" name="store_url" class="form-control" value="{{ $settings['store_url'] ?? url('/') }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Tax / VAT / GST Registration Number') }}</label>
              <input type="text" name="tax_number" class="form-control" value="{{ $settings['tax_number'] ?? 'GSTIN32AAAAA0000A1Z5' }}" />
            </div>
          </div>

          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-map-pin text-primary"></i>
            <span>{{ __('Physical Store Address') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Street Address') }}</label>
              <input type="text" name="address" class="form-control" value="{{ $settings['address'] ?? '742 Broadway Ave, Suite 400' }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('City / Town') }}</label>
              <input type="text" name="city" class="form-control" value="{{ $settings['city'] ?? 'New York' }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('State / Region') }}</label>
              <input type="text" name="state" class="form-control" value="{{ $settings['state'] ?? 'NY' }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Postal / PIN Code') }}</label>
              <input type="text" name="pincode" class="form-control" value="{{ $settings['pincode'] ?? '10003' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Country') }}</label>
              <input type="text" name="country" class="form-control" value="{{ $settings['country'] ?? 'United States' }}" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Default Active Branch') }}</label>
              <select name="default_branch" class="form-select">
                @foreach($branches as $b)
                  <option value="{{ $b->id }}" {{ ($settings['default_branch'] ?? '') == $b->id ? 'selected' : '' }}>
                    {{ $b->name }} ({{ $b->code ?: 'BR-' . $b->id }})
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
            <i class="bx bx-time-five text-primary"></i>
            <span>{{ __('Timezone, Formats & Units') }}</span>
          </h6>
          <div class="row g-4 mb-5">
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Timezone') }}</label>
              <select name="timezone" class="form-select">
                <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC (GMT)</option>
                <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                <option value="Asia/Dubai" {{ ($settings['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                <option value="Europe/Paris" {{ ($settings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Date Format') }}</label>
              <select name="date_format" class="form-select">
                <option value="Y-m-d" {{ ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-08-19)</option>
                <option value="M d, Y" {{ ($settings['date_format'] ?? '') === 'M d, Y' ? 'selected' : '' }}>Mon DD, YYYY (Aug 19, 2026)</option>
                <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (19/08/2026)</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">{{ __('Weight Unit') }}</label>
              <select name="weight_unit" class="form-select">
                <option value="kg" {{ ($settings['weight_unit'] ?? 'kg') === 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                <option value="lb" {{ ($settings['weight_unit'] ?? '') === 'lb' ? 'selected' : '' }}>Pounds (lb)</option>
                <option value="g" {{ ($settings['weight_unit'] ?? '') === 'g' ? 'selected' : '' }}>Grams (g)</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Store Description / Footer Text') }}</label>
              <textarea name="footer_text" class="form-control" rows="2">{{ $settings['footer_text'] ?? '© 2026 AK-Mart. Enterprise E-Commerce & Mini-Mart Management Platform. All rights reserved.' }}</textarea>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3 border-top pt-4">
            <button type="submit" class="btn btn-primary shadow-sm px-4">
              <i class="bx bx-check me-1"></i>{{ __('Save Store Details') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
