@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Cards Advance- UI elements')

@section('vendor-style')
  @vite(['resources/assets/vendor/fonts/flag-icons.scss', 'resources/assets/vendor/libs/apex-charts/apex-charts.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/cards-advance.js', 'resources/assets/js/modal-add-new-cc.js'])
@endsection

@section('content')
  <div class="row g-6">
    <!-- Employee List -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Employee List</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="employeeList" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="employeeList">
              <a class="dropdown-item" href="javascript:void(0);">Featured Employees</a>
              <a class="dropdown-item" href="javascript:void(0);">Based on Task</a>
              <a class="dropdown-item" href="javascript:void(0);">See All</a>
            </div>
          </div>
        </div>
        <div class="card-body pt-4">
          <ul class="p-0 m-0">
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/avatars/20.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 align-items-center gap-2">
                <div class="d-flex justify-content-between flex-grow-1 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-normal">Alberta</h6>
                    <small>UI Designer</small>
                  </div>

                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0 fw-normal">100h:</h6>
                    <span class="text-body-secondary">138h</span>
                  </div>
                </div>

                <div class="chart-progress" data-color="secondary" data-series="85"></div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/avatars/3.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 align-items-center gap-2">
                <div class="d-flex justify-content-between flex-grow-1 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-normal">Paul</h6>
                    <small>Branding</small>
                  </div>

                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0 fw-normal">121h:</h6>
                    <span class="text-body-secondary">109h</span>
                  </div>
                </div>

                <div class="chart-progress" data-color="warning" data-series="70"></div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/avatars/15.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 align-items-center gap-2">
                <div class="d-flex justify-content-between flex-grow-1 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-normal">Nannie</h6>
                    <small>iOS Developer</small>
                  </div>

                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0 fw-normal">112h:</h6>
                    <span class="text-body-secondary">160h</span>
                  </div>
                </div>

                <div class="chart-progress" data-color="primary" data-series="25"></div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/avatars/14.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 align-items-center gap-2">
                <div class="d-flex justify-content-between flex-grow-1 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-normal">Rodney</h6>
                    <small>iOS Developer</small>
                  </div>

                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0 fw-normal">125h:</h6>
                    <span class="text-body-secondary">166h</span>
                  </div>
                </div>

                <div class="chart-progress" data-color="danger" data-series="75"></div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/avatars/7.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 align-items-center gap-2">
                <div class="d-flex justify-content-between flex-grow-1 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-normal">Martin</h6>
                    <small>Product Designer</small>
                  </div>

                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0 fw-normal">76h:</h6>
                    <span class="text-body-secondary">89h</span>
                  </div>
                </div>

                <div class="chart-progress" data-color="info" data-series="60"></div>
              </div>
            </li>
            <li class="d-flex">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/avatars/18.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 align-items-center gap-2">
                <div class="d-flex justify-content-between flex-grow-1 flex-wrap">
                  <div>
                    <h6 class="mb-0 fw-normal">Nancy</h6>
                    <small>PHP Developer</small>
                  </div>

                  <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0 fw-normal">22h:</h6>
                    <span class="text-body-secondary">45h</span>
                  </div>
                </div>

                <div class="chart-progress" data-color="warning" data-series="45"></div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Employee List -->
    <!-- Transactions -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Transactions</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
        <div class="card-body pt-4">
          <ul class="p-0 m-0">
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block">Paypal</small>
                  <h6 class="fw-normal mb-0">Send money</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-2">
                  <h6 class="fw-normal mb-0">+82.6</h6>
                  <span class="text-body-secondary">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block">Wallet</small>
                  <h6 class="fw-normal mb-0">Mac'D</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-2">
                  <h6 class="fw-normal mb-0">+270.69</h6>
                  <span class="text-body-secondary">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/chart.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block">Transfer</small>
                  <h6 class="fw-normal mb-0">Refund</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-2">
                  <h6 class="fw-normal mb-0">+637.91</h6>
                  <span class="text-body-secondary">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/cc-primary.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block">Credit Card</small>
                  <h6 class="fw-normal mb-0">Ordered Food</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-2">
                  <h6 class="fw-normal mb-0">-838.71</h6>
                  <span class="text-body-secondary">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block">Wallet</small>
                  <h6 class="fw-normal mb-0">Starbucks</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-2">
                  <h6 class="fw-normal mb-0">+203.33</h6>
                  <span class="text-body-secondary">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/cc-warning.png') }}" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block">Mastercard</small>
                  <h6 class="fw-normal mb-0">Ordered Food</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-2">
                  <h6 class="fw-normal mb-0">-92.45</h6>
                  <span class="text-body-secondary">USD</span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Transactions -->
    <!-- Shared Event -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header flex-grow-0">
          <div class="d-flex align-items-center">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('assets/img/avatars/20.png') }}" alt="User" class="rounded-circle" />
            </div>
            <div class="d-flex w-100 flex-wrap justify-content-between gap-1">
              <div class="me-2">
                <h5 class="mb-1">Olivia Shared Event</h5>
                <small>07 Sep 2020 at 10:30 AM</small>
              </div>
              <div class="dropdown">
                <button class="btn text-body-secondary p-0" type="button" id="oliviaShared" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="oliviaShared">
                  <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <img class="object-fit-cover" src="{{ asset('assets/img/backgrounds/event.jpg') }}" alt="Card image cap" />
        <div class="mt-n8">
          <span class="featured-date d-inline-block ms-6 rounded shadow-lg text-center py-1 px-4">
            <span class="mb-0 h4">12</span><br />
            <span class="text-primary">Dec</span>
          </span>
        </div>
        <div class="card-body">
          <h5 class="text-truncate mb-2">How To Excel In A Technical Terminology?</h5>
          <div class="d-flex gap-2">
            <span class="badge bg-label-success">Technical</span>
            <span class="badge bg-label-primary">Excel</span>
            <span class="badge bg-label-info">Account</span>
          </div>
          <div class="d-flex my-6">
            <ul class="list-unstyled m-0 d-flex align-items-center avatar-group">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Vinnie Mostowy"
                class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar" />
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Allen Rieske"
                class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar" />
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Julee Rossignol"
                class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar" />
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Darcey Nooner"
                class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/10.png') }}" alt="Avatar" />
              </li>
            </ul>
            <a href="javascript:;" class="btn btn-primary ms-auto" role="button">Join Now</a>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="card-actions">
              <a href="javascript:;" class="text-body me-4"><i class="icon-base bx bx-heart icon-md me-1"></i> 236</a>
              <a href="javascript:;" class="text-body"><i class="icon-base bx bx-chat icon-md me-1"></i> 12</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Shared Event -->
    <!-- Payment Data -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Payment Data</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="paymentDataList" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="paymentDataList">
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
              <a class="dropdown-item" href="javascript:void(0);">24 Hours</a>
            </div>
          </div>
        </div>
        <div class="card-body pt-3">
          <small>Price</small>
          <div class="d-flex align-items-center mb-4">
            <h5 class="text-primary me-2 mb-0">$455.60</h5>
            <span class="badge bg-label-primary">35% OFF</span>
          </div>
          <form id="paymentMethodForm" class="row g-5" onsubmit="return false">
            <div class="col-12">
              <small class="d-block mb-2">Choose payment method:</small>
              <div class="row g-5 text-nowrap">
                <div class="col">
                  <div class="form-check custom-option custom-option-basic">
                    <label class="form-check-label custom-option-content py-2" for="customRadioPaypal">
                      <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                        id="customRadioPaypal" checked />
                      <span class="custom-option-header pb-0">
                        <span>Paypal</span>
                      </span>
                    </label>
                  </div>
                </div>
                <div class="col">
                  <div class="form-check custom-option custom-option-basic">
                    <label class="form-check-label custom-option-content py-2" for="customRadioCC">
                      <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                        id="customRadioCC" />
                      <span class="custom-option-header pb-0">
                        <span>Credit Card</span>
                      </span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="input-group input-group-merge">
                <input id="paymentAddCard" name="paymentAddCard" class="form-control credit-card-payment"
                  type="text" placeholder="Card Number" aria-describedby="paymentAddCard2" />
                <span class="input-group-text cursor-pointer" id="paymentAddCard2"><span
                    class="card-payment-type"></span></span>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <input type="text" id="paymentAddCardExpiryDate" class="form-control expiry-date-payment"
                placeholder="Expiry Date" />
            </div>
            <div class="col-12 col-md-6">
              <div class="input-group input-group-merge">
                <input type="text" id="paymentAddCardCvv" class="form-control cvv-code-payment" maxlength="3"
                  placeholder="CVV Code" />
                <span class="input-group-text cursor-pointer" id="paymentAddCardCvv2"><i
                    class="icon-base bx bx-help-circle text-body-secondary" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Card Verification Value"></i></span>
              </div>
            </div>
            <div class="col-12">
              <input type="text" id="paymentAddCardName" class="form-control" placeholder="Name" />
            </div>
            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="defaultCheck3" />
                <label class="form-check-label" for="defaultCheck3">Save card?</label>
              </div>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary w-100 d-grid">Add Card</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/ Payment Data -->
    <!-- Business Shark -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">For Business Sharks</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="purchaseItemList" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="purchaseItemList">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <p>Here, i focus ona range of items and featured that we use in life without them</p>
          <h6 class="fw-normal mb-3">Basic price is $30</h6>
          <form id="businessForm" onsubmit="return false">
            <div class="row g-3">
              <div class="col-12">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content py-3 ps-12" for="brandingCheckbox">
                    <input class="form-check-input" type="checkbox" id="brandingCheckbox" />
                    <span class="custom-option-header pb-0">
                      <span class="fw-medium">Branding</span>
                      <span class="badge bg-label-success">+ $30</span>
                    </span>
                  </label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content py-3 ps-12" for="marketingCheckbox">
                    <input class="form-check-input" type="checkbox" id="marketingCheckbox" checked />
                    <span class="custom-option-header pb-0">
                      <span class="fw-medium">Marketing</span>
                      <span class="badge bg-label-primary">+ $75</span>
                    </span>
                  </label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content py-3 ps-12" for="appBuildingCheckbox">
                    <input class="form-check-input" type="checkbox" id="appBuildingCheckbox" />
                    <span class="custom-option-header pb-0">
                      <span class="fw-medium">App Building</span>
                      <span class="badge bg-label-success">+ $125</span>
                    </span>
                  </label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content py-3 ps-12" for="seoCheckbox">
                    <input class="form-check-input" type="checkbox" id="seoCheckbox" />
                    <span class="custom-option-header pb-0">
                      <span class="fw-medium">SEO</span>
                      <span class="badge bg-label-primary">+ $48</span>
                    </span>
                  </label>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between mt-3 mb-1">
              <span class="fw-medium">Vat Taxes</span>
              <span class="fw-medium">$24</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
              <span class="fw-medium">Total Amount</span>
              <span class="h5 text-primary mb-0">$99</span>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary w-100 d-grid">Purchase</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/ Business Shark -->
    <!-- Upgrade Plan -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Upgrade Your Plan</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <p class="mb-5">Please make the payment to start enjoying all the features of our premium plan as soon as
            possible.</p>
          <div class="d-flex align-items-center border-primary py-3 px-4 border rounded mb-5">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('assets/img/icons/unicons/briefcase.png') }}" alt="User" class="rounded" />
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-3">
                <p class="mb-0 text-heading">Business</p>
                <a href="javascript:void(0)" class="small" data-bs-target="#upgradePlanModal"
                  data-bs-toggle="modal">Upgrade Plan</a>
              </div>
              <div class="user-progress">
                <div class="d-flex justify-content-center">
                  <small class="mt-1 mb-0 me-1 text-heading">$</small>
                  <h5 class="mb-0">2,124</h5>
                  <small class="mt-auto mb-1 text-heading"> /Year</small>
                </div>
              </div>
            </div>
          </div>
          <form id="paymentDetailsForm" onsubmit="return false">
            <h6 class="mb-6">Payment Details</h6>
            <div class="d-flex align-items-center mb-4">
              <div class="me-4">
                <img src="{{ asset('assets/img/icons/payments/master-' . $configData['theme'] . '.png') }}"
                  alt="master-card" class="img-fluid" data-app-light-img="icons/payments/master-light.png"
                  data-app-dark-img="icons/payments/master-dark.png" />
              </div>
              <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                <div class="d-flex flex-column">
                  <h6 class="mb-0">Master Card</h6>
                  <small class="text-body-secondary">5688 xxxx xxxx 2356</small>
                </div>
                <div class="w-px-75">
                  <input type="text" class="form-control cvv-code-payment" maxlength="3" placeholder="CVV" />
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-4">
              <div class="me-4">
                <img src="{{ asset('assets/img/icons/payments/dc-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" alt="Visa" data-app-light-img="icons/payments/dc-light.png"
                  data-app-dark-img="icons/payments/dc-dark.png" />
              </div>
              <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                <div class="d-flex flex-column">
                  <h6 class="mb-0">Visa Card</h6>
                  <small class="text-body-secondary">8562 xxxx xxxx 4563</small>
                </div>
                <div class="w-px-75">
                  <input type="text" class="form-control cvv-code-payment" maxlength="3" placeholder="CVV" />
                </div>
              </div>
            </div>
            <a href="javascript:;" class="fw-medium d-block mb-5" data-bs-toggle="modal"
              data-bs-target="#addNewCCModal"> Add Payment Method </a>
            <div class="col-12 mb-5">
              <input id="addEmail" name="addEmail" class="form-control" type="text"
                placeholder="Email Address" />
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary w-100 d-grid">Process to payment</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/ Upgrade Plan -->
    <!-- Sales By Country -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1 me-2">Sales by Countries</h5>
            <p class="card-subtitle">Monthly Sales Overview</p>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="salesByCountry" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesByCountry">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <i class="fis fi fi-us rounded-circle fs-2"></i>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <div class="d-flex align-items-center">
                    <h6 class="mb-0 me-2">$8,567k</h6>
                    <small class="text-success fw-medium d-flex align-items-center gap-1">
                      <i class="icon-base bx bx-chevron-up icon-md"></i>
                      25.8%
                    </small>
                  </div>
                  <small>United states of america</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">884k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <i class="fis fi fi-br rounded-circle fs-2"></i>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <div class="d-flex align-items-center">
                    <h6 class="mb-0 me-2">$2,415k</h6>
                    <small class="text-danger fw-medium d-flex align-items-center gap-1">
                      <i class="icon-base bx bx-chevron-down icon-md"></i>
                      6.2%
                    </small>
                  </div>
                  <small>Brazil</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">645k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <i class="fis fi fi-in rounded-circle fs-2"></i>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <div class="d-flex align-items-center">
                    <h6 class="mb-0 me-2">$865k</h6>
                    <small class="text-success fw-medium d-flex align-items-center gap-1">
                      <i class="icon-base bx bx-chevron-up icon-md"></i>
                      12.4%
                    </small>
                  </div>
                  <small>India</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">148k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <i class="fis fi fi-au rounded-circle fs-2"></i>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <div class="d-flex align-items-center">
                    <h6 class="mb-0 me-2">$745k</h6>
                    <small class="text-danger fw-medium d-flex align-items-center gap-1">
                      <i class="icon-base bx bx-chevron-down icon-md"></i>
                      11.9%
                    </small>
                  </div>
                  <small>Australia</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">86k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <i class="fis fi fi-fr rounded-circle fs-2"></i>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <div class="d-flex align-items-center">
                    <h6 class="mb-0 me-2">$45</h6>
                    <small class="text-success fw-medium d-flex align-items-center gap-1">
                      <i class="icon-base bx bx-chevron-up icon-md"></i>
                      16.2%
                    </small>
                  </div>
                  <small>France</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">42k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <i class="fis fi fi-cn rounded-circle fs-2"></i>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <div class="d-flex align-items-center">
                    <h6 class="mb-0 me-2">$12k</h6>
                    <small class="text-success fw-medium d-flex align-items-center gap-1">
                      <i class="icon-base bx bx-chevron-up icon-md"></i>
                      14.8%
                    </small>
                  </div>
                  <small>China</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">18k</h6>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Sales By Country -->
    <!-- Order Statistics -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1 me-2">Order Statistics</h5>
            <p class="card-subtitle">42.82k Total Sales</p>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-6">
            <div class="d-flex flex-column align-items-center gap-1">
              <h3 class="mb-1">8,258</h3>
              <small>Total Orders</small>
            </div>
            <div id="orderStatisticsChart"></div>
          </div>
          <ul class="p-0 m-0">
            <li class="d-flex align-items-center mb-5">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-mobile-alt"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Electronic</h6>
                  <small>Mobile, Earbuds, TV</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">82.5k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-5">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-closet"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Fashion</h6>
                  <small>T-shirt, Jeans, Shoes</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">23.8k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-5">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-home-alt"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Decor</h6>
                  <small>Fine Art, Dining</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">849k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-secondary"><i class="icon-base bx bx-football"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Sports</h6>
                  <small>Football, Cricket Kit</small>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">99</h6>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Order Statistics -->
    <!-- Earning Reports -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1 me-2">Earning Reports</h5>
            <p class="card-subtitle">Weekly Earnings Overview</p>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="earningReports" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReports">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body pb-0 pt-6">
          <ul class="p-0 m-0">
            <li class="d-flex mb-6 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-trending-up"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Net Profit</h6>
                  <small class="text-body-secondary">12.4k Sales</small>
                </div>
                <div class="user-progress"><span class="me-3">$1,619</span><i
                    class="icon-base bx bx-chevron-up text-success ms-1"></i> <span>18.6%</span></div>
              </div>
            </li>
            <li class="d-flex mb-6 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-dollar"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Total Income</h6>
                  <small class="text-body-secondary">Sales, Affiliation</small>
                </div>
                <div class="user-progress"><span class="me-3">$3,571</span><i
                    class="icon-base bx bx-chevron-up text-success ms-1"></i> <span>39.6%</span></div>
              </div>
            </li>
            <li class="d-flex mb-6 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-secondary"><i
                    class="icon-base bx bx-credit-card"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Total Expenses</h6>
                  <small class="text-body-secondary">ADVT, Marketing</small>
                </div>
                <div class="user-progress"><span class="me-3">$430</span><i
                    class="icon-base bx bx-chevron-up text-success ms-1"></i> <span>52.8%</span></div>
              </div>
            </li>
          </ul>
          <div id="reportBarChart"></div>
        </div>
      </div>
    </div>
    <!--/ Earning Reports -->
    <!-- Top Courses -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Top Courses</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="topCourses" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topCourses">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Download</a>
              <a class="dropdown-item" href="javascript:void(0);">View All</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="list-unstyled mb-0">
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-primary"><i
                    class="icon-base bx bx-video icon-lg"></i></span>
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                  <h6 class="mb-0">Videography Basic Design Course</h6>
                </div>
                <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-xxl-end">
                  <div class="badge bg-label-secondary">1.2k Views</div>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-info"><i
                    class="icon-base bx bx-code-alt icon-lg"></i></span>
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                  <h6 class="mb-0">Basic Front-end Development Course</h6>
                </div>
                <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-xxl-end">
                  <div class="badge bg-label-secondary">834 Views</div>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-success"><i
                    class="icon-base bx bx-camera icon-lg"></i></span>
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                  <h6 class="mb-0">Basic Fundamentals of Photography</h6>
                </div>
                <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-xxl-end">
                  <div class="badge bg-label-secondary">3.7k Views</div>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-warning"><i
                    class="icon-base bx bx-basketball icon-lg"></i></span>
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                  <h6 class="mb-0">Advance Dribble Base Visual Design</h6>
                </div>
                <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-xxl-end">
                  <div class="badge bg-label-secondary">2.5k Views</div>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-danger"><i
                    class="icon-base bx bx-microphone icon-lg"></i></span>
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                  <h6 class="mb-0">Your First Singing Lesson</h6>
                </div>
                <div class="col-sm-4 col-lg-12 col-xxl-4 d-flex justify-content-xxl-end">
                  <div class="badge bg-label-secondary">948 Views</div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Top Courses -->
    <!-- Upcoming Webinar -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="bg-label-primary rounded-3 text-center mb-4 pt-6">
            <img class="img-fluid" src="{{ asset('assets/img/illustrations/sitting-girl-with-laptop.png') }}"
              alt="Card girl image" style="width: 52%;" />
          </div>
          <h5 class="mb-2">Upcoming Webinar</h5>
          <p>Next Generation Frontend Architecture Using Layout Engine And React Native Web.</p>
          <div class="row mb-4 g-3">
            <div class="col-6">
              <div class="d-flex align-items-center">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-primary"><i
                      class="icon-base bx bx-calendar icon-lg"></i></span>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">17 Nov 23</h6>
                  <small>Date</small>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-primary"><i
                      class="icon-base bx bx-time-five icon-lg"></i></span>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">32 minutes</h6>
                  <small>Duration</small>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 text-center">
            <a href="javascript:void(0);" class="btn btn-primary w-100 d-grid">Join the event</a>
          </div>
        </div>
      </div>
    </div>
    <!--/ Upcoming Webinar -->
    <!-- Assignment Progress -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Assignment Progress</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="assignmentProgress"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="assignmentProgress">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Download</a>
              <a class="dropdown-item" href="javascript:void(0);">View All</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="primary" data-series="72" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-9">
                  <div class="me-2">
                    <h6 class="mb-1">User experience Design</h6>
                    <p class="mb-0">120 Tasks</p>
                  </div>
                </div>
                <div class="col-3 text-end">
                  <button type="button" class="btn btn-sm btn-icon btn-label-secondary">
                    <i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-20px"></i>
                  </button>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="success" data-series="48" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-9">
                  <div class="me-2">
                    <h6 class="mb-1">Basic fundamentals</h6>
                    <p class="mb-0">32 Tasks</p>
                  </div>
                </div>
                <div class="col-3 text-end">
                  <button type="button" class="btn btn-sm btn-icon btn-label-secondary">
                    <i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-20px"></i>
                  </button>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="danger" data-series="15" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-9">
                  <div class="me-2">
                    <h6 class="mb-1">React native components</h6>
                    <p class="mb-0">182 Tasks</p>
                  </div>
                </div>
                <div class="col-3 text-end">
                  <button type="button" class="btn btn-sm btn-icon btn-label-secondary">
                    <i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-20px"></i>
                  </button>
                </div>
              </div>
            </li>
            <li class="d-flex">
              <div class="chart-progress me-4" data-color="info" data-series="24" data-progress_variant="true"></div>
              <div class="row w-100 align-items-center">
                <div class="col-9">
                  <div class="me-2">
                    <h6 class="mb-1">Basic of music theory</h6>
                    <p class="mb-0">56 Tasks</p>
                  </div>
                </div>
                <div class="col-3 text-end">
                  <button type="button" class="btn btn-sm btn-icon btn-label-secondary">
                    <i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-20px"></i>
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Assignment Progress -->
    <!-- Team Members -->
    <div class="col-lg-12 col-xxl-6">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Team Members</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="teamMemberList" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="teamMemberList">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-borderless table-sm">
            <thead>
              <tr>
                <th class="ps-6">Name</th>
                <th>Project</th>
                <th>Task</th>
                <th class="pe-6">Progress</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar me-3">
                      <img src="{{ asset('assets/img/avatars/17.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="mb-0 text-truncate">Nathan Wagner</h6>
                      <small class="text-truncate text-body">iOS Developer</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary text-uppercase">Zipcar</span></td>
                <td><span class="fw-medium">87/135</span></td>
                <td>
                  <div class="chart-progress" data-color="primary" data-series="65"></div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar me-3">
                      <img src="{{ asset('assets/img/avatars/8.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="mb-0 text-truncate">Emma Bowen</h6>
                      <small class="text-truncate text-body">UI/UX Designer</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-danger text-uppercase">Bitbank</span></td>
                <td><span class="fw-medium">320/440</span></td>
                <td>
                  <div class="chart-progress" data-color="danger" data-series="85"></div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar me-3">
                      <span class="avatar-initial rounded-circle bg-label-warning">AM</span>
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="mb-0 text-truncate">Adrian McGuire</h6>
                      <small class="text-truncate text-body">PHP Developer</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-warning text-uppercase">Payers</span></td>
                <td><span class="fw-medium">50/82</span></td>
                <td>
                  <div class="chart-progress" data-color="warning" data-series="73"></div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar me-3">
                      <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="mb-0 text-truncate">Alma Gonzalez</h6>
                      <small class="text-truncate text-body">Product Manager</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-info text-uppercase">Brandi</span></td>
                <td><span class="fw-medium">98/260</span></td>
                <td>
                  <div class="chart-progress" data-color="info" data-series="61"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--/ Team Members -->
    <!-- pill table -->
    <div class="col-xxl-6">
      <div class="card text-center h-100">
        <div class="card-header nav-align-top">
          <ul class="nav nav-pills d-flex flex-wrap row-gap-2" role="tablist">
            <li class="nav-item">
              <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                data-bs-target="#navs-pills-browser" aria-controls="navs-pills-browser"
                aria-selected="true">Browser</button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                data-bs-target="#navs-pills-os" aria-controls="navs-pills-os" aria-selected="false">Operating
                System</button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                data-bs-target="#navs-pills-country" aria-controls="navs-pills-country"
                aria-selected="false">Country</button>
            </li>
          </ul>
        </div>
        <div class="tab-content pt-0">
          <div class="tab-pane fade show active" id="navs-pills-browser" role="tabpanel">
            <div class="table-responsive text-start text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Browser</th>
                    <th>Visits</th>
                    <th class="w-50">Data In Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/chrome.png') }}" alt="Chrome" height="24"
                          class="me-3" />
                        <span class="text-heading">Chrome</span>
                      </div>
                    </td>
                    <td class="text-heading">8.92k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-success" role="progressbar" style="width: 64.75%"
                            aria-valuenow="64.75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">64.75%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/safari.png') }}" alt="Safari" height="24"
                          class="me-3" />
                        <span class="text-heading">Safari</span>
                      </div>
                    </td>
                    <td class="text-heading">1.29k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-primary" role="progressbar" style="width: 18.43%"
                            aria-valuenow="18.43" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">18.43%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/firefox.png') }}" alt="Firefox" height="24"
                          class="me-3" />
                        <span class="text-heading">Firefox</span>
                      </div>
                    </td>
                    <td class="text-heading">328</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 8.37%"
                            aria-valuenow="8.37" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">8.37%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/edge.png') }}" alt="Edge" height="24"
                          class="me-3" />
                        <span class="text-heading">Edge</span>
                      </div>
                    </td>
                    <td class="text-heading">142</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-warning" role="progressbar" style="width: 6.12%"
                            aria-valuenow="6.12" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">6.12%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>5</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/opera.png') }}" alt="Opera" height="24"
                          class="me-3" />
                        <span class="text-heading">Opera</span>
                      </div>
                    </td>
                    <td class="text-heading">82</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-danger" role="progressbar" style="width: 1.94%"
                            aria-valuenow="1.94" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">1.94%</small>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="navs-pills-os" role="tabpanel">
            <div class="table-responsive text-start text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>System</th>
                    <th>Visits</th>
                    <th class="w-50">Data In Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/windows.png') }}" alt="Windows" height="24"
                          class="me-3" />
                        <span class="text-heading">Windows</span>
                      </div>
                    </td>
                    <td class="text-heading">875.24k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-success" role="progressbar" style="width: 61.50%"
                            aria-valuenow="61.50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">61.50%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/mac.png') }}" alt="Mac" height="24"
                          class="me-3" />
                        <span class="text-heading">Mac</span>
                      </div>
                    </td>
                    <td class="text-heading">89.68k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-primary" role="progressbar" style="width: 16.67%"
                            aria-valuenow="16.67" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">16.67%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/ubuntu.png') }}" alt="Ubuntu" height="24"
                          class="me-3" />
                        <span class="text-heading">Ubuntu</span>
                      </div>
                    </td>
                    <td class="text-heading">37.68k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 12.82%"
                            aria-valuenow="12.82" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">12.82%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/chrome.png') }}" alt="Chrome" height="24"
                          class="me-3" />
                        <span class="text-heading">Chrome</span>
                      </div>
                    </td>
                    <td class="text-heading">8.34k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-warning" role="progressbar" style="width: 6.25%"
                            aria-valuenow="6.25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">6.25%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>5</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/icons/brands/cent.png') }}" alt="Cent" height="24"
                          class="me-3" />
                        <span class="text-heading">Cent</span>
                      </div>
                    </td>
                    <td class="text-heading">2.25k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-danger" role="progressbar" style="width: 2.76%"
                            aria-valuenow="2.76" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">2.76%</small>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="navs-pills-country" role="tabpanel">
            <div class="table-responsive text-start text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Country</th>
                    <th>Visits</th>
                    <th class="w-50">Data In Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="fis fi fi-us rounded-circle fs-3 me-3"></i>
                        <span class="text-heading">USA</span>
                      </div>
                    </td>
                    <td class="text-heading">87.24k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-success" role="progressbar" style="width: 38.12%"
                            aria-valuenow="38.12" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">38.12%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="fis fi fi-br rounded-circle fs-3 me-3"></i>
                        <span class="text-heading">Brazil</span>
                      </div>
                    </td>
                    <td class="text-heading">42.68k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-primary" role="progressbar" style="width: 28.23%"
                            aria-valuenow="28.23" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">28.23%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="fis fi fi-in rounded-circle fs-3 me-3"></i>
                        <span class="text-heading">India</span>
                      </div>
                    </td>
                    <td class="text-heading">12.58k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 14.82%"
                            aria-valuenow="14.82" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">14.82%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="fis fi fi-au rounded-circle fs-3 me-3"></i>
                        <span class="text-heading">Australia</span>
                      </div>
                    </td>
                    <td class="text-heading">4.13k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-warning" role="progressbar" style="width: 12.72%"
                            aria-valuenow="12.72" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">12.72%</small>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>5</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="fis fi fi-fr rounded-circle fs-3 me-3"></i>
                        <span class="text-heading">France</span>
                      </div>
                    </td>
                    <td class="text-heading">2.21k</td>
                    <td>
                      <div class="d-flex justify-content-between align-items-center gap-4">
                        <div class="progress w-100" style="height:10px;">
                          <div class="progress-bar bg-danger" role="progressbar" style="width: 7.11%"
                            aria-valuenow="7.11" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="fw-medium">7.11%</small>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ pill table -->
    <!-- Delivery Performance -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1 me-2">Delivery Performance</h5>
            <p class="card-subtitle">12% increase in this month</p>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="deliveryPerformance"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryPerformance">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-primary"><i
                    class="icon-base bx bx-cube icon-lg"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-1 fw-normal">Packages in transit</h6>
                  <p class="text-success mb-0">
                    <i class="icon-base bx bx-chevron-up icon-lg me-1"></i>
                    25.8%
                  </p>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">10k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-info"><i
                    class="icon-base bx bxs-truck icon-lg"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-1 fw-normal">Packages out for delivery</h6>
                  <p class="text-success mb-0">
                    <i class="icon-base bx bx-chevron-up icon-lg me-1"></i>
                    4.3%
                  </p>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">5k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-success"><i
                    class="icon-base bx bx-check-circle icon-lg"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-1 fw-normal">Packages delivered</h6>
                  <p class="text-danger mb-0">
                    <i class="icon-base bx bx-chevron-down icon-lg me-1"></i>
                    12.5
                  </p>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">15k</h6>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-warning"><i
                    class="icon-base bx bxs-offer icon-lg"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-1 fw-normal">Delivery success rate</h6>
                  <p class="text-success mb-0">
                    <i class="icon-base bx bx-chevron-up icon-lg me-1"></i>
                    35.6%
                  </p>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">95%</h6>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6 align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-secondary"><i
                    class="icon-base bx bx-time-five icon-lg"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-1 fw-normal">Average delivery time</h6>
                  <p class="text-danger mb-0">
                    <i class="icon-base bx bx-chevron-down icon-lg me-1"></i>
                    2.15
                  </p>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">2.5 Days</h6>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-danger"><i
                    class="icon-base bx bx-group icon-lg"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-1 fw-normal">Customer satisfaction</h6>
                  <p class="text-success mb-0">
                    <i class="icon-base bx bx-chevron-up icon-lg me-1"></i>
                    5.7%
                  </p>
                </div>
                <div class="user-progress">
                  <h6 class="mb-0">4.5/5</h6>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Delivery Performance -->
    <!-- Orders by Countries -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Orders by Countries</h5>
            <p class="card-subtitle">62 deliveries in progress</p>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="ordersCountries"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="ordersCountries">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="nav-align-top">
            <ul class="nav nav-tabs nav-fill rounded-0 timeline-indicator-advanced" role="tablist">
              <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-justified-new" aria-controls="navs-justified-new"
                  aria-selected="true">New</button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-justified-link-preparing" aria-controls="navs-justified-link-preparing"
                  aria-selected="false">Preparing</button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-justified-link-shipping" aria-controls="navs-justified-link-shipping"
                  aria-selected="false">Shipping</button>
              </li>
            </ul>
            <div class="tab-content border-0  mx-1">
              <div class="tab-pane fade show active" id="navs-justified-new" role="tabpanel">
                <ul class="timeline mb-0">
                  <li class="timeline-item ps-6 border-dashed">
                    <span class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                      <i class="icon-base bx bx-check-circle"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-success text-uppercase">sender</small>
                      </div>
                      <h6 class="my-50">Myrtle Ullrich</h6>
                      <p class="text-body mb-0">101 Boulder, California(CA), 95959</p>
                    </div>
                  </li>
                  <li class="timeline-item ps-6 border-0">
                    <span class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                      <i class="icon-base bx bx-map"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-primary text-uppercase">Receiver</small>
                      </div>
                      <h6 class="my-50">Barry Schowalter</h6>
                      <p class="text-body mb-0">939 Orange, California(CA), 92118</p>
                    </div>
                  </li>
                </ul>
                <div class="border-1 border-light border-dashed my-4"></div>
                <ul class="timeline mb-0">
                  <li class="timeline-item ps-6 border-dashed">
                    <span class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                      <i class="icon-base bx bx-check-circle"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-success text-uppercase">sender</small>
                      </div>
                      <h6 class="my-50">Veronica Herman</h6>
                      <p class="text-body mb-0">162 Windsor, California(CA), 95492</p>
                    </div>
                  </li>
                  <li class="timeline-item ps-6 border-0">
                    <span class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                      <i class="icon-base bx bx-map"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-primary text-uppercase">Receiver</small>
                      </div>
                      <h6 class="my-50">Helen Jacobs</h6>
                      <p class="text-body mb-0">487 Sunset, California(CA), 94043</p>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="tab-pane fade" id="navs-justified-link-preparing" role="tabpanel">
                <ul class="timeline mb-0">
                  <li class="timeline-item ps-6 border-dashed">
                    <span class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                      <i class="icon-base bx bx-check-circle"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-success text-uppercase">sender</small>
                      </div>
                      <h6 class="my-50">Barry Schowalter</h6>
                      <p class="text-body mb-0">939 Orange, California(CA), 92118</p>
                    </div>
                  </li>
                  <li class="timeline-item ps-6 border-0">
                    <span class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                      <i class="icon-base bx bx-map"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-primary text-uppercase">Receiver</small>
                      </div>
                      <h6 class="my-50">Myrtle Ullrich</h6>
                      <p class="text-body mb-0">101 Boulder, California(CA), 95959</p>
                    </div>
                  </li>
                </ul>
                <div class="border-1 border-light border-dashed my-4"></div>
                <ul class="timeline mb-0">
                  <li class="timeline-item ps-6 border-dashed">
                    <span class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                      <i class="icon-base bx bx-check-circle"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-success text-uppercase">sender</small>
                      </div>
                      <h6 class="my-50">Veronica Herman</h6>
                      <p class="text-body mb-0">162 Windsor, California(CA), 95492</p>
                    </div>
                  </li>
                  <li class="timeline-item ps-6 border-0">
                    <span class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                      <i class="icon-base bx bx-map"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-primary text-uppercase">Receiver</small>
                      </div>
                      <h6 class="my-50">Helen Jacobs</h6>
                      <p class="text-body mb-0">487 Sunset, California(CA), 94043</p>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="tab-pane fade" id="navs-justified-link-shipping" role="tabpanel">
                <ul class="timeline mb-0">
                  <li class="timeline-item ps-6 border-dashed">
                    <span class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                      <i class="icon-base bx bx-check-circle"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-success text-uppercase">sender</small>
                      </div>
                      <h6 class="my-50">Veronica Herman</h6>
                      <p class="text-body mb-0">101 Boulder, California(CA), 95959</p>
                    </div>
                  </li>
                  <li class="timeline-item ps-6 border-0">
                    <span class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                      <i class="icon-base bx bx-map"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-primary text-uppercase">Receiver</small>
                      </div>
                      <h6 class="my-50">Barry Schowalter</h6>
                      <p class="text-body mb-0">939 Orange, California(CA), 92118</p>
                    </div>
                  </li>
                </ul>
                <div class="border-1 border-light border-dashed my-4"></div>
                <ul class="timeline mb-0">
                  <li class="timeline-item ps-6 border-dashed">
                    <span class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                      <i class="icon-base bx bx-check-circle"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-success text-uppercase">sender</small>
                      </div>
                      <h6 class="my-50">Myrtle Ullrich</h6>
                      <p class="text-body mb-0">162 Windsor, California(CA), 95492</p>
                    </div>
                  </li>
                  <li class="timeline-item ps-6 border-0">
                    <span class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                      <i class="icon-base bx bx-map"></i>
                    </span>
                    <div class="timeline-event ps-1">
                      <div class="timeline-header">
                        <small class="text-primary text-uppercase">Receiver</small>
                      </div>
                      <h6 class="my-50">Helen Jacobs</h6>
                      <p class="text-body mb-0">487 Sunset, California(CA), 94043</p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Orders by Countries -->
    <!-- Popular Instructors -->
    <div class="col-md-6 col-xxl-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">Popular Instructors</h5>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="popularInstructors"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="popularInstructors">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="px-5 py-4 border border-start-0 border-end-0">
          <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0 text-uppercase">Instructors</p>
            <p class="mb-0 text-uppercase">courses</p>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-6">
            <div class="d-flex align-items-center">
              <div class="avatar avatar me-4">
                <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
              </div>
              <div>
                <div>
                  <h6 class="mb-0 text-truncate">Maven Analytics</h6>
                  <small class="text-truncate text-body">Business Intelligence</small>
                </div>
              </div>
            </div>
            <div class="text-end">
              <h6 class="mb-0">33</h6>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-6">
            <div class="d-flex align-items-center">
              <div class="avatar avatar me-4">
                <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
              </div>
              <div>
                <div>
                  <h6 class="mb-0 text-truncate">Bentlee Emblin</h6>
                  <small class="text-truncate text-body">Digital Marketing</small>
                </div>
              </div>
            </div>
            <div class="text-end">
              <h6 class="mb-0">52</h6>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-6">
            <div class="d-flex align-items-center">
              <div class="avatar avatar me-4">
                <img src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar" class="rounded-circle" />
              </div>
              <div>
                <div>
                  <h6 class="mb-0 text-truncate">Benedetto Rossiter</h6>
                  <small class="text-truncate text-body">UI/UX Design</small>
                </div>
              </div>
            </div>
            <div class="text-end">
              <h6 class="mb-0">12</h6>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-6">
            <div class="d-flex align-items-center">
              <div class="avatar avatar me-4">
                <img src="{{ asset('assets/img/avatars/4.png') }}" alt="Avatar" class="rounded-circle" />
              </div>
              <div>
                <div>
                  <h6 class="mb-0 text-truncate">Beverlie Krabbe</h6>
                  <small class="text-truncate text-body">React Native</small>
                </div>
              </div>
            </div>
            <div class="text-end">
              <h6 class="mb-0">8</h6>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div class="avatar avatar me-4">
                <img src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar" class="rounded-circle" />
              </div>
              <div>
                <div>
                  <h6 class="mb-0 text-truncate">Alma Gonzalez</h6>
                  <small class="text-truncate text-body">Java Developer</small>
                </div>
              </div>
            </div>
            <div class="text-end">
              <h6 class="mb-0">9</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Popular Instructors -->
    <!-- Conversion rate -->
    <div class="col-md-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1 me-2">Conversion Rate</h5>
            <p class="card-subtitle">Compared To Last Month</p>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="conversionRate"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="conversionRate">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body pt-4">
          <div class="d-flex justify-content-between align-items-center mb-6">
            <div class="d-flex flex-row align-items-center gap-2">
              <h3 class="mb-0">8.72%</h3>
              <small class="text-success">
                <i class="icon-base bx bx-chevron-up icon-lg"></i>
                4.8%
              </small>
            </div>
            <div id="conversionRateChart"></div>
          </div>
          <ul class="p-0 m-0">
            <li class="d-flex mb-6">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0 fw-normal">Impressions</h6>
                  <small>12.4k Visits</small>
                </div>
                <div class="user-progress"><i class="icon-base bx icon-lg bx-up-arrow-alt text-success me-2"></i>
                  <span>12.8%</span>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0 fw-normal">Added To Cart</h6>
                  <small>32 Product in cart</small>
                </div>
                <div class="user-progress"><i class="icon-base bx icon-lg bx-down-arrow-alt text-danger me-2"></i>
                  <span>- 8.5% </span>
                </div>
              </div>
            </li>
            <li class="d-flex mb-6">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0 fw-normal">Checkout</h6>
                  <small>21 Products checkout</small>
                </div>
                <div class="user-progress"><i class="icon-base bx icon-lg bx-up-arrow-alt text-success me-2"></i>
                  <span>9.12%</span>
                </div>
              </div>
            </li>
            <li class="d-flex">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0 fw-normal">Purchased</h6>
                  <small>12 Orders</small>
                </div>
                <div class="user-progress"><i class="icon-base bx icon-lg bx-up-arrow-alt text-success me-2"></i>
                  <span>2.83%</span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Conversion rate -->
    <!-- Top Products by -->
    <div class="col-12 col-xxl-8 mb-6">
      <div class="card">
        <div class="row row-bordered g-0">
          <div class="col-md-6">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0 me-2">Top Products by <span class="text-primary">Sales</span></h5>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="topSales" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topSales">
                  <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                </div>
              </div>
            </div>
            <div class="card-body pt-6">
              <ul class="p-0 m-0">
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/oneplus.png') }}" alt="oneplus" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Oneplus Nord</h6>
                      <small class="d-block">Oneplus</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                      <span class="fw-medium">$98,348</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/watch-primary.png') }}" alt="smart band" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Smart Band 4</h6>
                      <small class="d-block">Xiaomi</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                      <span class="fw-medium">$15,459</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/surface.png') }}" alt="Surface" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Surface Pro X</h6>
                      <small class="d-block">Miscrosoft</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                      <span class="fw-medium">$4,589</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/iphone.png') }}" alt="iphone" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">iphone 13</h6>
                      <small class="d-block">Apple</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                      <span class="fw-medium">$84,345</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/earphone.png') }}" alt="Bluetooth Earphone" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Bluetooth Earphone</h6>
                      <small class="d-block">Beats</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                      <span class="fw-medium">$10,374</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0 me-2">Top Products by <span class="text-primary">Volume</span></h5>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="topVolume" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topVolume">
                  <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                </div>
              </div>
            </div>
            <div class="card-body pt-6">
              <ul class="p-0 m-0">
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/laptop-secondary.png') }}" alt="ENVY Laptop"
                      class="rounded" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">ENVY Laptop</h6>
                      <small class="d-block">HP</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-3">
                      <span class="fw-medium">124k</span>
                      <span class="badge bg-label-success">+12.4%</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/computer.png') }}" alt="Apple"
                      class="rounded" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Apple</h6>
                      <small class="d-block">iMac Pro</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-3">
                      <span class="fw-medium">74.9k</span>
                      <span class="badge bg-label-danger">-8.5%</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/watch.png') }}" alt="Smart Watch"
                      class="rounded" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Smart Watch</h6>
                      <small class="d-block">Fitbit</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-3">
                      <span class="fw-medium">4.4k</span>
                      <span class="badge bg-label-success">+62.6%</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center mb-7">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/oneplus-success.png') }}" alt="Oneplus RT"
                      class="rounded" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Oneplus RT</h6>
                      <small class="d-block">Oneplus</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-3">
                      <span class="fw-medium">12,3k.71</span>
                      <span class="badge bg-label-success">+16.7%</span>
                    </div>
                  </div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="avatar flex-shrink-0 me-3">
                    <img src="{{ asset('assets/img/icons/unicons/pixel.png') }}" alt="Pixel 4a" class="rounded" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-0">Pixel 4a</h6>
                      <small class="d-block">Google</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-3">
                      <span class="fw-medium">834k</span>
                      <span class="badge bg-label-danger">-12.9%</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Top Products by -->
    <!-- Activity Timeline -->
    <div class="col-xxl-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title m-0 me-2">Activity Timeline</h5>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="timelineWapper"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="timelineWapper">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body pt-2">
          <ul class="timeline mb-0">
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-primary"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-3">
                  <h6 class="mb-0">12 Invoices have been paid</h6>
                  <small class="text-body-secondary">12 min ago</small>
                </div>
                <p class="mb-2">Invoices have been paid to the company</p>
                <div class="d-flex align-items-center mb-1">
                  <div class="badge bg-lighter rounded-2">
                    <img src="{{ asset('assets/img/icons/misc/pdf.png') }}" alt="img" width="20"
                      class="me-2" />
                    <span class="h6 mb-0 text-body">invoices.pdf</span>
                  </div>
                </div>
              </div>
            </li>
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-success"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-3">
                  <h6 class="mb-0">Client Meeting</h6>
                  <small class="text-body-secondary">45 min ago</small>
                </div>
                <p class="mb-2">Project meeting with john @10:15am</p>
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <div class="d-flex flex-wrap align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <p class="mb-0 small fw-medium">Lester McCarthy (Client)</p>
                      <small>CEO of {{ config('variables.creatorName') }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-info"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-3">
                  <h6 class="mb-0">Create a new project for client</h6>
                  <small class="text-body-secondary">2 Day Ago</small>
                </div>
                <p class="mb-2">6 team members in a project</p>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap p-0">
                    <div class="d-flex flex-wrap align-items-center">
                      <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                          title="Vinnie Mostowy" class="avatar pull-up">
                          <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}"
                            alt="Avatar" />
                        </li>
                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                          title="Allen Rieske" class="avatar pull-up">
                          <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}"
                            alt="Avatar" />
                        </li>
                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                          title="Julee Rossignol" class="avatar pull-up">
                          <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}"
                            alt="Avatar" />
                        </li>
                        <li class="avatar">
                          <span class="avatar-initial rounded-circle pull-up text-heading" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="3 more">+3</span>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Activity Timeline -->
    <!-- Finance Summary -->
    <div class="col-xxl-6">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1 me-2">Finance Summary</h5>
            <p class="card-subtitle">Check out each column for more details</p>
          </div>
          <span class="badge rounded-pill bg-label-primary p-2">
            <i class="icon-base bx bx-dollar icon-lg m-50"></i>
          </span>
        </div>
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2 mb-6">
            <div class="d-flex flex-column w-50 me-2">
              <small class="text-nowrap d-block mb-1">Annual Companies Taxes</small>
              <h6 class="mb-0">$500,00</h6>
            </div>
            <div class="d-flex flex-column">
              <small class="text-nowrap d-block mb-1">Next Tax Review Date</small>
              <h6 class="mb-0">July 24,2022</h6>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 my-6 py-4">
            <div class="d-flex flex-column w-50 me-2">
              <small class="text-nowrap d-block mb-1">Average Product Price</small>
              <h6 class="mb-0">$89.90</h6>
            </div>
            <div class="d-flex flex-column flex-grow-1">
              <small class="text-nowrap d-block mb-1">Satisfaction Rate</small>
              <div class="d-flex align-items-center">
                <div class="progress w-100 me-4" style="height: 10px;">
                  <div class="progress-bar bg-primary shadow-none" role="progressbar" style="width: 80%"
                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="fw-medium">75%</small>
              </div>
            </div>
          </div>
          <div class="d-flex flex-wrap align-items-center">
            <ul class="list-unstyled w-50 me-2 d-flex align-items-center avatar-group mb-0">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                title="Vinnie Mostowy" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar" />
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                title="Allen Rieske" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar" />
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                title="Julee Rossignol" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar" />
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                title="Darcey Nooner" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/10.png') }}" alt="Avatar" />
              </li>
            </ul>
            <span class="badge bg-label-primary rounded">5 Days Ago</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Finance Summary -->
    <!-- Vehicles Condition -->
    <div class="col-lg-6 col-xxl-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">Vehicles Condition</h5>
          </div>
          <div class="dropdown">
            <button class="btn text-body-secondary p-0" type="button" id="vehiclesCondition"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="vehiclesCondition">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="success" data-series="83" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-8">
                  <div class="me-2">
                    <h6 class="mb-1 text-success">Incorrect address</h6>
                    <p class="mb-0">all exceptions</p>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="badge bg-label-secondary">+10%</div>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="info" data-series="50" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-8">
                  <div class="me-2">
                    <h6 class="mb-1 text-info">Good</h6>
                    <p class="mb-0">24 Vehicles</p>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="badge bg-label-secondary">+8.1</div>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="primary" data-series="35" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-8">
                  <div class="me-2">
                    <h6 class="mb-1 text-primary">Average</h6>
                    <p class="mb-0">182 Tasks</p>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="badge bg-label-secondary">-2.5%</div>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="chart-progress me-4" data-color="warning" data-series="28" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-8">
                  <div class="me-2">
                    <h6 class="mb-1 text-warning">Bad</h6>
                    <p class="mb-0">8 Vehicles</p>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="badge bg-label-secondary">-3.4%</div>
                </div>
              </div>
            </li>
            <li class="d-flex">
              <div class="chart-progress me-4" data-color="danger" data-series="15" data-progress_variant="true">
              </div>
              <div class="row w-100 align-items-center">
                <div class="col-8">
                  <div class="me-2">
                    <h6 class="mb-1 text-danger">Not Working</h6>
                    <p class="mb-0">4 Vehicles</p>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="badge bg-label-secondary">+12.6%</div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Vehicles Condition -->
  </div>

  <!-- Modal -->
  @include('_partials/_modals/modal-add-new-cc')
  @include('_partials/_modals/modal-upgrade-plan')
  <!-- /Modal -->
  <script type="module">
    // Check selected custom option
    window.Helpers.initCustomOptionCheck();
  </script>

@endsection
