@extends('layouts/layoutMaster')

@section('title', __('Store Details') . ' - ' . __('Store Settings'))

@section('vendor-style')
@vite('resources/assets/vendor/libs/select2/select2.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/app-ecommerce-settings.js')
@endsection

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
<form method="POST" action="{{ route('app-ecommerce-settings-details-save') }}">
@csrf
<div class="row g-6">
  <!-- Navigation -->
  <div class="col-12 col-lg-4">
    @include('content.apps._settings-sidebar')
  </div>
  <!-- /Navigation -->

  <!-- Options -->
  <div class="col-12 col-lg-8 pt-6 pt-lg-0">
    <div class="tab-content p-0">
      <!-- Store Details Tab -->
      <div class="tab-pane fade show active" id="store_details" role="tabpanel">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title m-0">{{ __('Profile') }}</h5>
          </div>
          <div class="card-body">
            <div class="row mb-6 g-6">
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="ecommerce-settings-details-name">{{ __('Store Name') }}</label>
                <input type="text" class="form-control" id="ecommerce-settings-details-name" placeholder="{{ __('Store Name') }}"
                  name="store_name" value="{{ $settings['store_name'] ?? '' }}" aria-label="Store Name" />
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="ecommerce-settings-details-phone">{{ __('Phone') }}</label> 
                <input type="tel" class="form-control phone-mask" id="ecommerce-settings-details-phone" placeholder="+(123) 456-7890" name="store_phone" value="{{ $settings['store_phone'] ?? '' }}" aria-label="phone" />
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="ecommerce-settings-details-email">{{ __('Store contact email') }}</label> 
                <input type="email" class="form-control" id="ecommerce-settings-details-email" placeholder="store@example.com" name="store_email" value="{{ $settings['store_email'] ?? '' }}" aria-label="email" />
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="ecommerce-settings-sender-email">{{ __('Sender email') }}</label> 
                <input type="email" class="form-control" id="ecommerce-settings-sender-email" placeholder="noreply@example.com" name="sender_email" value="{{ $settings['sender_email'] ?? '' }}" aria-label="sender email" />
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="default_branch">{{ __('Default Branch') }}</label>
                <select id="default_branch" name="default_branch" class="select2 form-select">
                  <option value="">{{ __('Select Branch') }}</option>
                  @foreach($branches as $branch)
                  <option value="{{ $branch->id }}" {{ (isset($settings['default_branch']) && $settings['default_branch'] == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title m-0">{{ __('Billing information') }}</h5>
          </div>
          <div class="card-body">
            <div class="row g-6">
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="business-name">{{ __('Legal business name') }}</label>
                <input type="text" id="business-name" name="business_name" class="form-control" placeholder="{{ __('Legal business name') }}" value="{{ $settings['business_name'] ?? '' }}" />
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="country_region">{{ __('Country/region') }}</label>
                <select id="country_region" class="select2 form-select" data-placeholder="United States">
                  <option value="">United States</option>
                  <option value="Australia">Australia</option>
                  <option value="Canada">Canada</option>
                  <option value="France">France</option>
                  <option value="Germany">Germany</option>
                  <option value="India">India</option>
                  <option value="United Arab Emirates">United Arab Emirates</option>
                  <option value="United Kingdom">United Kingdom</option>
                  <option value="United States">United States</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="bill_address">{{ __('Address') }}</label>
                <input type="text" id="bill_address" name="address" class="form-control" placeholder="{{ __('Address') }}" value="{{ $settings['address'] ?? '' }}" />
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="apa_suite">{{ __('Apartment, suite, etc.') }}</label>
                <input type="text" id="apa_suite" class="form-control" placeholder="{{ __('Apartment, suite, etc.') }}" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="bill_city">{{ __('City') }}</label>
                <input type="text" id="bill_city" name="city" class="form-control" placeholder="{{ __('City') }}" value="{{ $settings['city'] ?? '' }}" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="bill_state">{{ __('State') }}</label>
                <input type="text" id="bill_state" name="state" class="form-control" placeholder="{{ __('State') }}" value="{{ $settings['state'] ?? '' }}" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="bill_pincode">{{ __('PIN code') }}</label>
                <input type="number" id="bill_pincode" name="pincode" class="form-control" placeholder="{{ __('PIN code') }}" min="0"
                  max="999999" value="{{ $settings['pincode'] ?? '' }}" />
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-6">
          <div class="card-header">
            <div class="card-title mb-0">
              <h5 class="m-0">{{ __('Time zone and units of measurement') }}</h5>
              <p class="my-0 card-subtitle">{{ __('Used to calculate product prices, shipping weighs, and order times.') }}</p>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-6">
              <div class="col-12">
                <label for="timeZones" class="form-label mb-1">{{ __('Time zone') }}</label>
                <select id="timeZones" class="select2 form-select">
                  <option value="">(GMT+00:00) UTC</option>
                  <option value="-5">(GMT-05:00) Eastern Time (US & Canada)</option>
                  <option value="+4">(GMT+04:00) Dubai, Abu Dhabi</option>
                  <option value="+5.5">(GMT+05:30) India Standard Time</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label for="unitSystemDropdown" class="form-label mb-1">{{ __('Unit system') }}</label>
                <select id="unitSystemDropdown" class="select2 form-select">
                  <option value="metric">Metric (kg, m)</option>
                  <option value="imperial">Imperial (lb, ft)</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label for="weightUnits" class="form-label mb-1">{{ __('Default weight unit') }}</label>
                <select id="weightUnits" class="select2 form-select">
                  <option value="kg">Kilograms (kg)</option>
                  <option value="g">Grams (g)</option>
                  <option value="lb">Pounds (lb)</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-6">
          <div class="card-header">
            <div class="card-title mb-0">
              <h5 class="m-0">{{ __('Order id format') }}</h5>
              <p class="my-0 card-subtitle">{{ __('Shown on the Orders page, customer pages, and customer order notifications') }}</p>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-6">
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="ecommerce-settings-details-prefix">{{ __('Prefix') }}</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text text-body-secondary">#</span>
                  <input type="text" class="form-control" id="ecommerce-settings-details-prefix" name="order_prefix"
                    aria-label="Prefix" value="{{ $settings['order_prefix'] ?? '' }}" />
                </div>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label mb-1" for="ecommerce-settings-sender-suffix">{{ __('Suffix') }}</label> 
                <input type="text" class="form-control" id="ecommerce-settings-sender-suffix" name="order_suffix" aria-label="Suffix" value="{{ $settings['order_suffix'] ?? '' }}" />
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-4">
          <button type="reset" class="btn btn-label-secondary">{{ __('Discard') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /Options-->
</div>
</form>
@endsection