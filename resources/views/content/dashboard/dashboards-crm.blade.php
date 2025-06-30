@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard - CRM')

@section('vendor-style')
@vite([
'resources/assets/vendor/fonts/flag-icons.scss',
'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/dashboards-crm.js'])
@endsection

@section('content')
<div class="row">
  <!-- Customer Ratings -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Customer Ratings</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="customerRatings" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customerRatings">
            <a class="dropdown-item" href="javascript:void(0);">Featured Ratings</a>
            <a class="dropdown-item" href="javascript:void(0);">Based on Task</a>
            <a class="dropdown-item" href="javascript:void(0);">See All</a>
          </div>
        </div>
      </div>
      <div class="card-body pb-0">
        <div class="d-flex align-items-center gap-2 mb-1">
          <h2 class="mb-0">4.0</h2>
          <div class="ratings">
            <i class="icon-base bx bxs-star icon-lg text-warning"></i>
            <i class="icon-base bx bxs-star icon-lg text-warning"></i>
            <i class="icon-base bx bxs-star icon-lg text-warning"></i>
            <i class="icon-base bx bxs-star icon-lg text-warning"></i>
            <i class="icon-base bx bxs-star icon-lg text-lighter"></i>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <span class="badge bg-label-primary me-2">+5.0</span>
          <span>Points from last month</span>
        </div>
      </div>
      <div id="customerRatingsChart"></div>
    </div>
  </div>
  <!--/ Customer Ratings -->
  <!-- Overview & Sales Activity -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100 gap-12">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title me-2">
          <h5 class="mb-1">Overview & Sales Activity</h5>
          <p class="card-subtitle">Check out each column for more details</p>
        </div>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="salesActivity" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesActivity">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body px-1 pb-0">
        <div id="salesActivityChart"></div>
      </div>
    </div>
  </div>
  <!--/ Overview & Sales Activity -->
  <div class="col-12 col-md-12 col-xxl-4">
    <div class="row">
      <div class="col-6 col-md-3 col-xxl-6 mb-6">
        <div class="card h-100">
          <div class="card-body pb-4">
            <span class="d-block fw-medium mb-1">Sessions</span>
            <h4 class="card-title mb-0">2,845</h4>
          </div>
          <div id="sessionsChart" class="mb-0"></div>
        </div>
      </div>
      <div class="col-6 col-md-3 col-xxl-6 mb-6">
        <div class="card h-100">
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
      <div class="col-12 col-md-6 col-xxl-12 mb-6">
        <div class="card h-100">
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
    </div>
  </div>
