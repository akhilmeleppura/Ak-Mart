@extends('layouts/layoutMaster')

@section('title', 'eCommerce Settings Locations - Apps')

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
<form method="POST" action="{{ route('app-ecommerce-settings-locations-save') }}">
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
      <!-- Locations Tab -->
      <div class="tab-pane fade show active" id="locations" role="tabpanel">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title m-0">Location Name</h5>
          </div>
          <div class="card-body">
            <div class="col-12 mb-6">
              <label for="location_name" class="form-label mb-1">Location Name</label>
              <input class="form-control" type="text" name="location_name" id="location_name"
                value="{{ $settings['location_name'] ?? '' }}" placeholder="Shop location" />
            </div>
            <div class="form-check mb-6 ms-2">
              <input class="form-check-input" type="checkbox" name="def_location" value="1" id="def_location" {{ (!isset($settings['def_location']) || $settings['def_location'] == '1') ? 'checked' : '' }} />
              <label class="form-check-label" for="def_location"> Fulfill online orders from this location </label>
            </div>
            <div class="alert row alert-info mb-0 h6" role="alert">
              <span class="col-3 alert-icon me-0 rounded-circle px-0"><i
                  class="icon-base bx bx-info-circle icon-18px"></i></span>
              <div class="col text-wrap ps-3 pe-0">This is your default location. To change whether you fulfill online
                orders from this location, select another default location first.</div>
            </div>
          </div>
        </div>
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="card-title m-0">Address</h5>
          </div>
          <div class="card-body">
            <div class="row g-6">
              <div class="col-12">
                <label class="form-label mb-1" for="country_region">Country/region</label>
                @php $selectedCountry = $settings['location_country'] ?? 'United States'; @endphp
                <select id="country_region" name="location_country" class="select2 form-select" data-placeholder="United States">
                  @foreach(['United States', 'Australia', 'Bangladesh', 'Belarus', 'Brazil', 'Canada', 'China', 'France', 'Germany', 'India', 'Indonesia', 'Israel', 'Italy', 'Japan', 'Korea, Republic of', 'Mexico', 'Philippines', 'Russian Federation', 'South Africa', 'Thailand', 'Turkey', 'Ukraine', 'United Arab Emirates', 'United Kingdom'] as $country)
                    <option value="{{ $country }}" {{ $selectedCountry == $country ? 'selected' : '' }}>{{ $country }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="loc_address">Address</label>
                <input type="text" id="loc_address" name="location_address" class="form-control" value="{{ $settings['location_address'] ?? '' }}" placeholder="Address" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="loc_apa_suite">Apartment, suite, etc.</label>
                <input type="text" id="loc_apa_suite" name="location_apt" class="form-control" value="{{ $settings['location_apt'] ?? '' }}" placeholder="Apartment, suite, etc." />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-0" for="loc_phone">Phone</label>
                <input type="tel" class="form-control phone-mask" id="loc_phone" placeholder="Phone" name="location_phone" value="{{ $settings['location_phone'] ?? '' }}" aria-label="loc_phone" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="loc_city">City</label>
                <input type="text" id="loc_city" name="location_city" class="form-control" value="{{ $settings['location_city'] ?? '' }}" placeholder="City" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="loc_state">State</label>
                <input type="text" id="loc_state" name="location_state" class="form-control" value="{{ $settings['location_state'] ?? '' }}" placeholder="State" />
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mb-1" for="loc_pincode">PIN Code</label>
                <input type="number" id="loc_pincode" name="location_pincode" class="form-control" value="{{ $settings['location_pincode'] ?? '' }}" placeholder="PIN Code" min="0" max="999999" />
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end gap-4">
          <button type="reset" class="btn btn-label-secondary">Discard</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
    <!-- /Options-->
  </div>
</div>
</form>
@endsection
