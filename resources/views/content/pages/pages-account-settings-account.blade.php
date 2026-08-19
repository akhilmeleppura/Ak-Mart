@extends('layouts/layoutMaster')

@section('title', __('Account Settings') . ' — AK-Mart')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
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
            <a class="nav-link active" href="javascript:void(0);"><i class="icon-base bx bx-cog icon-sm me-1_5"></i>{{ __('Account Settings') }}</a>
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

      <div class="card mb-6 shadow-sm border-0">
        <!-- Profile Photo -->
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">{{ __('Profile Details & Avatar') }}</h5>
        </div>
        <div class="card-body pt-5">
          <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="d-block w-px-100 h-px-100 rounded-circle border border-2 border-primary shadow-sm"
              id="uploadedAvatar" style="object-fit: cover;" />
            <div class="button-wrapper">
              <label for="uploadPhotoInput" class="btn btn-primary me-3 mb-3 shadow-sm" tabindex="0">
                <span class="d-none d-sm-block"><i class="bx bx-upload me-1"></i>{{ __('Upload New Photo') }}</span>
                <i class="icon-base bx bx-upload d-block d-sm-none"></i>
                <input type="file" id="uploadPhotoInput" class="account-file-input" hidden accept="image/png, image/jpeg, image/webp, image/gif" />
              </label>
              <button type="button" class="btn btn-label-secondary mb-3" id="btnRemovePhoto">
                <i class="icon-base bx bx-reset me-1"></i>
                <span>{{ __('Remove Photo') }}</span>
              </button>

              <div class="text-muted small">{{ __('Allowed JPG, PNG, WEBP or GIF. Maximum file size: 2MB.') }}</div>
            </div>
          </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="card-body pt-4">
          <form id="formAccountSettings" method="POST" action="{{ route('account-settings-profile-update') }}">
            @csrf
            <div class="row g-6">
              <div class="col-md-6">
                <label for="name" class="form-label fw-medium">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus />
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label fw-medium">{{ __('E-mail Address') }} <span class="text-danger">*</span></label>
                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium" for="phone">{{ __('Phone Number') }}</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-phone"></i></span>
                  <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900" />
                </div>
              </div>
              <div class="col-md-6">
                <label for="locale" class="form-label fw-medium">{{ __('Preferred Language') }}</label>
                <select id="locale" name="locale" class="form-select select2">
                  <option value="en" {{ old('locale', $user->locale ?? 'en') == 'en' ? 'selected' : '' }}>English (EN)</option>
                  <option value="ml" {{ old('locale', $user->locale) == 'ml' ? 'selected' : '' }}>മലയാളം - Malayalam (ML)</option>
                  <option value="hi" {{ old('locale', $user->locale) == 'hi' ? 'selected' : '' }}>हिन्दी - Hindi (HI)</option>
                  <option value="ar" {{ old('locale', $user->locale) == 'ar' ? 'selected' : '' }}>العربية - Arabic (AR - RTL)</option>
                  <option value="fr" {{ old('locale', $user->locale) == 'fr' ? 'selected' : '' }}>Français - French (FR)</option>
                  <option value="de" {{ old('locale', $user->locale) == 'de' ? 'selected' : '' }}>Deutsch - German (DE)</option>
                </select>
              </div>

              <div class="col-md-6">
                <label for="address_line_1" class="form-label fw-medium">{{ __('Address Line 1') }}</label>
                <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="{{ old('address_line_1', $user->address_line_1) }}" placeholder="{{ __('Street address, P.O. box') }}" />
              </div>
              <div class="col-md-6">
                <label for="address_line_2" class="form-label fw-medium">{{ __('Address Line 2') }}</label>
                <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="{{ old('address_line_2', $user->address_line_2) }}" placeholder="{{ __('Apartment, suite, unit, building, floor') }}" />
              </div>
              <div class="col-md-4">
                <label for="town" class="form-label fw-medium">{{ __('City / Town') }}</label>
                <input class="form-control" type="text" id="town" name="town" value="{{ old('town', $user->town) }}" placeholder="{{ __('New York') }}" />
              </div>
              <div class="col-md-4">
                <label for="state" class="form-label fw-medium">{{ __('State / Region') }}</label>
                <input class="form-control" type="text" id="state" name="state" value="{{ old('state', $user->state) }}" placeholder="{{ __('NY') }}" />
              </div>
              <div class="col-md-4">
                <label for="post_code" class="form-label fw-medium">{{ __('Postal / Zip Code') }}</label>
                <input type="text" class="form-control" id="post_code" name="post_code" value="{{ old('post_code', $user->post_code) }}" placeholder="10001" />
              </div>
              <div class="col-md-6">
                <label for="country" class="form-label fw-medium">{{ __('Country') }}</label>
                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $user->country ?? 'United States') }}" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">{{ __('Assigned Branch / Store') }}</label>
                <input type="text" class="form-control bg-light" value="{{ $branch ? $branch->name : __('All Branches (Global Admin)') }}" readonly />
                <small class="text-muted">{{ __('Branch assignment is managed by platform administrators.') }}</small>
              </div>

              <div class="col-12 mt-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="marketing_opt_out" name="marketing_opt_out" value="1" {{ old('marketing_opt_out', $user->marketing_opt_out) ? 'checked' : '' }} />
                  <label class="form-check-label" for="marketing_opt_out">{{ __('Opt-out of non-critical marketing and promotional emails') }}</label>
                </div>
              </div>
            </div>
            <div class="mt-6 d-flex gap-3">
              <button type="submit" class="btn btn-primary shadow-sm" id="btnSaveProfile">
                <i class="bx bx-check me-1"></i>{{ __('Save Changes') }}
              </button>
              <a href="{{ route('pages-profile-user') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Photo Upload handling
      const photoInput = document.getElementById('uploadPhotoInput');
      const avatarImg = document.getElementById('uploadedAvatar');
      const removeBtn = document.getElementById('btnRemovePhoto');

      if (photoInput) {
        photoInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.size > 2 * 1024 * 1024) {
              Swal.fire({
                icon: 'error',
                title: @json(__('File Too Large')),
                text: @json(__('Profile photo must not exceed 2MB in size.'))
              });
              return;
            }

            const formData = new FormData();
            formData.append('photo', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Upload via AJAX
            fetch('{{ route("account-settings-photo-update") }}', {
              method: 'POST',
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: formData
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                avatarImg.src = data.photo_url;
                Swal.fire({
                  icon: 'success',
                  title: @json(__('Photo Updated')),
                  text: data.message,
                  timer: 1800,
                  showConfirmButton: false
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: @json(__('Upload Failed')),
                  text: data.message || @json(__('Unable to update photo.'))
                });
              }
            })
            .catch(() => {
              Swal.fire({
                icon: 'error',
                title: @json(__('Error')),
                text: @json(__('An error occurred while uploading photo.'))
              });
            });
          }
        });
      }

      // Remove Photo handling
      if (removeBtn) {
        removeBtn.addEventListener('click', function() {
          Swal.fire({
            title: @json(__('Remove Profile Photo?')),
            text: @json(__('Your profile avatar will be reset to default.')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: @json(__('Yes, remove it')),
            cancelButtonText: @json(__('Cancel')),
            customClass: {
              confirmButton: 'btn btn-primary me-3',
              cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
          }).then(result => {
            if (result.isConfirmed) {
              fetch('{{ route("account-settings-photo-remove") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  avatarImg.src = data.photo_url;
                  Swal.fire({
                    icon: 'success',
                    title: @json(__('Photo Removed')),
                    text: data.message,
                    timer: 1800,
                    showConfirmButton: false
                  });
                }
              });
            }
          });
        });
      }
    });
  </script>
@endsection