</div>
<div class="row">
  <!-- Top Products by -->
  <div class="col-12 col-xxl-8 mb-6">
    <div class="card h-100">
      <div class="row row-bordered g-0 h-100">
        <div class="col-md-6">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Top Products by <span class="text-primary">Sales</span></h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="topSales" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
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
              <button class="btn p-0" type="button" id="topVolume" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
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
                  <img src="{{ asset('assets/img/icons/unicons/computer.png') }}" alt="Apple" class="rounded" />
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
                  <img src="{{ asset('assets/img/icons/unicons/watch.png') }}" alt="Smart Watch" class="rounded" />
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

  <!-- Earning Reports -->
  <div class="col-md-6 col-xxl-4 mb-6">
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
      <div class="card-body pb-0">
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
              <span class="avatar-initial rounded bg-label-secondary"><i class="icon-base bx bx-credit-card"></i></span>
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

  <!-- Sales Analytics -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h5 class="card-title m-0 me-2 mb-2">Sales Analytics</h5>
          <span class="badge bg-label-success me-1">+42.6%</span> <span>Than last year</span>
        </div>
        <div class="btn-group">
          <button type="button" class="btn btn-sm btn-label-primary">2022</button>
          <button type="button" class="btn btn-sm btn-label-primary dropdown-toggle dropdown-toggle-split"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0);">2021</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">2020</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">2019</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body pb-0">
        <div id="salesAnalyticsChart"></div>
      </div>
    </div>
  </div>
  <!--/ Sales Analytics -->

  <!-- Sales By Country -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100">
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

  <!-- Sales Stats -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Sales Stats</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="salesStatsID" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesStatsID">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div id="salesStats"></div>
      <div class="card-body">
        <div class="d-flex justify-content-center align-items-center gap-4">
          <div class="d-flex align-items-center"><span class="badge badge-dot bg-success me-2"></span> Conversion Ratio
          </div>
          <div class="d-flex align-items-center"><span class="badge badge-dot bg-label-secondary me-2"></span> Total
            requirements</div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Sales Stats -->

  <!-- Team Members -->
  <div class="col-xxl-5 mb-xxl-0 mb-6">
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
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar me-3">
                    <img src="{{ asset('assets/img/avatars/11.png') }}" alt="Avatar" class="rounded-circle" />
                  </div>
                  <div class="d-flex flex-column">
                    <h6 class="mb-0 text-truncate">Allan kristian</h6>
                    <small class="text-truncate text-body">Frontend Designer</small>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-label-info text-uppercase">Crypter</span></td>
              <td><span class="fw-medium">690/760</span></td>
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

  <!-- Customer Table -->
  <div class="col-xxl-7 mb-0">
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="invoice-list-table table table-border-top-0">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Amount</th>
              <th>Status</th>
              <th class="cell-fit">Paid By</th>
              <th class="cell-fit">Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/7.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="{{ url('/pages/profile-user') }}" class="text-heading text-truncate fw-medium">Henry
                      Barnes</a>
                    <small class="text-truncate text-body">jok@puc.co.uk</small>
                  </div>
                </div>
              </td>
              <td>$459.65</td>
              <td><span class="badge bg-label-success"> Paid </span></td>
              <td class="text-center"><img
                  src="{{ asset('assets/img/icons/payments/master-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" width="29" alt="masterCard" data-app-light-img="icons/payments/master-light.png"
                  data-app-dark-img="icons/payments/master-dark.png"></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown"><i
                        class="icon-base bx bx-dots-vertical-rounded icon-md text-body"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                      <a href="javascript:;" class="dropdown-item">Duplicate</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/20.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="{{ url('/pages/profile-user') }}" class="text-heading text-truncate fw-medium">Hallie
                      Warner</a>
                    <small class="text-truncate text-body">hellie@war.co.uk</small>
                  </div>
                </div>
              </td>
              <td>$93.81</td>
              <td><span class="badge bg-label-warning"> Pending </span></td>
              <td class="text-center"><img
                  src="{{ asset('assets/img/icons/payments/master-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" width="29" alt="masterCard" data-app-light-img="icons/payments/master-light.png"
                  data-app-dark-img="icons/payments/master-dark.png"></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown"><i
                        class="icon-base bx bx-dots-vertical-rounded icon-md text-body"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                      <a href="javascript:;" class="dropdown-item">Duplicate</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/9.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="{{ url('/pages/profile-user') }}" class="text-heading text-truncate fw-medium">Gerald
                      Flowers</a>
                    <small class="text-truncate text-body">initus@odemi.com</small>
                  </div>
                </div>
              </td>
              <td>$934.35</td>
              <td><span class="badge bg-label-warning"> Pending </span></td>
              <td class="text-center"><img
                  src="{{ asset('assets/img/icons/payments/paypal_logo-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" width="29" alt="paypalCard"
                  data-app-light-img="icons/payments/paypal_logo-light.png"
                  data-app-dark-img="icons/payments/paypal_logo-dark.png"></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown"><i
                        class="icon-base bx bx-dots-vertical-rounded icon-md text-body"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                      <a href="javascript:;" class="dropdown-item">Duplicate</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/14.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="{{ url('/pages/profile-user') }}" class="text-heading text-truncate fw-medium">John
                      Davidson</a>
                    <small class="text-truncate text-body">jtum@upkesja.gov</small>
                  </div>
                </div>
              </td>
              <td>$794.97</td>
              <td><span class="badge bg-label-success"> Paid </span></td>
              <td class="text-center"><img
                  src="{{ asset('assets/img/icons/payments/master-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" width="29" alt="masterCard" data-app-light-img="icons/payments/master-light.png"
                  data-app-dark-img="icons/payments/master-dark.png"></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown"><i
                        class="icon-base bx bx-dots-vertical-rounded icon-md text-body"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                      <a href="javascript:;" class="dropdown-item">Duplicate</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-warning">JH</span>
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="{{ url('/pages/profile-user') }}" class="text-heading text-truncate fw-medium">Jayden
                      Harris</a>
                    <small class="text-truncate text-body">wipare@tin.com</small>
                  </div>
                </div>
              </td>
              <td>$19.49</td>
              <td><span class="badge bg-label-success"> Paid </span></td>
              <td class="text-center"><img
                  src="{{ asset('assets/img/icons/payments/master-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" width="29" alt="masterCard" data-app-light-img="icons/payments/master-light.png"
                  data-app-dark-img="icons/payments/master-dark.png"></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown"><i
                        class="icon-base bx bx-dots-vertical-rounded icon-md text-body"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                      <a href="javascript:;" class="dropdown-item">Duplicate</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/8.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="{{ url('/pages/profile-user') }}" class="text-heading text-truncate fw-medium">Rena
                      Ferguson</a>
                    <small class="text-truncate text-body">nur@kaomor.edu</small>
                  </div>
                </div>
              </td>
              <td>$636.27</td>
              <td><span class="badge bg-label-danger"> Failed </span></td>
              <td class="text-center"><img
                  src="{{ asset('assets/img/icons/payments/paypal_logo-' . $configData['theme'] . '.png') }}"
                  class="img-fluid" width="29" alt="paypalCard"
                  data-app-light-img="icons/payments/paypal_logo-light.png"
                  data-app-dark-img="icons/payments/paypal_logo-dark.png"></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown"><i
                        class="icon-base bx bx-dots-vertical-rounded icon-md text-body"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                      <a href="javascript:;" class="dropdown-item">Duplicate</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item delete-record text-danger">Delete</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--/ Customer Table -->
</div>
@endsection
