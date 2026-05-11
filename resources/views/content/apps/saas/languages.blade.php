@extends('layouts/layoutMaster')

@section('title', 'Global Language Management')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Supported Languages</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="w-px-250">
                        <input type="text" class="form-control date-picker" placeholder="Filter by Date Range" id="dateRange" />
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                        <i class="bx bx-plus me-1"></i> Add Language
                    </button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-languages table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>RTL</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Language</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addLanguageForm" action="{{ route('app-saas-languages-store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-4">
            <label class="form-label">Language Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Hindi" required>
          </div>
          <div class="mb-4">
            <label class="form-label">Language Code (ISO)</label>
            <input type="text" name="code" class="form-control" placeholder="e.g. hi" maxlength="5" required>
          </div>
          <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_rtl" value="1" id="is_rtl">
                <label class="form-check-label" for="is_rtl">Right-to-Left (RTL)</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Language</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function (e) {
  const dt_language_table = document.querySelector('.datatables-languages');

  if (dt_language_table) {
    const dt_languages = $(dt_language_table).DataTable({
      ajax: {
        url: window.location.href,
        data: function (d) {
          const dateRange = $('#dateRange').val();
          if (dateRange && dateRange.includes(' to ')) {
            const dates = dateRange.split(' to ');
            d.start_date = dates[0];
            d.end_date = dates[1];
          }
        }
      },
      columns: [
        { data: 'id', visible: false },
        { data: 'name' },
        { data: 'code' },
        { data: 'is_rtl' },
        { data: 'is_active' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          targets: 2,
          render: function (data) {
            return `<code class="fw-bold">${data}</code>`;
          }
        },
        {
          targets: 3,
          render: function (data) {
            return `<span class="badge bg-label-${data ? 'warning' : 'info'}">${data ? 'Yes' : 'No'}</span>`;
          }
        },
        {
          targets: 4,
          render: function (data) {
            const status = data ? 'success' : 'danger';
            const label = data ? 'Active' : 'Inactive';
            return `<span class="badge bg-label-${status}">${label}</span>`;
          }
        },
        {
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full) {
            return `
              <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-icon toggle-status" data-id="${data}"><i class="bx ${full['is_active'] ? 'bx-block' : 'bx-check'}"></i></button>
                <button class="btn btn-sm btn-icon delete-record text-danger" data-id="${data}"><i class="bx bx-trash"></i></button>
              </div>`;
          }
        }
      ],
      order: [1, 'asc'],
      layout: {
        topStart: {
            features: [
                {
                    search: {
                        placeholder: 'Search Language'
                    }
                }
            ]
        },
        topEnd: {
            features: [
                {
                    buttons: [
                        {
                            extend: 'collection',
                            className: 'btn btn-label-secondary dropdown-toggle',
                            text: '<i class="bx bx-export me-1"></i> Export',
                            buttons: [
                                { extend: 'print', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } },
                                { extend: 'csv', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } },
                                { extend: 'excel', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } },
                                { extend: 'pdf', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } }
                            ]
                        }
                    ]
                }
            ]
        }
      }
    });

    // Toggle Status
    $(document).on('click', '.toggle-status', function () {
      const id = $(this).data('id');
      $.post(`${baseUrl}app/saas/languages/toggle/${id}`, { _token: '{{ csrf_token() }}' }, function (res) {
        dt_languages.ajax.reload();
        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: 'Language status updated successfully.',
          customClass: { confirmButton: 'btn btn-success' }
        });
      });
    });

    // Delete
    $(document).on('click', '.delete-record', function () {
      const id = $(this).data('id');
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
            url: `${baseUrl}app/saas/languages/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
              dt_languages.ajax.reload();
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Language has been deleted.',
                customClass: { confirmButton: 'btn btn-success' }
              });
            }
          });
        }
      });
    });

    // Flatpickr
    flatpickr('#dateRange', {
      mode: 'range',
      dateFormat: 'Y-m-d',
      onChange: function (selectedDates) {
        if (selectedDates.length === 2) {
          dt_languages.ajax.reload();
        }
      }
    });
  }
});
</script>
@endsection
