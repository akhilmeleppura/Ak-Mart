@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verify Email Cover - Pages')

@section('page-style')
  <!-- Page -->
  @vite('resources/assets/vendor/scss/pages/page-auth.scss')
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
          <img src="{{ asset('assets/img/illustrations/boy-verify-email-' . $configData['theme'] . '.png') }}"
            class="img-fluid" alt="Login image" width="700" data-app-dark-img="illustrations/boy-verify-email-dark.png"
            data-app-light-img="illustrations/boy-verify-email-light.png">
        </div>
      </div>
      <!-- /Left Text -->

      <!--  Verify email -->
      <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-sm-12 mt-8">
          <h4 class="mb-1">Verify your email ✉️</h4>
          <p class="text-start mb-0">Account activation link sent to your email address: <span
              class="fw-medium text-heading">john.doe@email.com</span> Please follow the link inside to continue.</p>
          <a class="btn btn-primary w-100 my-6" href="{{ url('/') }}">Skip for now</a>
          <p class="text-center mb-0">
            Didn't get the mail?
            <a href="javascript:void(0);">Resend</a>
          </p>
        </div>
      </div>
      <!-- / Verify email -->
    </div>
  </div>
@endsection
