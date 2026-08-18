@extends('layouts/layoutMaster')

@section('title', 'eCommerce Settings Checkout - Apps')

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
    <form method="POST" action="{{ route('app-ecommerce-settings-checkout-save') }}">
      @csrf
      <div class="tab-content p-0">
        <!-- Checkout Tab -->
        <div class="tab-pane fade show active" id="checkout" role="tabpanel">
          <div class="card mb-6">
            <div class="card-header">
              <div class="card-title m-0">
                <h5 class="m-0">Customer contact method</h5>
                <p class="my-0 card-subtitle">Select what contact method customers use to check out.</p>
              </div>
            </div>

            <div class="card-body">
              @php $contactMethod = $settings['contact_method'] ?? 'phone'; @endphp
              <div class="form-check my-2 ms-2">
                <input class="form-check-input" type="radio" name="contact_method" value="phone" id="contactPhone" {{ $contactMethod == 'phone' ? 'checked' : '' }} />
                <label class="form-check-label text-heading" for="contactPhone"> Phone number </label>
              </div>
              <div class="form-check mb-6 mt-4 ms-2">
                <input class="form-check-input" type="radio" name="contact_method" value="email" id="contactMail" {{ $contactMethod == 'email' ? 'checked' : '' }} />
                <label class="form-check-label text-heading" for="contactMail"> Email </label>
              </div>
              <div class="alert d-flex align-items-center alert-info mb-0 h6 flex-wrap gap-2 gap-sm-0" role="alert">
                <span class="alert-icon rounded-circle me-3">
                  <i class="icon-base bx bx-info-circle icon-18px"></i>
                </span>
                To send SMS updates, you need to install an SMS App.
              </div>
            </div>
          </div>

          <div class="card mb-6">
            <div class="card-header">
              <h5 class="card-title m-0">Customer information</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <p class="mb-0 fw-medium">Full name</p>
                @php $fullNameReq = $settings['full_name_requirement'] ?? 'last_name'; @endphp
                <div class="form-check my-2 ms-2">
                  <input class="form-check-input" type="radio" name="full_name_requirement" value="last_name" id="last_name" {{ $fullNameReq == 'last_name' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="last_name"> Only require last name </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="full_name_requirement" value="last_and_first_name" id="last_and_first_name" {{ $fullNameReq == 'last_and_first_name' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="last_and_first_name"> Require first and last name </label>
                </div>
              </div>

              <div class="mb-4">
                <p class="mb-0 fw-medium">Company name</p>
                @php $companyNameReq = $settings['company_name_requirement'] ?? 'dont_include'; @endphp
                <div class="form-check my-2 ms-2">
                  <input class="form-check-input" type="radio" name="company_name_requirement" value="dont_include" id="dont_include_company" {{ $companyNameReq == 'dont_include' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="dont_include_company"> Don't include name </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="company_name_requirement" value="optional" id="optional_company" {{ $companyNameReq == 'optional' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="optional_company"> Optional </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="company_name_requirement" value="required" id="required_company" {{ $companyNameReq == 'required' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="required_company"> Required </label>
                </div>
              </div>

              <div class="mb-4">
                <p class="mb-0 fw-medium">Address line 2 (apartment, unit, etc.)</p>
                @php $address2Req = $settings['address_line_2_requirement'] ?? 'optional'; @endphp
                <div class="form-check my-2 ms-2">
                  <input class="form-check-input" type="radio" name="address_line_2_requirement" value="dont_include" id="dont_include_address" {{ $address2Req == 'dont_include' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="dont_include_address"> Don't include line 2 </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="address_line_2_requirement" value="optional" id="optional_address" {{ $address2Req == 'optional' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="optional_address"> Optional </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="address_line_2_requirement" value="required" id="required_address" {{ $address2Req == 'required' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="required_address"> Required </label>
                </div>
              </div>

              <div>
                <p class="mb-0 fw-medium">Shipping address phone number</p>
                @php $shipPhoneReq = $settings['shipping_phone_requirement'] ?? 'optional'; @endphp
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="shipping_phone_requirement" value="dont_include" id="dont_include_ship_phone" {{ $shipPhoneReq == 'dont_include' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="dont_include_ship_phone"> Don't include phone </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="shipping_phone_requirement" value="optional" id="optional_ship_phone" {{ $shipPhoneReq == 'optional' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="optional_ship_phone"> Optional </label>
                </div>
                <div class="form-check mt-4 ms-2">
                  <input class="form-check-input" type="radio" name="shipping_phone_requirement" value="required" id="required_ship_phone" {{ $shipPhoneReq == 'required' ? 'checked' : '' }} />
                  <label class="form-check-label text-heading" for="required_ship_phone"> Required </label>
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
    </form>
  </div>
  <!-- /Options-->
</div>
@endsection
