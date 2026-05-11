@extends('layouts.layoutMaster')

@section('title', 'Edit Role')

@section('content')
<div class="row g-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Role</h5>
        <a href="{{ route('role.view') }}" class="btn btn-sm btn-outline-secondary">← Back to Role List</a>
      </div>

      <div class="card-body">
        <!-- Show Validation Errors -->
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('role.update', $role->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row g-3">
            <!-- Role Name -->
            <div class="col-md-6">
              <label for="name" class="form-label">Role Name</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}" required>
            </div>

            <!-- Role Status -->
            <div class="col-md-6">
              <label for="status" class="form-label">Status</label>
              <select name="status" id="status" class="form-select" required>
                <option value="1" {{ $role->status == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ $role->status == 0 ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary">Update Role</button>
            <a href="{{ route('role.view') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
