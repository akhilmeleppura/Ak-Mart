@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Access Control Hub - Ak Mart')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-access-roles.js'])
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="card-header border-bottom">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#roles-overview" role="tab">
              <i class="icon-base bx bx-shield me-2"></i> Role Management
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#role-permissions" role="tab">
              <i class="icon-base bx bx-check-shield me-2"></i> Role Permissions
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-roles" role="tab">
              <i class="icon-base bx bx-user-check me-2"></i> User Assignment
            </button>
          </li>
        </ul>
      </div>
      <div class="tab-content card-body">
        <!-- Tab 1: Roles Overview -->
        <div class="tab-pane fade show active" id="roles-overview" role="tabpanel">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">System Roles</h5>
            <button class="btn btn-primary add-new-role" data-bs-toggle="modal" data-bs-target="#addRoleModal">
              <i class="icon-base bx bx-plus me-2"></i> Create New Role
            </button>
          </div>
          <div class="row g-6">
            @foreach ($roles as $role)
            <div class="col-xl-4 col-lg-6 col-md-6">
              <div class="card border">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-normal mb-0">Total {{ $role->users->count() }} users</h6>
                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                      @foreach($role->users->take(3) as $u)
                      <li data-bs-toggle="tooltip" title="{{ $u->name }}" class="avatar avatar-sm pull-up">
                        <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($u->name, 0, 2)) }}</span>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                  <div class="d-flex justify-content-between align-items-end">
                    <div>
                      <h5 class="mb-1 text-primary">{{ $role->name }}</h5>
                      <small class="text-muted">{{ $role->permissions->count() }} Permissions assigned</small>
                    </div>
                    <div class="d-flex gap-2">
                       <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-label-danger delete-role" data-id="{{ $role->id }}">
                         <i class="icon-base bx bx-trash"></i>
                       </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <!-- Tab 2: Role Permissions -->
        <div class="tab-pane fade" id="role-permissions" role="tabpanel">
           <div class="row">
             <div class="col-md-4 border-end">
                <h6 class="mb-4">Select Role to Configure</h6>
                <div class="list-group list-group-flush" id="role-selector">
                  @foreach($roles as $role)
                  <button type="button" class="list-group-item list-group-item-action role-item d-flex justify-content-between align-items-center" data-id="{{ $role->id }}">
                    {{ $role->name }}
                    <i class="icon-base bx bx-chevron-right"></i>
                  </button>
                  @endforeach
                </div>
             </div>
             <div class="col-md-8 pt-4 pt-md-0 ps-md-6">
                <div id="permission-config-container" class="d-none">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Permissions for: <span id="selected-role-name" class="text-primary"></span></h5>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                      <label class="form-check-label" for="selectAllPermissions">Select All</label>
                    </div>
                  </div>
                  <form id="syncRolePermissionsForm">
                    @csrf
                    <input type="hidden" name="role_id" id="config-role-id">
                    <div class="row g-3" id="permissions-grid">
                      @foreach($permissions as $permission)
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="p-{{ $permission->id }}">
                          <label class="form-check-label" for="p-{{ $permission->id }}">
                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                          </label>
                        </div>
                      </div>
                      @endforeach
                    </div>
                    <div class="mt-6">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form>
                </div>
                <div id="no-role-selected" class="text-center py-10">
                  <i class="icon-base bx bx-shield-alt-2 icon-56px text-muted mb-4"></i>
                  <p class="text-muted">Select a role from the left to manage its permissions.</p>
                </div>
             </div>
           </div>
        </div>

        <!-- Tab 3: User Assignment -->
        <div class="tab-pane fade" id="user-roles" role="tabpanel">
           <div class="row justify-content-center">
             <div class="col-md-8">
               <div class="card border shadow-none mt-4">
                 <div class="card-body">
                   <h5 class="mb-6">Assign Roles to Users</h5>
                   <form id="assignUserRoleForm">
                     @csrf
                     <div class="mb-6">
                       <label class="form-label">Search User</label>
                       <select name="user_id" class="form-select select2-user-search" required>
                         <option value="">Select a user...</option>
                         @foreach($users as $user)
                         <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                         @endforeach
                       </select>
                     </div>
                     <div class="mb-6">
                       <label class="form-label">Available Roles</label>
                       <div class="row g-4 mt-2">
                         @foreach($roles as $role)
                         <div class="col-md-6">
                           <div class="form-check card-radio">
                             <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="user-role-{{ $role->id }}">
                             <label class="form-check-label p-3 border rounded d-block" for="user-role-{{ $role->id }}">
                               <span class="d-block fw-bold mb-1">{{ $role->name }}</span>
                               <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                             </label>
                           </div>
                         </div>
                         @endforeach
                       </div>
                     </div>
                     <div class="text-end">
                       <button type="submit" class="btn btn-primary btn-lg px-10">Assign Roles</button>
                     </div>
                   </form>
                 </div>
               </div>
             </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Role Modal (Simplified) -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title">Create New Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="quickCreateRoleForm">
        @csrf
        <div class="modal-body py-6">
          <div class="col-12">
            <label class="form-label" for="roleName">Role Name</label>
            <input type="text" id="roleName" name="name" class="form-control form-control-lg" placeholder="e.g. Moderator" required />
            <small class="text-muted mt-2 d-block">You can assign permissions after creating the role.</small>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Role</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
