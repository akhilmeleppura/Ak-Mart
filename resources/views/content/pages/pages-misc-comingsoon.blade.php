@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Coming Soon - Pages')

@section('page-style')
  <!-- Page -->
  @vite('resources/assets/vendor/scss/pages/page-misc.scss')
@endsection

@section('content')
  <!-- Coming Soon -->
  <div class="container-xxl py-4">
    <div class="misc-wrapper">
      <h3 class="mb-2 mx-2">We are launching soon 🚀</h3>
      <p class="mb-6 mx-2">Our website is opening soon. Please register to get notified when it's ready!</p>
      <form onsubmit="return false">
        <div>
          <div class="mb-1 d-flex gap-4">
            <input type="email" class="form-control" placeholder="Enter your email" autofocus />
            <button type="submit" class="btn btn-primary">Notify</button>
          </div>
        </div>
      </form>
      <div class="mt-12">
        <img src="{{ asset('assets/img/illustrations/boy-with-rocket-' . $configData['theme'] . '.png') }}"
          alt="boy-with-rocket-light" width="500" class="img-fluid"
          data-app-light-img="illustrations/boy-with-rocket-light.png"
          data-app-dark-img="illustrations/boy-with-rocket-dark.png" />
      </div>
    </div>
  </div>
  <!-- /Coming Soon -->
@endsection
