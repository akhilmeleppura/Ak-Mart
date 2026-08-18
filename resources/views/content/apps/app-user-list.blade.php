@extends('layouts/layoutMaster')

@section('title', 'System Users & Staff Directory - AK-Mart')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/app-user-list.js')
@endsection

@section('content')
  <!-- System Users Management Header (Type 2: Executive Staff Administration Design) -->
  <div class="card mb-6 border-0 shadow-xs bg-label-primary bg-opacity-10">
    <div class="card-body py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-primary text-white rounded-3 d-flex align-items-center justify-content-center">
          <i class="bx bx-shield-quarter fs-2 text-white"></i>
        </div>
        <div>
          <h4 class="mb-1 text-heading fw-bold">Console Staff & System Users</h4>
          <p class="mb-0 text-muted small">Manage console access, assign Spatie RBAC roles, bind branch access scopes, and monitor staff login sessions.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-start border-4 border-primary shadow-xs">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading fw-semibold">Total Staff Accounts</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2 text-primary fw-bold">5</h4>
                <span class="badge bg-label-primary">Console</span>
              </div>
              <small class="mb-0 text-muted">Active Users</small>
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
      <div class="card border-start border-4 border-info shadow-xs">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading fw-semibold">Super Admins</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2 text-info fw-bold">2</h4>
                <span class="badge bg-label-info">Full Access</span>
              </div>
              <small class="mb-0 text-muted">All Branches</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-info">
                <i class="icon-base bx bx-user-plus icon-lg"></i>
              </span>
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
              <span class="text-heading fw-semibold">Branch Managers</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2 text-success fw-bold">2</h4>
                <span class="badge bg-label-success">Branch Scope</span>
              </div>
              <small class="mb-0 text-muted">Store Managers</small>
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
      <div class="card border-start border-4 border-warning shadow-xs">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading fw-semibold">Store Cashiers</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2 text-warning fw-bold">1</h4>
                <span class="badge bg-label-warning">POS Terminal</span>
              </div>
              <small class="mb-0 text-muted">Active Cashiers</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-warning">
                <i class="icon-base bx bx-terminal icon-lg"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0 fw-semibold text-heading"><i class="bx bx-user-pin me-2 text-primary"></i>Staff Users Directory</h5>
    </div>
    <div class="card-datatable">
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

    <!-- Type 2 Offcanvas Form: Executive Staff Member Details & Governance Form -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" style="width: 420px;">
      <div class="offcanvas-header border-bottom bg-label-primary bg-opacity-10 py-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center">
            <i class="bx bx-user-check fs-5"></i>
          </div>
          <div>
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title fw-bold mb-0 text-heading">Add Console Staff User</h5>
            <small class="text-muted" style="font-size: 0.75rem;">System User Profile & Security Credentials</small>
          </div>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-4 h-100">
        <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false">
          
          <!-- Staff Profile Badge Banner -->
          <div class="p-3 mb-4 bg-light rounded-3 text-center border">
            <div class="avatar avatar-xl mx-auto mb-2 position-relative">
              <span class="avatar-initial rounded-circle bg-primary fs-3 fw-bold text-white shadow-xs">AK</span>
            </div>
            <h6 class="fw-bold mb-0 text-heading">Console Staff Member</h6>
            <span class="badge bg-label-primary mt-1"><i class="bx bx-shield-alt-2 me-1"></i>System Account</span>
          </div>

          <div class="mb-4">
            <h6 class="mb-3 text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px;"><i class="bx bx-user me-1"></i> 1. Staff Identity</h6>
            
            <div class="mb-3 form-control-validation">
              <label class="form-label fw-semibold" for="add-user-fullname">Full Staff Name*</label>
              <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bx bx-user"></i></span>
                <input type="text" class="form-control" id="add-user-fullname" placeholder="Akhil S" name="userFullname" required />
              </div>
            </div>

            <div class="mb-3 form-control-validation">
              <label class="form-label fw-semibold" for="add-user-email">Official Email Address*</label>
              <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bx bx-envelope"></i></span>
                <input type="email" id="add-user-email" class="form-control" placeholder="staff@ak-mart.com" name="userEmail" required />
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="add-user-contact">Contact Phone</label>
              <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bx bx-phone"></i></span>
                <input type="text" id="add-user-contact" class="form-control phone-mask" placeholder="+1 (555) 019-2834" name="userContact" />
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="add-user-password">Initial Password</label>
              <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bx bx-lock-alt"></i></span>
                <input type="password" id="add-user-password" class="form-control" placeholder="••••••••" name="userPassword" />
              </div>
              <small class="text-muted" style="font-size: 0.75rem;">Leave blank to generate temporary login invite.</small>
            </div>
          </div>

          <div class="mb-4 pt-3 border-top">
            <h6 class="mb-3 text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px;"><i class="bx bx-cog me-1"></i> 2. Access Governance & Branch Scoping</h6>
            
            <div class="mb-3">
              <label class="form-label fw-semibold" for="user-role">Spatie Access Role*</label>
              <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bx bx-shield-quarter"></i></span>
                <select id="user-role" class="form-select" required>
                  <option value="Super Admin">Super Admin (Full System Access)</option>
                  <option value="Branch Manager">Branch Manager (Branch Scoped)</option>
                  <option value="User">Cashier / POS User (Terminal Only)</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="user-branch">Assigned Store Branch*</label>
              <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bx bx-git-branch"></i></span>
                <select id="user-branch" class="form-select" required>
                  <option value="1">Global HQ (New York)</option>
                  <option value="2">London Flagship</option>
                  <option value="3">Dubai Mall Branch</option>
                  <option value="4">Main Branch</option>
                  <option value="5">Sub Branch</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="user-status">Account Status</label>
              <select id="user-status" class="form-select">
                <option value="active">Active (Granted Access)</option>
                <option value="inactive">Suspended / Deactivated</option>
              </select>
            </div>

            <div class="form-check form-switch my-3">
              <input class="form-check-input" type="checkbox" id="twoFactorEnforce" checked />
              <label class="form-check-label fw-semibold text-heading small" for="twoFactorEnforce">Enforce Two-Factor Authentication (2FA)</label>
            </div>
          </div>

          <div class="d-flex gap-2 pt-3 border-top">
            <button type="submit" class="btn btn-primary me-2 data-submit shadow-xs">
              <i class="bx bx-check-circle me-1"></i> Save Staff Account
            </button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
