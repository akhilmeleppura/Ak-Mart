@extends('layouts/layoutMaster')

@section('title', 'Cards Statistics- UI elements')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/cards-statistics.js'])
@endsection

@section('content')
  <!-- Cards with few info -->
  <div class="row g-6 mb-6">
    <!-- single card  -->
    <div class="col-12">
      <div class="card">
        <div class="card-widget-separator-wrapper">
          <div class="card-body card-widget-separator">
            <div class="row gy-4 gy-sm-1">
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">
                  <div>
                    <h4 class="mb-0">24</h4>
                    <p class="mb-0">Clients</p>
                  </div>
                  <div class="avatar me-sm-6">
                    <span class="avatar-initial rounded bg-label-secondary text-heading">
                      <i class="icon-base bx bx-user icon-26px"></i>
                    </span>
                  </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none me-6" />
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                  <div>
                    <h4 class="mb-0">165</h4>
                    <p class="mb-0">Invoices</p>
                  </div>
                  <div class="avatar me-lg-6">
                    <span class="avatar-initial rounded bg-label-secondary text-heading">
                      <i class="icon-base bx bx-file icon-26px"></i>
                    </span>
                  </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none" />
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0 card-widget-3">
                  <div>
                    <h4 class="mb-0">$2.46k</h4>
                    <p class="mb-0">Paid</p>
                  </div>
                  <div class="avatar me-sm-6">
                    <span class="avatar-initial rounded bg-label-secondary text-heading">
                      <i class="icon-base bx bx-check-double icon-26px"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h4 class="mb-0">$876</h4>
                    <p class="mb-0">Unpaid</p>
                  </div>
                  <div class="avatar">
                    <span class="avatar-initial rounded bg-label-secondary text-heading">
                      <i class="icon-base bx bx-error-circle icon-26px"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /single card  -->
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="text-heading mb-1">Session</p>
              <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">58,352</h4>
                <span class="text-success">(+29%)</span>
              </div>
              <span>Last Week Analytics</span>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-primary rounded p-2">
                <i class="icon-base bx bx-trending-up icon-lg"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="text-heading mb-1">Time On Site</p>
              <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">28m 14s</h4>
                <span class="text-success">(+18%)</span>
              </div>
              <span>Last Day Analytics</span>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-info rounded p-2">
                <i class="icon-base bx bx-time-five icon-lg"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="text-heading mb-1">Bounce Rate</p>
              <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">62%</h4>
                <span class="text-danger">(-14%)</span>
              </div>
              <span>Last Week Analytics</span>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-danger rounded p-2">
                <i class="icon-base bx bx-pie-chart-alt icon-lg"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="text-heading mb-1">Users</p>
              <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">18,472</h4>
                <span class="text-success">(+42%)</span>
              </div>
              <span>Last Year Analytics</span>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-success rounded p-2">
                <i class="icon-base bx bx-user icon-lg"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Cards with few info -->

  <!-- Card Border Shadow -->
  <div class="row g-6 mb-6">
    <div class="col-sm-6 col-lg-3">
      <div class="card card-border-shadow-primary h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bxs-truck icon-lg"></i></span>
            </div>
            <h4 class="mb-0">42</h4>
          </div>
          <p class="mb-2">On route vehicles</p>
          <p class="mb-0">
            <span class="text-heading fw-medium me-2">+18.2%</span>
            <span class="text-body-secondary">than last week</span>
          </p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card card-border-shadow-warning h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-warning"><i class="icon-base bx bx-error icon-lg"></i></span>
            </div>
            <h4 class="mb-0">8</h4>
          </div>
          <p class="mb-2">Vehicles with errors</p>
          <p class="mb-0">
            <span class="text-heading fw-medium me-2">-8.7%</span>
            <span class="text-body-secondary">than last week</span>
          </p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card card-border-shadow-danger h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-danger"><i
                  class="icon-base bx bx-git-repo-forked icon-lg"></i></span>
            </div>
            <h4 class="mb-0">27</h4>
          </div>
          <p class="mb-2">Deviated from route</p>
          <p class="mb-0">
            <span class="text-heading fw-medium me-2">+4.3%</span>
            <span class="text-body-secondary">than last week</span>
          </p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card card-border-shadow-info h-100">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-info"><i
                  class="icon-base bx bx-time-five icon-lg"></i></span>
            </div>
            <h4 class="mb-0">13</h4>
          </div>
          <p class="mb-2">Late vehicles</p>
          <p class="mb-0">
            <span class="text-heading fw-medium me-2">-2.5%</span>
            <span class="text-body-secondary">than last week</span>
          </p>
        </div>
      </div>
    </div>
  </div>
  <!--/ Card Border Shadow -->

  <!-- Centered align Cards -->
  <div class="row g-6 mb-6">
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="card-title fw-normal m-0 me-2">Total Sales</h6>
          <div class="dropdown">
            <button class="btn btn-text text-body-secondary p-0" type="button" id="totalSalesList"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Today <i
                class="icon-base bx bx-chevron-down"></i></button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalSalesList">
              <a class="dropdown-item" href="javascript:void(0);">Yesterday</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            </div>
          </div>
        </div>
        <div class="card-body text-center">
          <div class="d-flex justify-content-center mb-3">
            <div class="avatar avatar-md flex-shrink-0">
              <span class="avatar-initial avatar-shadow-primary rounded-circle"><i
                  class="icon-base bx bx-trending-up icon-26px"></i></span>
            </div>
          </div>
          <h4 class="card-title mb-0">8,352</h4>
          <p class="mb-2">12% of target</p>
          <span class="text-success">+29% <i class="icon-base bx bx-chevron-up icon-lg"></i></span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="card-title fw-normal m-0 me-2">Referral Income</h6>
          <div class="dropdown">
            <button class="btn btn-text text-body-secondary p-0" type="button" id="referralsList"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Today <i
                class="icon-base bx bx-chevron-down"></i></button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="referralsList">
              <a class="dropdown-item" href="javascript:void(0);">Yesterday</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            </div>
          </div>
        </div>
        <div class="card-body text-center">
          <div class="d-flex justify-content-center mb-3">
            <div class="avatar avatar-md flex-shrink-0">
              <span class="avatar-initial avatar-shadow-info rounded-circle"><i
                  class="icon-base bx bx-dollar icon-26px"></i></span>
            </div>
          </div>
          <h4 class="card-title mb-0">$1,271</h4>
          <p class="mb-2">34% of target</p>
          <span class="text-danger">-23% <i class="icon-base bx bx-chevron-down icon-lg"></i></span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="card-title fw-normal m-0 me-2">Customers</h6>
          <div class="dropdown">
            <button class="btn btn-text text-body-secondary p-0" type="button" id="customersList"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Today <i
                class="icon-base bx bx-chevron-down"></i></button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customersList">
              <a class="dropdown-item" href="javascript:void(0);">Yesterday</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            </div>
          </div>
        </div>
        <div class="card-body text-center">
          <div class="d-flex justify-content-center mb-3">
            <div class="avatar avatar-md flex-shrink-0">
              <span class="avatar-initial avatar-shadow-success rounded-circle"><i
                  class="icon-base bx bx-user icon-26px"></i></span>
            </div>
          </div>
          <h4 class="card-title mb-0">24,680</h4>
          <p class="mb-2">29% of target</p>
          <span class="text-success">+42% <i class="icon-base bx bx-chevron-up icon-lg"></i></span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="card-title fw-normal m-0 me-2">Orders Received</h6>
          <div class="dropdown">
            <button class="btn btn-text text-body-secondary p-0" type="button" id="orderReceivedList"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Today <i
                class="icon-base bx bx-chevron-down"></i></button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderReceivedList">
              <a class="dropdown-item" href="javascript:void(0);">Yesterday</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            </div>
          </div>
        </div>
        <div class="card-body text-center">
          <div class="d-flex justify-content-center mb-3">
            <div class="avatar avatar-md flex-shrink-0">
              <span class="avatar-initial avatar-shadow-warning rounded-circle"><i
                  class="icon-base bx bx-archive icon-26px"></i></span>
            </div>
          </div>
          <h4 class="card-title mb-0">1,862</h4>
          <p class="mb-2">47% of target</p>
          <span class="text-success">+82% <i class="icon-base bx bx-chevron-up icon-lg"></i></span>
        </div>
      </div>
    </div>
  </div>
  <!--/ Centered align Cards -->

  <!-- Cards with unicons & charts -->
  <div class="row g-6 mb-6">
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <img src="{{ asset('assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card" class="rounded" />
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
              </button>
              <div class="dropdown-menu" aria-labelledby="cardOpt1">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
              </div>
            </div>
          </div>
          <p class="mb-1">Transactions</p>
          <h4 class="card-title mb-3">$14,857</h4>
          <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +28.14%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <img src="{{ asset('assets/img/icons/unicons/cube-secondary.png') }}" alt="cube" class="rounded" />
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="cardOpt2" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt2">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
              </div>
            </div>
          </div>
          <p class="mb-1">Order</p>
          <h4 class="card-title mb-3">$1,286</h4>
          <small class="text-danger fw-medium"><i class="icon-base bx bx-down-arrow-alt"></i> -13.24%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" alt="chart success"
                class="rounded" />
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
              </div>
            </div>
          </div>
          <p class="mb-1">Profit</p>
          <h4 class="card-title mb-3">$12,628</h4>
          <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +72.80%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="paypal" class="rounded" />
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
              </div>
            </div>
          </div>
          <p class="mb-1">Payments</p>
          <h4 class="card-title mb-3">$2,456</h4>
          <small class="text-danger fw-medium"><i class="icon-base bx bx-down-arrow-alt"></i> -14.82%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <img src="{{ asset('assets/img/icons/unicons/computer.png') }}" alt="computer" class="rounded" />
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="cardOpt5" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt5">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
              </div>
            </div>
          </div>
          <p class="mb-1">Revenue</p>
          <h4 class="card-title mb-3">$42,389</h4>
          <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +52.18%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" alt="wallet info" class="rounded" />
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
              </div>
            </div>
          </div>
          <p class="mb-1">Sales</p>
          <h4 class="card-title mb-3">$4,679</h4>
          <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +28.42%</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card h-100">
        <div class="card-body pb-4">
          <span class="d-block fw-medium mb-1">Order</span>
          <h4 class="card-title mb-0">276k</h4>
        </div>
        <div id="orderChart" class="pe-1"></div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card h-100">
        <div class="card-body pb-0">
          <span class="d-block fw-medium mb-1">Revenue</span>
          <h4 class="card-title mb-8">425k</h4>
          <div id="revenueChart"></div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card h-100">
        <div class="card-body pb-0">
          <span class="d-block fw-medium mb-1">Profit</span>
          <h4 class="card-title mb-8">624k</h4>
          <div id="profitChart"></div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card h-100">
        <div class="card-body pb-4">
          <span class="d-block fw-medium mb-1">Sessions</span>
          <h4 class="card-title mb-0">2,845</h4>
        </div>
        <div id="sessionsChart" class="pb-4"></div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card h-100">
        <div class="card-body pb-0">
          <span class="d-block fw-medium mb-1">Expenses</span>
        </div>
        <div id="expensesChart" class="mb-2"></div>
        <div class="p-4 pt-2">
          <small class="d-block text-center">$21k Expenses more than last month</small>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
      <div class="card h-100">
        <div class="card-body">
          <span class="d-block fw-medium mb-1">Sales</span>
          <h4 class="card-title mb-3">482k</h4>
          <span class="badge bg-label-info mb-5">+34%</span>
          <small class="d-block mb-1">Sales Target</small>
          <div class="d-flex align-items-center">
            <div class="progress w-75 me-2" style="height: 8px;">
              <div class="progress-bar bg-info shadow-none" style="width: 78%" role="progressbar" aria-valuenow="78"
                aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small>78%</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Cards with unicons & charts -->

  <!-- Cards with charts & info -->
  <div class="row g-6 mb-6">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body row g-4 p-0">
          <div class="col-md-6">
            <div class="p-6 card-separator">
              <div class="card-title d-flex align-items-start justify-content-between">
                <h5 class="mb-0">New Visitors</h5>
                <small>Last Week</small>
              </div>
              <div class="d-flex justify-content-between">
                <div class="mt-auto">
                  <h3 class="mb-1">23%</h3>
                  <small class="text-danger text-nowrap fw-medium"><i class="icon-base bx bx-down-arrow-alt"></i>
                    -13.24%</small>
                </div>
                <div id="visitorsChart"></div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="p-6">
              <div class="card-title d-flex align-items-start justify-content-between">
                <h5 class="mb-0">Activity</h5>
                <small>Last Week</small>
              </div>
              <div class="d-flex justify-content-between mt-4">
                <div class="mt-auto">
                  <h3 class="mb-1">82%</h3>
                  <small class="text-success text-nowrap fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i>
                    24.8%</small>
                </div>
                <div id="activityChart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="d-flex flex-column">
              <div class="card-title mb-auto">
                <h5 class="mb-0">Generated Leads</h5>
                <p class="mb-0">Monthly Report</p>
              </div>
              <div class="chart-statistics">
                <h4 class="card-title mb-0">4,230</h4>
                <p class="text-success text-nowrap mb-0"><i class="icon-base bx bx-chevron-up icon-lg"></i> +12.8%</p>
              </div>
            </div>
            <div id="leadsReportChart"></div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Cards with charts & info -->
  </div>
@endsection
