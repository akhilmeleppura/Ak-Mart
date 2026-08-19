@extends('layouts/layoutMaster')

@section('title', __('Security & Password') . ' — AK-Mart')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-profile-user') }}"><i class="icon-base bx bx-user icon-sm me-1_5"></i>{{ __('My Profile') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-account') }}"><i class="icon-base bx bx-cog icon-sm me-1_5"></i>{{ __('Account Settings') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);"><i class="icon-base bx bx-lock-alt icon-sm me-1_5"></i>{{ __('Security & Password') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('app-saas-billing') }}"><i class="icon-base bx bx-credit-card icon-sm me-1_5"></i>{{ __('Billing & Plans') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-account-settings-notifications') }}"><i class="icon-base bx bx-bell icon-sm me-1_5"></i>{{ __('Notifications') }}</a>
          </li>
        </ul>
      </div>

      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <ul class="mb-0 ps-3">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      <!-- Change Password Card -->
      <div class="card mb-6 shadow-sm border-0">
        <h5 class="card-header border-bottom d-flex align-items-center gap-2">
          <i class="bx bx-key text-primary fs-4"></i>
          <span>{{ __('Change Account Password') }}</span>
        </h5>
        <div class="card-body pt-5">
          <form id="formPasswordChange" method="POST" action="{{ route('account-settings-password-update') }}">
            @csrf
            <div class="row g-6">
              <div class="col-md-6 form-password-toggle">
                <label class="form-label fw-medium" for="currentPassword">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <input class="form-control" type="password" name="currentPassword" id="currentPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                  <span class="input-group-text cursor-pointer" onclick="togglePassVisibility('currentPassword')"><i class="bx bx-hide" id="icon-currentPassword"></i></span>
                </div>
              </div>
            </div>

            <div class="row g-6 mt-1">
              <div class="col-md-6 form-password-toggle">
                <label class="form-label fw-medium" for="newPassword">{{ __('New Password') }} <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required minlength="8" />
                  <span class="input-group-text cursor-pointer" onclick="togglePassVisibility('newPassword')"><i class="bx bx-hide" id="icon-newPassword"></i></span>
                </div>
              </div>

              <div class="col-md-6 form-password-toggle">
                <label class="form-label fw-medium" for="newPassword_confirmation">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <input class="form-control" type="password" name="newPassword_confirmation" id="newPassword_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required minlength="8" />
                  <span class="input-group-text cursor-pointer" onclick="togglePassVisibility('newPassword_confirmation')"><i class="bx bx-hide" id="icon-newPassword_confirmation"></i></span>
                </div>
              </div>
            </div>

            <div class="bg-label-secondary p-4 rounded mt-4">
              <h6 class="text-heading fw-bold mb-2">{{ __('Password Requirements:') }}</h6>
              <ul class="ps-4 mb-0 small text-muted">
                <li class="mb-1">{{ __('Minimum 8 characters long — the more, the better.') }}</li>
                <li class="mb-1">{{ __('Include a mix of letters, numbers, and symbols.') }}</li>
                <li>{{ __('Never share your password or reuse it on other sites.') }}</li>
              </ul>
            </div>

            <div class="mt-6 d-flex gap-3">
              <button type="submit" class="btn btn-primary shadow-sm">
                <i class="bx bx-lock-open-alt me-1"></i>{{ __('Update Password') }}
              </button>
              <button type="reset" class="btn btn-label-secondary">{{ __('Reset') }}</button>
            </div>
          </form>
        </div>
      </div>
      <!--/ Change Password Card -->

      <!-- Active Session & Security Status -->
      <div class="card mb-6 shadow-sm border-0">
        <h5 class="card-header border-bottom d-flex align-items-center gap-2">
          <i class="bx bx-devices text-info fs-4"></i>
          <span>{{ __('Active Device & Session Security') }}</span>
        </h5>
        <div class="card-body pt-5">
          <div class="d-flex align-items-center justify-content-between p-4 bg-label-primary rounded mb-4">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-md bg-white rounded p-2 text-primary shadow-xs">
                <i class="bx bx-laptop fs-3"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">{{ __('Current Browser Session') }}</h6>
                <small class="text-muted">{{ __('IP Address:') }} <code>{{ request()->ip() }}</code> — {{ __('Active Now') }}</small>
              </div>
            </div>
            <span class="badge bg-success px-3 py-1">{{ __('Connected') }}</span>
          </div>

          <div class="text-muted small">
            <i class="bx bx-shield-check text-success me-1"></i>
            {{ __('Your account is secured with session hashing and CSRF protection on all sensitive operations.') }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassVisibility(inputId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById('icon-' + inputId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
      } else {
        input.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
      }
    }
  </script>
@endsection
