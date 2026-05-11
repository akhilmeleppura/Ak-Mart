@extends('layouts/layoutMaster')

@section('title', 'Coupon Management - Ak Mart')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const dt_coupon_table = $('.datatables-coupons');
  let dt_coupons;

  if (dt_coupon_table.length) {
    dt_coupons = dt_coupon_table.DataTable({
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
        { data: 'id' },
        { data: 'code' },
        { data: 'type' },
        { data: 'value' },
        { data: 'usage' },
        { data: 'status' },
        { data: 'actions' }
      ],
      columnDefs: [
        {
          targets: 0,
          visible: false
        },
        {
          targets: 1,
          render: function(data) {
            return `<span class="fw-bold text-primary">${data}</span>`;
          }
        },
        {
          targets: 2,
          render: function(data) {
            return `<span class="badge bg-label-info text-capitalize">${data}</span>`;
          }
        },
        {
          targets: 3,
          render: function(data, type, full) {
            return full.type === 'percentage' ? data + '%' : '$' + data;
          }
        },
        {
          targets: 5,
          render: function(data) {
            const statusObj = {
              active: { title: 'Active', class: 'bg-label-success' },
              inactive: { title: 'Inactive', class: 'bg-label-danger' }
            };
            return `<span class="badge ${statusObj[data].class}">${statusObj[data].title}</span>`;
          }
        },
        {
          targets: -1,
          title: 'Actions',
          render: function(data, type, full) {
            return `
              <div class="d-flex align-items-center">
                <a href="javascript:;" class="text-body edit-coupon" data-id="${full.id}"><i class="icon-base bx bx-edit icon-md mx-2"></i></a>
                <a href="javascript:;" class="text-body delete-coupon text-danger" data-id="${full.id}"><i class="icon-base bx bx-trash icon-md mx-2"></i></a>
              </div>
            `;
          }
        }
      ],
      dom: '<"card-header d-flex flex-column flex-md-row align-items-start align-items-md-center pt-0"<"ms-n2"f><"d-flex align-items-md-center justify-content-md-end mt-2 mt-md-0 ms-auto"lB>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
        {
            extend: 'collection',
            className: 'btn btn-label-secondary dropdown-toggle me-3',
            text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export icon-xs"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
            buttons: [
                { extend: 'print', text: `<i class="icon-base bx bx-printer me-2"></i>Print`, className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5] } },
                { extend: 'csv', text: `<i class="icon-base bx bx-file me-2"></i>Csv`, className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5] } },
                { extend: 'excel', text: `<i class="icon-base bx bxs-file-export me-2"></i>Excel`, className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5] } },
                { extend: 'pdf', text: `<i class="icon-base bx bxs-file-pdf me-2"></i>Pdf`, className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5] } }
            ]
        },
        {
          text: '<i class="icon-base bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Coupon</span>',
          className: 'add-new btn btn-primary',
          attr: {
            'data-bs-toggle': 'modal',
            'data-bs-target': '#addCouponModal'
          }
        }
      ]
    });
  }

  // Initialize Flatpickr
  flatpickr('#dateRange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    onChange: function (selectedDates) {
      if (selectedDates.length === 2) {
        dt_coupons.ajax.reload();
      }
    }
  });

  // Handle Form Submit
  $('#addCouponForm').on('submit', function (e) {
    e.preventDefault();
    const id = $('#coupon_id').val();
    const url = id ? `${baseUrl}app/ecommerce/coupons/${id}` : `${baseUrl}app/ecommerce/coupons`;
    const method = id ? 'PUT' : 'POST';

    $.ajax({
      url: url,
      type: method,
      data: $(this).serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function (res) {
        $('#addCouponModal').modal('hide');
        dt_coupons.ajax.reload();
        Swal.fire({ icon: 'success', title: 'Success', text: res.success });
      },
      error: function (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: err.responseJSON.message || 'Error processing request' });
      }
    });
  });

  // Edit
  $(document).on('click', '.edit-coupon', function() {
    const id = $(this).data('id');
    $.get(`${baseUrl}app/ecommerce/coupons/${id}/edit`, function(data) {
      $('#coupon_id').val(data.id);
      $('#modalCouponCode').val(data.code);
      $('#modalCouponType').val(data.type);
      $('#modalCouponValue').val(data.value);
      $('#modalCouponLimit').val(data.usage_limit);
      $('#modalCouponStatus').val(data.is_active ? 1 : 0);
      $('.modal-title').text('Edit Coupon');
      $('#addCouponModal').modal('show');
    });
  });

  // Reset modal on close
  $('#addCouponModal').on('hidden.bs.modal', function() {
    $('#coupon_id').val('');
    $('#addCouponForm')[0].reset();
    $('.modal-title').text('Add New Coupon');
  });

  // Delete
  $(document).on('click', '.delete-coupon', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Are you sure?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' }
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: `${baseUrl}app/ecommerce/coupons/${id}`,
          type: 'DELETE',
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function (res) {
            dt_coupons.ajax.reload();
            Swal.fire({ icon: 'success', title: 'Deleted!', text: res.success });
          }
        });
      }
    });
  });
});
</script>
@endsection

@section('content')
<div class="card mb-6">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Coupons</h5>
    <div class="d-flex align-items-center gap-3">
        <div class="w-px-250">
            <input type="text" class="form-control date-picker" placeholder="Filter by Date Range" id="dateRange" />
        </div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-coupons table border-top">
      <thead>
        <tr>
          <th>ID</th>
          <th>Code</th>
          <th>Type</th>
          <th>Value</th>
          <th>Usage</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Add/Edit Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="modal-title">Add New Coupon</h3>
          <p>Create a discount coupon for your store</p>
        </div>
        <form id="addCouponForm" class="row g-3">
          <input type="hidden" id="coupon_id" name="id">
          <div class="col-12">
            <label class="form-label" for="modalCouponCode">Coupon Code</label>
            <input type="text" id="modalCouponCode" name="code" class="form-control" placeholder="SUMMER2026" required />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="modalCouponType">Discount Type</label>
            <select id="modalCouponType" name="type" class="form-select">
              <option value="fixed">Fixed Amount ($)</option>
              <option value="percentage">Percentage (%)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="modalCouponValue">Discount Value</label>
            <input type="number" id="modalCouponValue" name="value" class="form-control" placeholder="10" required />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="modalCouponLimit">Usage Limit</label>
            <input type="number" id="modalCouponLimit" name="usage_limit" class="form-control" placeholder="100" />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="modalCouponStatus">Status</label>
            <select id="modalCouponStatus" name="is_active" class="form-select">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
