@extends('layouts/layoutMaster')

@section('title', 'User Management - AK-Mart')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/app-user-list.js')
@endsection

@section('content')

<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card border-start border-4 border-primary shadow-xs">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-semibold">Total Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-primary fw-bold">{{ $totalUser }}</h4>
              <span class="badge bg-label-primary">System</span>
            </div>
            <small class="mb-0 text-muted">Console Accounts</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-user icon-lg"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-start border-4 border-success shadow-xs">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-semibold">Verified Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-success fw-bold">{{ $verified }}</h4>
              <span class="badge bg-label-success">Active</span>
            </div>
            <small class="mb-0 text-muted">Email Verified</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-user-check icon-lg"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-start border-4 border-warning shadow-xs">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-semibold">Duplicate Emails</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-warning fw-bold">{{ $userDuplicates }}</h4>
              <span class="badge bg-label-warning">Monitor</span>
            </div>
            <small class="mb-0 text-muted">Unique Verification</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning"><i class="icon-base bx bx-user-voice icon-lg"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-start border-4 border-info shadow-xs">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-semibold">Verification Pending</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-info fw-bold">{{ $notVerified }}</h4>
              <span class="badge bg-label-info">Pending</span>
            </div>
            <small class="mb-0 text-muted">Unverified Email</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-user-search icon-lg"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Users List Table -->
<div class="card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0 fw-semibold text-heading"><i class="bx bx-user-pin me-2 text-primary"></i>System Users List</h5>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-users table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>User</th>
          <th>Role</th>
          <th>Plan</th>
          <th>Billing</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Offcanvas to Add/Edit Staff User -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" style="width: 420px;">
    <div class="offcanvas-header border-bottom bg-label-primary bg-opacity-10 py-3">
      <div class="d-flex align-items-center gap-2">
        <div class="avatar avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center">
          <i class="bx bx-user-plus fs-5"></i>
        </div>
        <div>
          <h5 id="offcanvasAddUserLabel" class="offcanvas-title fw-bold mb-0 text-heading">Add Console Staff User</h5>
          <small class="text-muted" style="font-size: 0.75rem;">Create & Configure System User Credentials</small>
        </div>
      </div>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body mx-0 flex-grow-0 p-4 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" autocomplete="off">
        <input type="hidden" name="id" id="user_id">
        
        <!-- Profile Banner -->
        <div class="p-3 mb-4 bg-light rounded-3 text-center border">
          <div class="avatar avatar-lg mx-auto mb-2">
            <span class="avatar-initial rounded-circle bg-primary fs-4 fw-bold text-white shadow-xs">AK</span>
          </div>
          <h6 class="fw-bold mb-0 text-heading">Console User Profile</h6>
          <span class="badge bg-label-primary mt-1"><i class="bx bx-shield-alt-2 me-1"></i>Staff Credentials</span>
        </div>

        <div class="mb-4">
          <h6 class="mb-3 text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px;"><i class="bx bx-user me-1"></i> 1. User Identity</h6>
          
          <div class="mb-3 form-control-validation">
            <label class="form-label fw-semibold" for="add-user-fullname">Full Name*</label>
            <div class="input-group">
              <span class="input-group-text bg-white text-muted"><i class="bx bx-user"></i></span>
              <input type="text" class="form-control" id="add-user-fullname" placeholder="John Doe" name="name" autocomplete="off" required />
            </div>
          </div>

          <div class="mb-3 form-control-validation">
            <label class="form-label fw-semibold" for="add-user-email">Email Address*</label>
            <div class="input-group">
              <span class="input-group-text bg-white text-muted"><i class="bx bx-envelope"></i></span>
              <input type="email" id="add-user-email" class="form-control" placeholder="john.doe@ak-mart.com" name="email" autocomplete="off" required />
            </div>
          </div>
        </div>

        <div class="mb-4 pt-3 border-top">
          <h6 class="mb-3 text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px;"><i class="bx bx-cog me-1"></i> 2. Role & Branch Assignment</h6>
          
          <div class="mb-3">
            <label class="form-label fw-semibold" for="user-role">Spatie User Role*</label>
            <div class="input-group">
              <span class="input-group-text bg-white text-muted"><i class="bx bx-shield-quarter"></i></span>
              <select id="user-role" name="role" class="form-select" required>
                <option value="">Select Role</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" for="user-branch">Assigned Branch</label>
            <div class="input-group">
              <span class="input-group-text bg-white text-muted"><i class="bx bx-git-branch"></i></span>
              <select id="user-branch" name="branch_id" class="form-select">
                <option value="1">Global HQ (New York)</option>
                <option value="2">London Flagship</option>
                <option value="3">Dubai Mall Branch</option>
              </select>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 pt-3 border-top">
          <button type="submit" class="btn btn-primary me-2 data-submit shadow-xs w-100">
            <i class="bx bx-check-circle me-1"></i> Save User Account
          </button>
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
