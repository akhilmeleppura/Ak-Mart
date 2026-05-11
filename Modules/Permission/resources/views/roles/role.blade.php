@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Roles - Apps')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/app-access-roles.js',
    'resources/assets/js/modal-add-role.js'
  ])
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.delete-role-btn').forEach(button => {
        button.addEventListener('click', function () {
          const roleId = this.getAttribute('data-id');
          const roleName = this.getAttribute('data-name');

          Swal.fire({
            title: `Delete role: ${roleName}?`,
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById(`delete-form-${roleId}`).submit();
            }
          });
        });
      });

      @if(session('success'))
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: '{{ session("success") }}',
    showConfirmButton: false,
    timer: 2500,
    didOpen: (toast) => {
      toast.style.marginTop = '60px'; // Move down 60px from top
    }
  });
@endif

@if(session('error'))
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'error',
    title: '{{ session("error") }}',
    showConfirmButton: false,
    timer: 2500,
    didOpen: (toast) => {
      toast.style.marginTop = '60px'; 
    }
  });
@endif

    });
  </script>
@endsection
@php
  $user = auth()->user();
  $isSupremeAdmin = $user->is_supreme_admin == 1;
  $canViewRole = $user->can('role.view');
    $canCreateRole = $user->can('role.create');
    $canEditRole = $user->can('role.edit');
    $canDeleteRole = $user->can('role.delete');
    
  $canEditPermission = $user->can('edit.permission');
@endphp

@section('content')
          @if ($isSupremeAdmin || $canCreateRole )
<div class="row g-4">
  <!-- Add Role Section -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Role</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('role.store') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Role Name</label>
              <input type="text" name="name" class="form-control" placeholder="Enter role name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary">Create Role</button>
          </div>
        </form>
      </div>
    </div>
  </div>
          @endif

  <!-- Role List Table Section -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Role List</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Sl. No</th>
                <th>Role Name</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($roles as $index => $role)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $role->name }}</td>
                  <td>
                    <span class="badge {{ $role->status ? 'bg-success' : 'bg-secondary' }}">
                      {{ $role->status ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                   @if ($isSupremeAdmin || $canEditRole )
                    <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                    @endif
                      @if ($isSupremeAdmin || $canDeleteRole )
                    <button type="button"
                            class="btn btn-sm btn-outline-danger delete-role-btn"
                            data-id="{{ $role->id }}"
                            data-name="{{ $role->name }}">
                      Delete
                      @endif
                    </button>

                    <form id="delete-form-{{ $role->id }}" action="{{ route('role.destroy', $role->id) }}" method="POST" style="display: none;">
                      @csrf
                      @method('DELETE')
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center">No roles found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Role Modal (optional if used) -->
@include('_partials._modals.modal-add-role')
@endsection
