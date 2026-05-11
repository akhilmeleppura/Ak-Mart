@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product List - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-ecommerce-product-list.js'])
@endsection

@section('content')
<!-- Product List Widget -->
<div class="card mb-6">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">{{ __('In-store Sales') }}</p>
              <h4 class="mb-1">$5,345.43</h4>
              <p class="mb-0"><span class="me-2">5k {{ __('orders') }}</span><span class="badge bg-label-success">+5.7%</span></p>
            </div>
            <span class="avatar me-sm-6">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-store-alt icon-lg text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-6" />
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
            <div>
              <p class="mb-1">{{ __('Website Sales') }}</p>
              <h4 class="mb-1">$674,347.12</h4>
              <p class="mb-0"><span class="me-2">21k {{ __('orders') }}</span><span class="badge bg-label-success">+12.4%</span></p>
            </div>
            <span class="avatar p-2 me-lg-6">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-laptop icon-lg text-heading"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none" />
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
            <div>
              <p class="mb-1">{{ __('Discount') }}</p>
              <h4 class="mb-1">$14,235.12</h4>
              <p class="mb-0">6k {{ __('orders') }}</p>
            </div>
            <span class="avatar p-2 me-sm-6">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-gift icon-lg text-heading"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1">{{ __('Affiliate') }}</p>
              <h4 class="mb-1">$8,345.23</h4>
              <p class="mb-0"><span class="me-2">150 {{ __('orders') }}</span><span class="badge bg-label-danger">-3.5%</span></p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial rounded w-px-44 h-px-44">
                <i class="icon-base bx bx-wallet icon-lg text-heading"></i>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Product List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title">{{ __('Filter') }}</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0 g-md-6">
      <div class="col-md-3 product_status"></div>
      <div class="col-md-3 product_category"></div>
      <div class="col-md-3 product_stock"></div>
      <div class="col-md-3">
        <input type="text" class="form-control date-picker" placeholder="Filter by Date Range" id="dateRange" />
      </div>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-products table">
      <thead class="border-top">
        <tr>
          <th></th>
          <th></th>
          <th>{{ __('product') }}</th>
          <th>{{ __('category') }}</th>
          <th>{{ __('stock') }}</th>
          <th>{{ __('sku') }}</th>
          <th>{{ __('price') }}</th>
          <th>{{ __('qty') }}</th>
          <th>{{ __('Branch') }}</th>
          <th>{{ __('status') }}</th>
          <th>{{ __('actions') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection