@extends('layouts.layoutMaster')
@section('title', 'Edit Role Permissions')

@section('content')
<div class="row g-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Edit Permissions for Role: <strong>{{ $role->name }}</strong></h5>
      </div>
      <div class="card-body">
        <form action="{{ route('permissions.update', $role->id) }}" method="POST">
          @csrf
          <div class="row">
            @foreach ($permissions as $module => $modulePermissions)
              <div class="col-12 mb-3">
                <h6 class="text-muted">{{ $module }}</h6>
                <div class="row">
                  @foreach ($modulePermissions as $permission)
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->id }}"
                               class="form-check-input"
                               id="perm_{{ $permission->id }}"
                               {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                          {{ $permission->label }}
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-success">Update Permissions</button>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
