@extends('layouts/layoutMaster')

@section('title', 'eCommerce Order List - Apps')

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

@section('page-script')
@vite(['resources/assets/js/app-ecommerce-order-list.js'])
@endsection

@section('content')
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">Pending Payment</p>
              <h4 class="mb-1">{{ $pendingPayment }}</h4>
              <p class="mb-0"><span class="me-2">Orders awaiting payment</span></p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-calendar icon-lg text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6" />
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">Completed</p>
              <h4 class="mb-1">{{ $completed }}</h4>
              <p class="mb-0"><span class="me-2">Successful orders</span></p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-check-double icon-lg text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none" />
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <p class="mb-1">Refunded</p>
              <h4 class="mb-1">{{ $refunded }}</h4>
              <p class="mb-0">Processed refunds</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-refresh icon-lg text-heading"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1">Failed</p>
              <h4 class="mb-1">{{ $failed }}</h4>
              <p class="mb-0"><span class="me-2">Cancelled/Failed</span></p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-error-circle icon-lg text-heading"></i>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Order List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
      <div class="col-md-6">
        <h5 class="card-title mb-0">Orders</h5>
      </div>
      <div class="col-md-6">
        <div class="d-flex justify-content-end align-items-center gap-4">
          <div class="w-px-250">
            <input type="text" class="form-control date-picker" placeholder="Filter by Date Range" id="dateRange" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-order table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>order</th>
          <th>date</th>
          <th>customers</th>
          <th>payment</th>
          <th>status</th>
          <th>method</th>
          <th>actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection
