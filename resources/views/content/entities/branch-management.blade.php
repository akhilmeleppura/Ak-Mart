@extends('layouts/layoutMaster')

@section('title', 'Branch Management - Crud App')

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
 * Generic Data-Table Handler for Branches
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (function () {
    if (typeof window.dataTableConfig === 'undefined') {
      console.error('dataTableConfig is not defined.');
      return;
    }

    const { ajaxUrl, actionsRoutePrefix, entityName, offcanvasId, formId, columns, permissions } = window.dataTableConfig;

    const dt_table = $('.datatables-users');
    const form = document.getElementById(formId);
    const offcanvasElement = document.querySelector(offcanvasId);
    const offcanvasTitle = offcanvasElement.querySelector('.offcanvas-title');
    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
    const logoPreview = document.getElementById('logo-preview');

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    const setFormState = (isReadOnly) => {
        form.querySelectorAll('input, select, textarea').forEach(el => el.disabled = isReadOnly);
        form.querySelector('.data-submit').style.display = isReadOnly ? 'none' : 'block';
    };

    let dynamicColumns = [
      { data: '', name: 'responsive', orderable: false, searchable: false, render: () => '' },
      { data: 'id', title: 'ID', name: 'id' }
    ];

    for (const key in columns) {
      if (key !== 'id') {
        dynamicColumns.push({ data: key, name: key, title: columns[key].title, className: columns[key].className || '', responsivePriority: columns[key].responsivePriority || 1000 });
      }
    }

    if (permissions.canView || permissions.canEdit || permissions.canDelete) {
      dynamicColumns.push({
        data: 'action', name: 'action', title: 'Actions', orderable: false, searchable: false,
        render: function (data, type, full, meta) {
          const id = full.id;
          let actions = '<div class="d-inline-block">';
          if (permissions.canView) actions += `<a href="javascript:;" class="btn btn-icon item-view" data-id="${id}"><i class="bx bx-show"></i></a>`;
          if (permissions.canEdit) actions += `<a href="javascript:;" class="btn btn-icon item-edit" data-id="${id}"><i class="bx bxs-edit"></i></a>`;
          if (permissions.canDelete) actions += `<button class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button><div class="dropdown-menu dropdown-menu-end"><a href="javascript:;" class="dropdown-item text-danger delete-record" data-id="${id}">Delete</a></div>`;
          return actions + '</div>';
        }
      });
    }

    if (dt_table.length) {
      var dt_branch = dt_table.DataTable({
        processing: true, serverSide: true, ajax: ajaxUrl, columns: dynamicColumns,
        dom: '<"row mx-2"<"col-md-2"<"me-3"l>><"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { sLengthMenu: '_MENU_', search: '', searchPlaceholder: 'Search..' },
        buttons: [
          {
            extend: 'collection', className: 'btn btn-label-secondary dropdown-toggle mx-3', text: '<i class="bx bx-export me-1"></i>Export',
            buttons: [
              { extend: 'print', text: '<i class="bx bx-printer me-2" ></i>Print', className: 'dropdown-item' },
              { extend: 'csv', text: '<i class="bx bx-file me-2" ></i>Csv', className: 'dropdown-item' },
              { extend: 'excel', text: '<i class="bx bxs-file-excel me-2"></i>Excel', className: 'dropdown-item' },
              { extend: 'pdf', text: '<i class="bx bxs-file-pdf me-2"></i>Pdf', className: 'dropdown-item' },
              { extend: 'copy', text: '<i class="bx bx-copy me-2" ></i>Copy', className: 'dropdown-item' }
            ]
          },
          ...(permissions.canAdd ? [{ text: `<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New ${entityName}</span>`, className: 'add-new btn btn-primary', attr: { 'data-bs-toggle': 'offcanvas', 'data-bs-target': offcanvasId } }] : [])
        ],
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({ header: (row) => 'Details of ' + (row.data().name || entityName) }),
            type: 'column',
            renderer: (api, rowIdx, columns) => {
              var data = $.map(columns, (col, i) => col.title ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}"><td>${col.title}:</td> <td>${col.data}</td></tr>` : '').join('');
              return data ? $('<table class="table"/><tbody />').append(data) : false;
            }
          }
        }
      });
    }

    $('.add-new').on('click', function () {
      form.reset();
      offcanvasTitle.innerHTML = `Add New ${entityName}`;
      $('#branch_id').val('');
      logoPreview.src = '';
      logoPreview.style.display = 'none';
      setFormState(false);
      offcanvas.show();
    });

    $(form).on('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      $.ajax({
        url: `${actionsRoutePrefix}-store`, type: 'POST', data: formData,
        processData: false, contentType: false,
        success: function (response) {
          if (response.success) {
            offcanvas.hide();
            dt_branch.draw();
            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, customClass: { confirmButton: 'btn btn-success' } });
          }
        },
        error: function (xhr) {
          let errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>') || 'An error occurred.';
          Swal.fire({ icon: 'error', title: 'Error!', html: errorMsg, customClass: { confirmButton: 'btn btn-danger' } });
        }
      });
    });


    // --- *** CORRECTED CODE BLOCK STARTS HERE *** ---
    // This function now explicitly sets each field by its ID for reliability.
    const populateForm = (data, isViewMode) => {
        form.reset();
        offcanvasTitle.innerHTML = `${isViewMode ? 'View' : 'Edit'} ${entityName} Details`;
        setFormState(isViewMode);

        // Explicitly set values using jQuery and element IDs
        $('#branch_id').val(data.id);
        $('#add-branch-name').val(data.name);
        $('#add-branch-address').val(data.address);

        // Handle the logo preview
        if (data.logo) {
            logoPreview.src = data.logo;
            logoPreview.style.display = 'block';
        } else {
            logoPreview.src = '';
            logoPreview.style.display = 'none';
        }
        offcanvas.show();
    };

    // The click handler remains the same, but now calls the corrected function
    $(document).on('click', '.item-edit, .item-view', function () {
      const id = $(this).data('id');
      const isViewMode = $(this).hasClass('item-view');
      $.ajax({
        url: `${actionsRoutePrefix}-edit/${id}`,
        type: 'GET',
        success: function(data) {
          populateForm(data, isViewMode);
        }
      });
    });
    // --- *** CORRECTED CODE BLOCK ENDS HERE *** ---


    $(document).on('click', '.delete-record', function () {
      const id = $(this).data('id');
      Swal.fire({
        title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it!', customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' }, buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `${actionsRoutePrefix}-delete/${id}`, type: 'DELETE',
            success: function (response) {
              if (response.success) {
                dt_branch.draw();
                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, customClass: { confirmButton: 'btn btn-success' } });
              }
            }
          });
        }
      });
    });

    offcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
      form.reset();
      setFormState(false);
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
<!-- Branch Statistics -->
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Branches</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$totalBranches}}</h4>
            </div>
            <small class="mb-0">All registered branches</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
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
            <span class="text-heading">Assigned to Companies</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$branchesWithCompanies}}</h4>
            </div>
            <small class="mb-0">Branches with a company</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
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
            <span class="text-heading">Unassigned</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{$branchesWithoutCompanies}}</h4>
            </div>
            <small class="mb-0">Branches without a company</small>
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

<!-- Branch List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Search Filter</h5>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-users table border-top">
      <thead>
        <tr>
            <th></th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Offcanvas to add/edit branch -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddBranch" aria-labelledby="offcanvasAddBranchLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddBranchLabel" class="offcanvas-title">Add Branch</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
      <form class="add-new-branch-form pt-0" id="addNewBranchForm">
        {{-- This hidden input is crucial for updates. Its value is set by JS. --}}
        <input type="hidden" name="id" id="branch_id">
        <div class="mb-3">
          <label class="form-label" for="add-branch-name">Branch Name</label>
          <input type="text" class="form-control" id="add-branch-name" placeholder="Main Street Branch" name="name" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-branch-address">Address</label>
          <textarea class="form-control" id="add-branch-address" name="address" rows="3" placeholder="123 Main St, Anytown, USA"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-branch-logo">Branch Logo</label>
          <input class="form-control" type="file" id="add-branch-logo" name="logo">
        </div>
        <div class="mb-3">
            <img id="logo-preview" src="#" alt="Logo Preview" class="img-fluid rounded" style="display:none; max-height: 150px;"/>
        </div>
        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>
    </div>
  </div>
</div>
@endsection