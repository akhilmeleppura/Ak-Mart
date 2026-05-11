@extends('layouts/layoutMaster')

@section('title', 'User Management - Crud App')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

<script>
    const ajaxUrl = "{{ $standardDataTableConfig['otherConfig']['ajaxUrl'] }}";
     window.standardDataTableConfig = @json($standardDataTableConfig);

</script>

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$totalUser}}</h4>
              <p class="text-success mb-0">(100%)</p>
            </div>
            <small class="mb-0">Total Users</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base bx bx-group icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Verified Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$verified}}</h4>
              <p class="text-success mb-0">(+95%)</p>
            </div>
            <small class="mb-0">Recent analytics </small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base bx bx-user-plus icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Duplicate Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$userDuplicates}}</h4>
              <p class="text-success mb-0">(0%)</p>
            </div>
            <small class="mb-0">Recent analytics</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base bx bx-user-check icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Verification Pending</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$notVerified}}</h4>
              <p class="text-danger mb-0">(+6%)</p>
            </div>
            <small class="mb-0">Recent analytics</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base bx bx-user-voice icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Users List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Search Filter</h5>
  </div>
  <div class="card-datatable">
    <table class="datatables-users table border-top">
      <thead>
        <tr>
        <th></th>
        @include('HS.standard-datatable')
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new user -->
 @include('HS.standard-offcanvas-right')
</div>

@endsection
