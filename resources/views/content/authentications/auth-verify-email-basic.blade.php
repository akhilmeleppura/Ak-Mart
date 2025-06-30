@php
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verify Email Basic - Pages')

@section('page-style')
  <!-- Page -->
  @vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Verify Email -->
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="{{ url('/') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">@include('_partials.macros')</span>
                <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
              </a>
            </div>
            <!-- /Logo -->
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
        <!-- /Verify Email -->
      </div>
    </div>
  </div>
@endsection
