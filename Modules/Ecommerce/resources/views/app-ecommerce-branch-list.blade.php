@extends('layouts/layoutMaster')

@section('title', 'eCommerce Branch Management - Apps')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')
<div class="app-ecommerce-branch">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">Branch Management</h4>
    <button class="btn btn-primary" id="btnAddBranch" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBranchAdd">Add Branch</button>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <!-- Branch List Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Name</th>
              <th>Code</th>
              <th>Address</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($branches as $branch)
            <tr>
              <td>{{ $branch->name }}</td>
              <td><span class="badge bg-label-primary">{{ $branch->code ?? 'N/A' }}</span></td>
              <td>{{ $branch->address }}</td>
              <td><span class="badge bg-label-success">Active</span></td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item edit-branch" href="javascript:void(0);" data-id="{{ $branch->id }}"><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
                    <a class="dropdown-item delete-branch" href="javascript:void(0);" data-id="{{ $branch->id }}"><i class="icon-base bx bx-trash me-1"></i> Delete</a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Offcanvas to add/edit branch -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBranchAdd" aria-labelledby="offcanvasBranchAddLabel">
    <div class="offcanvas-header py-6">
      <h5 id="offcanvasBranchAddLabel" class="offcanvas-title">Add Branch</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body border-top">
      <form id="branchForm" class="pt-0" method="POST" action="{{ route('app-ecommerce-branch-store') }}">
        @csrf
        <input type="hidden" name="_method" id="methodField" value="POST">
        <div class="mb-6">
          <label class="form-label" for="branch-name">Branch Name</label>
          <input type="text" class="form-control" id="branch-name" placeholder="Main Store" name="name" required />
        </div>
        <div class="mb-6">
          <label class="form-label" for="branch-code">Branch Code (Unique)</label>
          <input type="text" class="form-control" id="branch-code" placeholder="NY-01" name="code" required />
          <small class="text-muted">Example: NY-01, LON-05</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="branch-address">Address</label>
          <textarea class="form-control" id="branch-address" name="address" rows="3" placeholder="123 Street, City" required></textarea>
        </div>
        <div class="mb-6">
          <button type="submit" class="btn btn-primary me-sm-3 me-1" id="btnSubmitBranch">Add Branch</button>
          <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Discard</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const offcanvasElement = document.getElementById('offcanvasBranchAdd');
  const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
  const form = document.getElementById('branchForm');
  const methodField = document.getElementById('methodField');
  const label = document.getElementById('offcanvasBranchAddLabel');
  const btnSubmit = document.getElementById('btnSubmitBranch');

  // Reset form for Add
  document.getElementById('btnAddBranch').addEventListener('click', function() {
    form.reset();
    form.action = "{{ route('app-ecommerce-branch-store') }}";
    methodField.value = "POST";
    label.innerText = "Add Branch";
    btnSubmit.innerText = "Add Branch";
  });

  // Load data for Edit
  $('.edit-branch').on('click', function() {
    const id = $(this).data('id');
    $.get(`${baseUrl}app/ecommerce/branch/${id}/edit`, function(data) {
      form.action = `${baseUrl}app/ecommerce/branch/${id}`;
      methodField.value = "PUT";
      label.innerText = "Edit Branch";
      btnSubmit.innerText = "Update Branch";
      
      document.getElementById('branch-name').value = data.name;
      document.getElementById('branch-code').value = data.code;
      document.getElementById('branch-address').value = data.address;
      
      offcanvas.show();
    });
  });

  // Handle form submission with AJAX
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(form);
    const action = form.action;
    const method = methodField.value;

    $.ajax({
      url: action,
      type: method === 'PUT' ? 'POST' : method,
      data: $(form).serialize(),
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(result) {
        offcanvas.hide();
        Swal.fire({
          icon: 'success',
          title: result.success ? 'Success!' : 'Updated!',
          text: result.message || 'Branch saved successfully.',
          customClass: { confirmButton: 'btn btn-success' }
        }).then(() => {
          location.reload(); // Reload to see changes in the simple table
        });
      },
      error: function(error) {
        Swal.fire({
          title: 'Error!',
          text: error.responseJSON.message || 'Error saving branch',
          icon: 'error',
          customClass: { confirmButton: 'btn btn-primary' }
        });
      }
    });
  });

  // Delete logic
  $('.delete-branch').on('click', function() {
    const id = $(this).data('id');
    const row = $(this).closest('tr');
    
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: `${baseUrl}app/ecommerce/branch/${id}`,
          type: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(result) {
            row.remove();
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: result.message,
              customClass: { confirmButton: 'btn btn-success' }
            });
          },
          error: function(error) {
            Swal.fire({
              title: 'Error!',
              text: error.responseJSON.message || 'Error deleting branch',
              icon: 'error',
              customClass: { confirmButton: 'btn btn-primary' }
            });
          }
        });
      }
    });
  });
});
</script>
@endsection
