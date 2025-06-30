@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Cards Gamifications- UI elements')

@section('content')
  <div class="row g-6">
    <div class="col-md-6 col-lg-4 order-lg-1 order-2">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-7">
            <div class="card-body">
              <h5 class="card-title mb-1 text-nowrap">Congratulations Katie! 🎉</h5>
              <p class="card-subtitle text-nowrap mb-3">Best seller of the month</p>

              <h5 class="card-title text-primary mb-0">$48.9k</h5>
              <p class="mb-3">78% of target 🚀</p>

              <a href="javascript:;" class="btn btn-sm btn-primary">View sales</a>
            </div>
          </div>
          <div class="col-5">
            <div class="card-body pb-0 text-end">
              <img src="{{ asset('assets/img/illustrations/prize-light.png') }}" width="91" height="144"
                class="rounded-start" alt="View Sales" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8 order-lg-2 order-1 align-self-end">
      <div class="card">
        <div class="d-flex align-items-start row">
          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary mb-3">Congratulations John! 🎉</h5>
              <p class="mb-6">You have done 72% more sales today.<br />Check your new badge in your profile.</p>

              <a href="javascript:;" class="btn btn-sm btn-label-primary">View Badges</a>
            </div>
          </div>
          <div class="col-sm-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-6">
              <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="175" class="scaleX-n1-rtl"
                alt="View Badge User" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8 order-lg-3 col-12 align-self-end order-4">
      <div class="card">
        <div class="d-flex row">
          <div class="col-sm-6 col-md-5 text-center text-sm-left">
            <div class="card-body pb-0 ps-10 text-sm-start text-center">
              <img src="{{ asset('assets/img/illustrations/sitting-girl-with-laptop.png') }}" height="181"
                alt="Target User" />
            </div>
          </div>
          <div class="col-sm-6 col-md-7">
            <div class="card-body">
              <h5 class="card-title text-primary mb-3">Welcome back Anna!</h5>
              <p class="mb-6">You have 12 task to finish today, Your already completed 189 task good job.</p>

              <span class="badge bg-label-primary">78% of TARGET</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-md-4 col-lg-4 order-lg-4 order-3">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-4">
            <div class="card-body pb-0">
              <img src="{{ asset('assets/img/illustrations/superman-flying.png') }}" height="176"
                class="rounded-start scaleX-n1-rtl" alt="upgrade account" />
            </div>
          </div>
          <div class="col-8 text-center">
            <div class="card-body">
              <h5 class="card-title mb-1">Upgrade Account</h5>
              <p class="card-subtitle mb-3">Add 15 team members</p>

              <h5 class="card-title text-info mb-0">$129</h5>
              <p class="mb-3">20% OFF</p>

              <a href="javascript:;" class="btn btn-sm btn-info">Upgrade</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
