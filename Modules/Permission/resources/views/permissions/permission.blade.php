@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Permission Management')

@section('vendor-style')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('vendor-script')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('page-script')
  <script>
    $(document).ready(function () {
      $('#role_id').select2({
        placeholder: "Select a role",
        width: '100%'
      });
    });
  </script>
@endsection

@section('content')
<div class="row g-4">
  <!-- Assign Permission Section -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assign Permissions to Role</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('permissions.assign') }}" method="POST">
          @csrf
          <div class="row g-3">
            <!-- Select Role -->
            <div class="col-md-6">
              <label for="role_id" class="form-label">Select Role</label>
              <select name="role_id" id="role_id" class="form-select" required>
                <option value="">-- Select Role --</option>
                @foreach ($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Permissions Section -->
          <div class="row mt-4">
            @foreach ($groupedPermissions as $group => $permissions)
              <div class="col-md-6">
                <div class="border p-3 rounded mb-3">
                  <h6 class="text-primary mb-3">{{ ucfirst($group) }}</h6>
                  @foreach ($permissions as $permission)
                    <div class="form-check mb-1">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}">
                      <label class="form-check-label" for="perm-{{ $permission->id }}">{{ $permission->label }}</label>
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>

          <!-- Submit -->
          <div class="mt-3">
            <button type="submit" class="btn btn-success">Assign Permissions</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Role Permission Table -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Role Permissions</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Sl. No</th>
                <th>Role Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($roles as $index => $role)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $role->name }}</td>
                  <td>
                    <a href="{{ route('permissions.show', $role->id) }}" class="btn btn-sm btn-outline-info me-1">Show Permissions</a>
                    <a href="{{ route('permissions.edit', $role->id) }}" class="btn btn-sm btn-outline-primary">Edit Permissions</a>
                  </td>
                </tr>
              @endforeach
              @if($roles->isEmpty())
                <tr>
                  <td colspan="3" class="text-center">No roles found.</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
