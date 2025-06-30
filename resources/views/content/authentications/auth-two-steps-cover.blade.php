@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Two Steps Verifications Cover - Pages')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/pages-auth.js', 'resources/assets/js/pages-auth-two-steps.js'])
@endsection

@section('content')
  <div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="app-brand auth-cover-brand gap-2">
      <span class="app-brand-logo demo">@include('_partials.macros')</span>
      <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
    </a>
    <!-- /Logo -->
    <div class="authentication-inner row m-0">
      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
        <div class="w-100 d-flex justify-content-center">
          <img src="{{ asset('assets/img/illustrations/girl-verify-password-' . $configData['theme'] . '.png') }}"
            class="img-fluid scaleX-n1-rtl" alt="Login image" width="700"
            data-app-dark-img="illustrations/girl-verify-password-dark.png"
            data-app-light-img="illustrations/girl-verify-password-light.png" />
        </div>
      </div>
      <!-- /Left Text -->

      <!-- Two Steps Verification -->
      <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-sm-12 mt-8">
          <h4 class="mb-1">Two Step Verification 💬</h4>
          <p class="text-start mb-6">
            We sent a verification code to your mobile. Enter the code from the mobile in the field below.
            <span class="fw-medium d-block mt-1 text-heading">******1234</span>
          </p>
          <p class="mb-0">Type your 6 digit security code</p>
          <form id="twoStepsForm" action="{{ url('/') }}" method="GET">
            <div class="mb-6 form-control-validation">
              <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" autofocus />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" />
              </div>
              <!-- Create a hidden field which is combined by 3 fields above -->
              <input type="hidden" name="otp" />
            </div>
            <button class="btn btn-primary d-grid w-100 mb-6">Verify my account</button>
            <div class="text-center">
              Didn't get the code?
              <a href="javascript:void(0);">Resend</a>
            </div>
          </form>
        </div>
      </div>
      <!-- /Two Steps Verification -->
    </div>
  </div>
@endsection
