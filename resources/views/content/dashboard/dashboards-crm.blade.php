@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Dashboard - CRM'))

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
        <h5 class="card-title m-0 me-2">{{ __('Customer Ratings') }}</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="customerRatings" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customerRatings">
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Featured Ratings') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Based on Task') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('See All') }}</a>
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
          <span>{{ __('Points from last month') }}</span>
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
          <h5 class="mb-1">{{ __('Overview & Sales Activity') }}</h5>
          <p class="card-subtitle">{{ __('Check out each column for more details') }}</p>
        </div>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="salesActivity" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesActivity">
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
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
            <span class="d-block fw-medium mb-1">{{ __('Sessions') }}</span>
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
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('View More') }}</a>
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('Delete') }}</a>
                </div>
              </div>
            </div>
            <p class="mb-1">{{ __('Order') }}</p>
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
                  <h5 class="mb-0">{{ __('Generated Leads') }}</h5>
                  <p class="mb-0">{{ __('Monthly Report') }}</p>
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
            <h5 class="card-title m-0 me-2">{{ __('Top Products by') }} <span class="text-primary">{{ __('Sales') }}</span></h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="topSales" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topSales">
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
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
                    <small class="d-block">Microsoft</small>
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
                    <h6 class="mb-0">iPhone 13</h6>
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
            <h5 class="card-title m-0 me-2">{{ __('Top Products by') }} <span class="text-primary">{{ __('Volume') }}</span></h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="topVolume" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topVolume">
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
              </div>
            </div>
          </div>
          <div class="card-body pt-6">
            <ul class="p-0 m-0">
              <li class="d-flex align-items-center mb-7">
                <div class="avatar flex-shrink-0 me-3">
                  <img src="{{ asset('assets/img/icons/unicons/laptop-secondary.png') }}" alt="ENVY x360" />
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">ENVY x360</h6>
                    <small class="d-block">HP</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <span class="fw-medium">12.4k</span>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center mb-7">
                <div class="avatar flex-shrink-0 me-3">
                  <img src="{{ asset('assets/img/icons/unicons/computer.png') }}" alt="Apple" />
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Apple</h6>
                    <small class="d-block">iMac</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <span class="fw-medium">74.9k</span>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center mb-7">
                <div class="avatar flex-shrink-0 me-3">
                  <img src="{{ asset('assets/img/icons/unicons/watch.png') }}" alt="Smart Watch" />
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Smart Watch</h6>
                    <small class="d-block">Fitbit</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <span class="fw-medium">4.4k</span>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center mb-7">
                <div class="avatar flex-shrink-0 me-3">
                  <img src="{{ asset('assets/img/icons/unicons/shoe.png') }}" alt="Nike" />
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Nike Air Max</h6>
                    <small class="d-block">Nike</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <span class="fw-medium">12.3k</span>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center">
                <div class="avatar flex-shrink-0 me-3">
                  <img src="{{ asset('assets/img/icons/unicons/headphone.png') }}" alt="Headphone" />
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Wireless Headphone</h6>
                    <small class="d-block">Sony</small>
                  </div>
                  <div class="user-progress d-flex align-items-center gap-1">
                    <span class="fw-medium">1.2k</span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Top Products by -->
  <!-- Earning Reports -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ __('Earning Reports') }}</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="earningReports" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReports">
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
          <h2 class="mb-0">$468</h2>
          <span class="badge bg-label-success">+4.2%</span>
        </div>
        <small class="text-body-secondary">{{ __('Weekly Earnings Overview') }}</small>
        <div id="earningReportsChart"></div>
        <div class="border rounded p-4 mt-5">
          <div class="row gap-4 gap-sm-0">
            <div class="col-12 col-sm-4">
              <div class="d-flex align-items-center gap-2">
                <div class="badge bg-label-primary p-1_5 rounded"><i class="icon-base bx bx-dollar icon-md"></i></div>
                <h6 class="mb-0">{{ __('Net Profit') }}</h6>
              </div>
              <h4 class="my-2">$1,645</h4>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0"
                  aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-12 col-sm-4">
              <div class="d-flex align-items-center gap-2">
                <div class="badge bg-label-info p-1_5 rounded"><i class="icon-base bx bx-wallet icon-md"></i></div>
                <h6 class="mb-0">{{ __('Total Income') }}</h6>
              </div>
              <h4 class="my-2">$4,860</h4>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: 50%;" aria-valuenow="50"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-12 col-sm-4">
              <div class="d-flex align-items-center gap-2">
                <div class="badge bg-label-danger p-1_5 rounded"><i class="icon-base bx bx-credit-card icon-md"></i></div>
                <h6 class="mb-0">{{ __('Total Expense') }}</h6>
              </div>
              <h4 class="my-2">$3,215</h4>
              <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: 35%;" aria-valuenow="35"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Earning Reports -->
</div>
@endsection
