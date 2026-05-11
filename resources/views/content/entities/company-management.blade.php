@extends('layouts/layoutMaster')

@section('title', 'Company Management - Crud App')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
// --- Scripts for Export Buttons ---
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.html5.js',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.print.js',
'resources/assets/vendor/libs/jszip/jszip.js',
'resources/assets/vendor/libs/pdfmake/pdfmake.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
<script>
/**
 * Generic Data-Table Handler
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (function () {
    // Check if the dataTableConfig is defined
    if (typeof window.dataTableConfig === 'undefined') {
      console.error('dataTableConfig is not defined. Make sure to pass it from your Blade view.');
      return;
    }

    // Deconstructing for easier access
    const { ajaxUrl, actionsRoutePrefix, entityName, offcanvasId, formId, columns, permissions } = window.dataTableConfig;

    const dt_user_table = $('.datatables-users');
    const form = document.getElementById(formId);
    const offcanvasElement = document.querySelector(offcanvasId);
    const offcanvasTitle = offcanvasElement.querySelector('.offcanvas-title');
    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);

    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Function to set form fields to read-only or editable
    const setFormState = (isReadOnly) => {
        const formElements = form.querySelectorAll('input, select, textarea');
        formElements.forEach(element => {
            element.disabled = isReadOnly;
        });
        form.querySelector('.data-submit').style.display = isReadOnly ? 'none' : 'block';
    };


    // Build the columns dynamically
    let dynamicColumns = [
      {
        // For Responsive
        data: '',
        name: 'responsive',
        orderable: false,
        searchable: false,
        render: function (data, type, full, meta) {
          return '';
        }
      },
      {
        data: 'id',
        title: 'ID',
        name: 'id'
      }
    ];

    for (const key in columns) {
      if (key !== 'id') { // ID is already added
        dynamicColumns.push({
          data: key,
          name: key,
          title: columns[key].title,
          className: columns[key].className || '',
          responsivePriority: columns[key].responsivePriority || 1000
        });
      }
    }

    // Add Actions column if any permission is true
    if (permissions.canView || permissions.canEdit || permissions.canDelete) {
      dynamicColumns.push({
        data: 'action',
        name: 'action',
        title: 'Actions',
        orderable: false,
        searchable: false,
        render: function (data, type, full, meta) {
          const id = full.id;
          let actions = '<div class="d-inline-block">';

          // View Button - REMOVED btn-sm
          if (permissions.canView) {
            actions += `<a href="javascript:;" class="btn btn-icon item-view" data-id="${id}"><i class="bx bx-show"></i></a>`;
          }

          // Edit Button - REMOVED btn-sm
          if (permissions.canEdit) {
            actions += `<a href="javascript:;" class="btn btn-icon item-edit" data-id="${id}"><i class="bx bxs-edit"></i></a>`;
          }

          // Delete Button - REMOVED btn-sm
          if (permissions.canDelete) {
            actions += `<button class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;" class="dropdown-item text-danger delete-record" data-id="${id}">Delete</a>
                        </div>`;
          }

          actions += '</div>';
          return actions;
        }
      });
    }

    // Initialize DataTable
    if (dt_user_table.length) {
      var dt_user = dt_user_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: dynamicColumns,
        dom:
          '<"row mx-2"' +
          '<"col-md-2"<"me-3"l>>' +
          '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
          '>t' +
          '<"row mx-2"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',
        language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Search..'
        },
        // --- UPDATED: Added Export button and configured Add button ---
        buttons: [
          {
            extend: 'collection',
            className: 'btn btn-label-secondary dropdown-toggle mx-3',
            text: '<i class="bx bx-export me-1"></i>Export',
            buttons: [
              {
                extend: 'print',
                text: '<i class="bx bx-printer me-2" ></i>Print',
                className: 'dropdown-item',
              },
              {
                extend: 'csv',
                text: '<i class="bx bx-file me-2" ></i>Csv',
                className: 'dropdown-item',
              },
              {
                extend: 'excel',
                text: '<i class="bx bxs-file-excel me-2"></i>Excel',
                className: 'dropdown-item',
              },
              {
                extend: 'pdf',
                text: '<i class="bx bxs-file-pdf me-2"></i>Pdf',
                className: 'dropdown-item',
              },
              {
                extend: 'copy',
                text: '<i class="bx bx-copy me-2" ></i>Copy',
                className: 'dropdown-item',
              }
            ]
          },
          ...(permissions.canAdd ? [{
            text: `<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New ${entityName}</span>`,
            className: 'add-new btn btn-primary',
            attr: {
              'data-bs-toggle': 'offcanvas',
              'data-bs-target': offcanvasId
            }
          }] : [])
        ],
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details of ' + (data.name || entityName);
              }
            }),
            type: 'column',
            renderer: function (api, rowIdx, columns) {
              var data = $.map(columns, function (col, i) {
                return col.title !== ''
                  ? '<tr data-dt-row="' +
                      col.rowIndex +
                      '" data-dt-column="' +
                      col.columnIndex +
                      '">' +
                      '<td>' +
                      col.title +
                      ':' +
                      '</td> ' +
                      '<td>' +
                      col.data +
                      '</td>' +
                      '</tr>'
                  : '';
              }).join('');

              return data ? $('<table class="table"/><tbody />').append(data) : false;
            }
          }
        }
      });
    }

    // Handle Add new button click
    $('.add-new').on('click', function () {
      form.reset();
      offcanvasTitle.innerHTML = `Add New ${entityName}`;
      $('#company_id').val(''); // Use a more generic ID if possible, or handle it
      setFormState(false); // Make form editable
      offcanvas.show();
    });

    // --- All other JavaScript functions (submit, edit, view, delete) remain unchanged ---

    // Handle form submission
    $(form).on('submit', function (e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: `${actionsRoutePrefix}-store`,
        type: 'POST',
        data: formData,
        success: function (response) {
          if (response.success) {
            offcanvas.hide();
            dt_user.draw();
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message,
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          }
        },
        error: function (xhr) {
          let errorMsg = 'An error occurred. Please try again.';
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
          }
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            html: errorMsg,
            customClass: {
              confirmButton: 'btn btn-danger'
            }
          });
        }
      });
    });

    // Handle Edit button click
    $(document).on('click', '.item-edit', function () {
      const id = $(this).data('id');
      $.ajax({
        url: `${actionsRoutePrefix}-edit/${id}`,
        type: 'GET',
        success: function (data) {
          form.reset();
          offcanvasTitle.innerHTML = `Edit ${entityName}`;
          setFormState(false); // Make form editable

          // Populate the form
          Object.keys(data).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
              if (key === 'id') {
                $('#company_id').val(data[key]);
              } else {
                field.value = data[key];
              }
            }
          });
          offcanvas.show();
        }
      });
    });

    // Handle View button click
    $(document).on('click', '.item-view', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `${actionsRoutePrefix}-edit/${id}`,
            type: 'GET',
            success: function(data) {
                form.reset();
                offcanvasTitle.innerHTML = `View ${entityName} Details`;
                setFormState(true); // Make form read-only

                // Populate the form
                Object.keys(data).forEach(key => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field) {
                        if (key === 'id') {
                           $('#company_id').val(data[key]);
                        } else {
                           field.value = data[key];
                        }
                    }
                });
                offcanvas.show();
            }
        });
    });

    // Handle Delete button click
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
        if (result.isConfirmed) {
          $.ajax({
            url: `${actionsRoutePrefix}-delete/${id}`,
            type: 'DELETE',
            success: function (response) {
              if (response.success) {
                dt_user.draw();
                Swal.fire({
                  icon: 'success',
                  title: 'Deleted!',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
              }
            }
          });
        }
      });
    });

    // Handle Offcanvas hide event
    offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
      form.reset();
      setFormState(false); // Reset to editable state when closed
    });
  })();
});
</script>
@endsection


<script>
    // Pass the configuration object from your controller to the JavaScript file
    window.dataTableConfig = @json($dataTableConfig);
</script>


@section('content')
<!-- Company Statistics -->
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Companies</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$totalCompany}}</h4>
            </div>
            <small class="mb-0">All registered companies</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base bx bx-buildings icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Assigned to Branch</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$companiesWithBranch}}</h4>
            </div>
            <small class="mb-0">Companies with a branch</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base bx bx-git-branch icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Unassigned</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$companiesWithoutBranch}}</h4>
            </div>
            <small class="mb-0">Companies without a branch</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base bx bx-error-circle icon-lg"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Company List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Search Filter</h5>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-users table border-top">
      <thead>
        {{-- The generic data-table.js will build the table header dynamically --}}
        <tr>
            <th></th>
            {{-- Leave this empty, JS will populate it --}}
        </tr>
      </thead>
    </table>
  </div>

  <!-- Offcanvas to add/edit company -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddCompany" aria-labelledby="offcanvasAddCompanyLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddCompanyLabel" class="offcanvas-title">Add Company</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
      <form class="add-new-company-form pt-0" id="addNewCompanyForm">
        {{-- CSRF token is added via AJAX setup in data-table.js --}}
        <input type="hidden" name="id" id="company_id">
        <div class="mb-3">
          <label class="form-label" for="add-company-name">Company Name</label>
          <input type="text" class="form-control" id="add-company-name" placeholder="Acme Corporation" name="name" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-company-branch">Branch</label>
          <select id="add-company-branch" class="form-select" name="branch_id">
            <option value="">Select Branch</option>
            @foreach($branches as $branch)
            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>
    </div>
  </div>
</div>
@endsection