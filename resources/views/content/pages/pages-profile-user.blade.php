@extends('layouts/layoutMaster')

@section('title', __('My Profile') . ' — AK-Mart')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Page Styles -->
@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('content')
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="card mb-6 shadow-sm border-0">
        <div class="user-profile-header-banner">
          <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top w-100" style="max-height: 180px; object-fit: cover;" />
        </div>
        <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-6">
          <div class="flex-shrink-0 mt-n5 mx-sm-0 mx-auto position-relative">
            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
              class="d-block h-auto ms-0 ms-sm-6 rounded-circle user-profile-img border border-4 border-white shadow"
              style="width: 120px; height: 120px; object-fit: cover; background: #fff;" />
          </div>
          <div class="flex-grow-1 mt-3 mt-lg-5">
            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
              <div class="user-profile-info">
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-sm-start justify-content-center">
                  <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                  <span class="badge bg-label-primary px-3 py-1">{{ $roleName }}</span>
                  <span class="badge bg-label-success px-3 py-1"><i class="bx bx-check-circle me-1"></i>{{ __('Active') }}</span>
                </div>
                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 mt-3 text-muted">
                  @if($branch)
                  <li class="list-inline-item">
                    <i class="icon-base bx bx-store me-1 align-top text-primary"></i>
                    <span class="fw-medium">{{ $branch->name }}</span>
                  </li>
                  @endif
                  @if($user->town || $user->country)
                  <li class="list-inline-item">
                    <i class="icon-base bx bx-map me-1 align-top text-danger"></i>
                    <span class="fw-medium">{{ collect([$user->town, $user->country])->filter()->implode(', ') }}</span>
                  </li>
                  @endif
                  <li class="list-inline-item">
                    <i class="icon-base bx bx-calendar me-1 align-top text-info"></i>
                    <span class="fw-medium">{{ __('Joined') }} {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}</span>
                  </li>
                </ul>
              </div>
              <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('pages-account-settings-account') }}" class="btn btn-primary shadow-sm">
                  <i class="icon-base bx bx-edit-alt me-1_5"></i>{{ __('Edit Profile') }}
                </a>
                <a href="{{ route('pages-account-settings-security') }}" class="btn btn-label-secondary">
                  <i class="icon-base bx bx-shield-quarter me-1_5"></i>{{ __('Security') }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Header -->

  <!-- Navbar pills -->
  <div class="row mb-6">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-sm-row gap-sm-0 gap-2">
          <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);"><i class="icon-base bx bx-user icon-sm me-1_5"></i>{{ __('Overview') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-account') }}"><i class="icon-base bx bx-cog icon-sm me-1_5"></i>{{ __('Account Settings') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-security') }}"><i class="icon-base bx bx-lock-alt icon-sm me-1_5"></i>{{ __('Security & Password') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('app-saas-billing') }}"><i class="icon-base bx bx-credit-card icon-sm me-1_5"></i>{{ __('Billing & Plans') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-notifications') }}"><i class="icon-base bx bx-bell icon-sm me-1_5"></i>{{ __('Notifications') }}</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Navbar pills -->

  <!-- User Profile Content -->
  <div class="row">
    <div class="col-xl-4 col-lg-5 col-md-5">
      <!-- About User -->
      <div class="card mb-6 shadow-sm border-0">
        <div class="card-body">
          <h6 class="card-text text-uppercase text-body-secondary small fw-bold mb-4">{{ __('About User') }}</h6>
          <ul class="list-unstyled mb-4">
            <li class="d-flex align-items-center mb-3">
              <i class="icon-base bx bx-user text-primary fs-5 me-3"></i>
              <div>
                <span class="text-muted d-block small">{{ __('Full Name') }}</span>
                <span class="fw-medium text-heading">{{ $user->name }}</span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <i class="icon-base bx bx-envelope text-primary fs-5 me-3"></i>
              <div>
                <span class="text-muted d-block small">{{ __('Email Address') }}</span>
                <span class="fw-medium text-heading">{{ $user->email }}</span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <i class="icon-base bx bx-phone text-primary fs-5 me-3"></i>
              <div>
                <span class="text-muted d-block small">{{ __('Phone') }}</span>
                <span class="fw-medium text-heading">{{ $user->phone ?: __('Not Provided') }}</span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <i class="icon-base bx bx-crown text-warning fs-5 me-3"></i>
              <div>
                <span class="text-muted d-block small">{{ __('System Role') }}</span>
                <span class="badge bg-label-primary">{{ $roleName }}</span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <i class="icon-base bx bx-store text-info fs-5 me-3"></i>
              <div>
                <span class="text-muted d-block small">{{ __('Assigned Branch') }}</span>
                <span class="fw-medium text-heading">{{ $branch ? $branch->name : __('All Branches (Global)') }}</span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <i class="icon-base bx bx-globe text-success fs-5 me-3"></i>
              <div>
                <span class="text-muted d-block small">{{ __('Preferred Language') }}</span>
                <span class="fw-medium text-heading text-uppercase">{{ $user->locale ?: 'EN' }}</span>
              </div>
            </li>
          </ul>

          <h6 class="card-text text-uppercase text-body-secondary small fw-bold mb-3">{{ __('Address & Location') }}</h6>
          <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-start mb-2">
              <i class="icon-base bx bx-map-pin text-danger fs-5 me-3 mt-1"></i>
              <div>
                <span class="fw-medium text-heading">
                  {{ collect([$user->address_line_1, $user->address_line_2, $user->town, $user->state, $user->post_code, $user->country])->filter()->implode(', ') ?: __('No address registered.') }}
                </span>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <!--/ About User -->

      <!-- Store Overview Card -->
      @if($branch)
      <div class="card mb-6 shadow-sm border-0">
        <div class="card-body">
          <h6 class="card-text text-uppercase text-body-secondary small fw-bold mb-3">{{ __('Store / Branch Details') }}</h6>
          <div class="d-flex align-items-center mb-3">
            <div class="avatar avatar-md bg-label-primary rounded p-2 me-3">
              <i class="bx bx-buildings fs-3"></i>
            </div>
            <div>
              <h6 class="mb-0">{{ $branch->name }}</h6>
              <small class="text-muted">{{ __('Branch Code:') }} <strong>{{ $branch->code ?: 'BR-' . $branch->id }}</strong></small>
            </div>
          </div>
          <p class="small text-muted mb-0"><i class="bx bx-map me-1"></i>{{ $branch->address ?: __('Standard Location') }}</p>
        </div>
      </div>
      @endif
    </div>

    <div class="col-xl-8 col-lg-7 col-md-7">
      <!-- Activity Timeline -->
      <div class="card mb-6 shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-time-five text-primary"></i>
            <span>{{ __('Recent Account Activity') }}</span>
          </h5>
          <span class="badge bg-label-secondary">{{ $activities->count() }} {{ __('Events') }}</span>
        </div>
        <div class="card-body pt-2">
          @if($activities->isNotEmpty())
          <ul class="timeline mb-0">
            @foreach($activities as $act)
            <li class="timeline-item timeline-item-transparent pb-4">
              <span class="timeline-point @if(str_contains($act->event, 'password')) timeline-point-danger @elseif(str_contains($act->event, 'photo')) timeline-point-info @elseif(str_contains($act->event, 'plan')) timeline-point-warning @else timeline-point-primary @endif"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1 d-flex justify-content-between">
                  <h6 class="mb-0 fw-semibold text-capitalize">{{ str_replace('_', ' ', $act->event) }}</h6>
                  <small class="text-muted">{{ $act->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1 text-muted small">
                  {{ __('Triggered from IP:') }} <code>{{ $act->ip_address ?: '127.0.0.1' }}</code>
                </p>
                @if($act->new_values)
                <div class="badge bg-label-secondary rounded mt-1 font-monospace small">
                  {{ Str::limit($act->new_values, 120) }}
                </div>
                @endif
              </div>
            </li>
            @endforeach
          </ul>
          @else
          <div class="text-center py-6 text-muted">
            <i class="bx bx-history fs-1 d-block mb-2 text-secondary"></i>
            <p class="mb-0">{{ __('No recent activity logs recorded for your profile.') }}</p>
          </div>
          @endif
        </div>
      </div>
      <!--/ Activity Timeline -->

      <!-- Quick Actions Grid -->
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-between">
              <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-label-primary rounded p-2 me-3">
                  <i class="bx bx-shield-alt-2 fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0">{{ __('Account Security') }}</h6>
                  <small class="text-muted">{{ __('Manage password & 2FA protection') }}</small>
                </div>
              </div>
              <a href="{{ route('pages-account-settings-security') }}" class="btn btn-outline-primary btn-sm w-100">{{ __('Update Password') }}</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-between">
              <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-label-success rounded p-2 me-3">
                  <i class="bx bx-credit-card-front fs-3"></i>
                </div>
                <div>
                  <h6 class="mb-0">{{ __('Store Subscription') }}</h6>
                  <small class="text-muted">{{ __('View plan limits & active billing') }}</small>
                </div>
              </div>
              <a href="{{ route('app-saas-billing') }}" class="btn btn-outline-success btn-sm w-100">{{ __('Manage Plan') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
