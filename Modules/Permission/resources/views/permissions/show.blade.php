@extends('layouts.layoutMaster')
@section('title', 'View Role Permissions')

@section('content')
<div class="row g-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Permissions for Role: <strong>{{ $role->name }}</strong></h5>
      </div>
      <div class="card-body">
        @foreach ($permissions as $module => $modulePermissions)
          <div class="mb-4">
            <h6 class="text-muted">{{ $module }}</h6>
            <div class="row">
              @foreach ($modulePermissions as $permission)
                <div class="col-md-4 mb-2">
                  <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center border border-success rounded-circle me-2" style="width: 20px; height: 20px;">
                      <span class="text-success" style="font-size: 12px;">✓</span>
                    </span>
                    <label>{{ $permission->label }}</label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
      <div class="card-footer text-end">
        <a href="{{ route('permissions.edit', $role->id) }}" class="btn btn-primary">Edit Permissions</a>
      </div>
    </div>
  </div>
</div>
@endsection