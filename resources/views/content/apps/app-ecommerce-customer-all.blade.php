@extends('layouts/layoutMaster')

@section('title', 'Store Customers Management - AK-Mart')

@section('vendor-style')
@vite([
  'resources/assets/vendor/fonts/flag-icons.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/app-ecommerce-customer-all.js')
@endsection

@section('content')
<!-- Customer Management Header (Type 1: E-Commerce Buyer Design) -->
<div class="card mb-6 border-0 shadow-xs bg-label-success bg-opacity-10">
  <div class="card-body py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg bg-teal text-white rounded-3 d-flex align-items-center justify-content-center" style="background-color: #14B8A6;">
        <i class="bx bx-shopping-bag fs-2 text-white"></i>
      </div>
      <div>
        <h4 class="mb-1 text-heading fw-bold">Store Customers Directory</h4>
        <p class="mb-0 text-muted small">Manage retail buyers, wholesale clients, shipping addresses, and customer lifetime order spend.</p>
    </div>
  </div>
</div>

<!-- Customers List Table -->
<div class="card mb-6">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0 fw-semibold text-heading"><i class="bx bx-group me-2 text-teal" style="color: #14B8A6;"></i>Customers List</h5>
    <div class="d-flex align-items-center gap-3">
        <div class="w-px-250">
            <input type="text" class="form-control date-picker" placeholder="Filter by Date Range" id="dateRange" />
        </div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-customers table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>Customer</th>
          <th class="text-nowrap">Customer Id</th>
          <th>Country</th>
          <th>Order</th>
          <th class="text-nowrap">Total Spent</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Type 1 Offcanvas Form: Add Store Customer Form -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCustomerAdd"
    aria-labelledby="offcanvasEcommerceCustomerAddLabel">
    <div class="offcanvas-header border-bottom bg-light py-3">
      <div class="d-flex align-items-center gap-2">
        <i class="bx bx-cart text-teal fs-4" style="color: #14B8A6;"></i>
        <h5 id="offcanvasEcommerceCustomerAddLabel" class="offcanvas-title fw-bold mb-0">Add Store Customer</h5>
      </div>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body border-top mx-0 flex-grow-0">
      <form class="ecommerce-customer-add pt-0" id="eCommerceCustomerAddForm" onsubmit="return false">
        <input type="hidden" name="id" id="customerId">
        
        <div class="ecommerce-customer-add-basic mb-4">
          <h6 class="mb-3 text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px;">1. Basic Customer Info</h6>
          <div class="mb-3 form-control-validation">
            <label class="form-label fw-semibold" for="ecommerce-customer-add-name">Customer Full Name*</label>
            <input type="text" class="form-control" id="ecommerce-customer-add-name" placeholder="John Doe"
              name="customerName" required />
          </div>
          <div class="mb-3 form-control-validation">
            <label class="form-label fw-semibold" for="ecommerce-customer-add-email">Email Address*</label>
            <input type="email" id="ecommerce-customer-add-email" class="form-control" placeholder="john.doe@example.com"
              name="customerEmail" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" for="ecommerce-customer-add-contact">Mobile Phone Number</label>
            <input type="text" id="ecommerce-customer-add-contact" class="form-control phone-mask"
              placeholder="+1 (123) 456-7890" name="customerContact" />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" for="customer-group">Customer Group</label>
            <select id="customer-group" class="form-select">
              <option value="retail">Retail Buyer</option>
              <option value="wholesale">Wholesale Buyer</option>
              <option value="vip">VIP Member</option>
            </select>
          </div>
        </div>

        <div class="ecommerce-customer-add-shiping mb-6 pt-3 border-top">
          <h6 class="mb-3 text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px;">2. Default Shipping Address</h6>
          <div class="mb-3">
            <label class="form-label" for="ecommerce-customer-add-address">Street Address Line 1</label>
            <input type="text" id="ecommerce-customer-add-address" class="form-control" placeholder="45 Roker Terrace"
              name="customerAddress1" />
          </div>
          <div class="mb-3">
            <label class="form-label" for="ecommerce-customer-add-town">City / Town</label>
            <input type="text" id="ecommerce-customer-add-town" class="form-control" placeholder="New York"
              name="customerTown" />
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label" for="ecommerce-customer-add-state">State / Province</label>
              <input type="text" id="ecommerce-customer-add-state" class="form-control" placeholder="NY"
                name="customerState" />
            </div>
            <div class="col-6">
              <label class="form-label" for="ecommerce-customer-add-post-code">Postal Code</label>
              <input type="text" id="ecommerce-customer-add-post-code" class="form-control" placeholder="10001"
                name="pin" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="ecommerce-customer-add-country">Country</label>
            <select id="ecommerce-customer-add-country" class="select2 form-select">
              <option value="United States">United States</option>
              <option value="United Kingdom">United Kingdom</option>
              <option value="Canada">Canada</option>
              <option value="France">France</option>
              <option value="Germany">Germany</option>
              <option value="United Arab Emirates">United Arab Emirates</option>
            </select>
          </div>
        </div>

        <div class="d-flex gap-2 pt-3 border-top">
          <button type="submit" class="btn btn-teal text-white me-2 data-submit" style="background-color: #14B8A6; border-color: #14B8A6;">Save Customer</button>
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection