@extends('layouts/layoutMaster')

@section('title', 'Cards Analytics- UI elements')
@section('content')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/card-analytics.scss'])
@endsection

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/ui-cards-analytics.js'])
@endsection

<div class="row g-6">
  <!-- Customer Ratings -->
  <div class="col-md-6 col-xl-4">
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

  <!-- Sales Stats -->
  <div class="col-md-6 col-xl-4">
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

  <!-- Sales Analytics -->
  <div class="col-lg-6 col-xl-4">
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

  <!-- Overview & Sales Activity -->
  <div class="col-lg-6 col-xl-4">
    <div class="card h-100">
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

  <!-- Total Income -->
  <div class="col-xl-8">
    <div class="card">
      <div class="row row-bordered g-0">
        <div class="col-md-8">
          <div class="card-header d-flex justify-content-between">
            <div>
              <h5 class="card-title mb-1">Total Income</h5>
              <p class="card-subtitle">Yearly report overview</p>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalIncome" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalIncome">
                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div id="totalIncomeChart"></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-header d-flex justify-content-between">
            <div>
              <h5 class="card-title mb-1">Report</h5>
              <p class="card-subtitle">Monthly Avg. $45.578k</p>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalReport" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalReport">
                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
              </div>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="report-list">
              <div class="report-list-item rounded-2 mb-4">
                <div class="d-flex align-items-center">
                  <div class="report-list-icon shadow-xs me-4">
                    <img src="{{ asset('assets/svg/icons/paypal-icon.svg') }}" width="22" height="22"
                      alt="Paypal" />
                  </div>
                  <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <div class="d-flex flex-column">
                      <span>Income</span>
                      <h5 class="mb-0">$42,845</h5>
                    </div>
                    <small class="text-success">+2.34k</small>
                  </div>
                </div>
              </div>
              <div class="report-list-item rounded-2 mb-4">
                <div class="d-flex align-items-center">
                  <div class="report-list-icon shadow-xs me-4">
                    <img src="{{ asset('assets/svg/icons/credit-card-icon.svg') }}" width="22" height="22"
                      alt="Shopping Bag" />
                  </div>
                  <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <div class="d-flex flex-column">
                      <span>Expense</span>
                      <h5 class="mb-0">$38,658</h5>
                    </div>
                    <small class="text-danger">-1.15k</small>
                  </div>
                </div>
              </div>
              <div class="report-list-item rounded-2">
                <div class="d-flex align-items-center">
                  <div class="report-list-icon shadow-xs me-4">
                    <img src="{{ asset('assets/svg/icons/wallet-icon.svg') }}" width="22" height="22"
                      alt="Wallet" />
                  </div>
                  <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <div class="d-flex flex-column">
                      <span>Profit</span>
                      <h5 class="mb-0">$18,220</h5>
                    </div>
                    <small class="text-success">+1.35k</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Total Income -->
  </div>
  <!--/ Total Income -->

  <!-- Expense Overview -->
  <div class="col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-header nav-align-top">
        <ul class="nav nav-pills" role="tablist">
          <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
              data-bs-target="#navs-tabs-line-card-income" aria-controls="navs-tabs-line-card-income"
              aria-selected="true">Income</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab">Expenses</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab">Profit</button>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content p-0">
          <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
            <div class="d-flex mb-6">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/wallet-primary.png') }}" alt="User" />
              </div>
              <div>
                <p class="mb-0">Total Balance</p>
                <div class="d-flex align-items-center">
                  <h6 class="mb-0 me-1">$459.10</h6>
                  <small class="text-success fw-medium">
                    <i class="icon-base bx bx-chevron-up icon-lg"></i>
                    42.9%
                  </small>
                </div>
              </div>
            </div>
            <div id="incomeChart"></div>
            <div class="d-flex align-items-center justify-content-center mt-6 gap-3">
              <div class="flex-shrink-0">
                <div id="expensesOfWeek"></div>
              </div>
              <div>
                <h6 class="mb-0">Income this week</h6>
                <small>$39k less than last week</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Expense Overview -->

  <!-- Performance -->
  <div class="col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Performance</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="performanceId" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="performanceId">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row justify-content-between mb-5">
          <div class="col-6">
            <p class="mb-0">Earnings: $846.17</p>
          </div>
          <div class="col-6">
            <p class="mb-0 text-end">Sales: 25.7M</p>
          </div>
        </div>
        <div id="performanceChart"></div>
      </div>
    </div>
  </div>
  <!--/ Performance -->

  <!-- Total Balance -->
  <div class="col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Total Balance</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="totalBalance" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalBalance">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body pb-0">
        <div class="row">
          <div class="col d-flex">
            <div class="me-3">
              <span class="badge rounded-2 bg-label-warning p-2"><i
                  class="icon-base bx bx-wallet icon-lg text-warning"></i></span>
            </div>
            <div>
              <h6 class="mb-0">$2.54k</h6>
              <small>Wallet</small>
            </div>
          </div>
          <div class="col d-flex">
            <div class="me-3">
              <span class="badge rounded-2 bg-label-secondary p-2"><i
                  class="icon-base bx bx-dollar icon-lg text-secondary"></i></span>
            </div>
            <div>
              <h6 class="mb-0">$4.2k</h6>
              <small>Paypal</small>
            </div>
          </div>
        </div>
        <div id="totalBalanceChart"></div>
      </div>
      <hr class="m-0" />
      <div class="card-footer">
        <div class="d-flex justify-content-between">
          <small class="text-body">You have done 57.6% more sales.<br />Check your new badge in your profile.</small>
          <div>
            <span class="badge bg-label-warning rounded-2 p-2"><i
                class="icon-base bx bx-chevron-right icon-md text-warning"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Balance -->

  <!-- Score -->
  <div class="col-md-6 col-xxl-4">
    <div class="card h-100">
      <div class="card-header text-center">
        <p class="text-body mb-0">Your score is</p>
        <h5 class="card-title mb-0">Awesome</h5>
      </div>
      <div class="card-body">
        <div id="scoreChart"></div>

        <div class="d-flex flex-column align-items-center mt-2">
          <p class="mb-0">Your score is based on the last</p>
          <h6>287 Transactions</h6>
          <a href="javascript:void(0)" class="btn btn-primary" role="button">View My Account</a>
        </div>
      </div>
    </div>
  </div>
  <!--/ Score -->

  <!-- Total Revenue -->
  <div class="col-12 col-xxl-8">
    <div class="card">
      <div class="row row-bordered g-0">
        <div class="col-lg-8">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Total Revenue</h5>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalRevenue" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalRevenue">
                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                <a class="dropdown-item" href="javascript:void(0);">Share</a>
              </div>
            </div>
          </div>
          <div id="totalRevenueChart" class="px-3"></div>
        </div>
        <div class="col-lg-4">
          <div class="card-body px-xl-9 d-flex align-items-center flex-column" style="position: relative;">
            <div class="text-center mb-6">
              <div class="btn-group">
                <button type="button" class="btn btn-label-primary">
                  <script>
                    document.write(new Date().getFullYear() - 1);
                  </script>
                </button>
                <button type="button" class="btn btn-label-primary dropdown-toggle dropdown-toggle-split"
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

            <div id="growthChart"></div>
            <div class="text-center fw-medium my-6">62% Company Growth</div>

            <div class="d-flex gap-6 justify-content-between">
              <div class="d-flex">
                <div class="avatar me-2">
                  <span class="avatar-initial rounded-2 bg-label-primary"><i
                      class="icon-base bx bx-dollar icon-lg text-primary"></i></span>
                </div>
                <div class="d-flex flex-column">
                  <small>
                    <script>
                      document.write(new Date().getFullYear() - 1);
                    </script>
                  </small>
                  <h6 class="mb-0">$32.5k</h6>
                </div>
              </div>
              <div class="d-flex">
                <div class="avatar me-2">
                  <span class="avatar-initial rounded-2 bg-label-info"><i
                      class="icon-base bx bx-wallet icon-lg text-info"></i></span>
                </div>
                <div class="d-flex flex-column">
                  <small>
                    <script>
                      document.write(new Date().getFullYear() - 2);
                    </script>
                  </small>
                  <h6 class="mb-0">$41.2k</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Revenue -->

  <!-- Vehicles overview -->
  <div class="col-xxl-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="m-0 me-2">Vehicles Overview</h5>
        </div>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="vehiclesOverview" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="vehiclesOverview">
            <a class="dropdown-item" href="javascript:void(0);">Select All</a>
            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
            <a class="dropdown-item" href="javascript:void(0);">Share</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-none d-lg-flex vehicles-progress-labels mb-6">
          <div class="vehicles-progress-label on-the-way-text" style="width: 39.7%;">On the way</div>
          <div class="vehicles-progress-label unloading-text" style="width: 28.3%;">Unloading</div>
          <div class="vehicles-progress-label loading-text" style="width: 17.4%;">Loading</div>
          <div class="vehicles-progress-label waiting-text text-nowrap" style="width: 14.6%;">Waiting</div>
        </div>
        <div class="vehicles-overview-progress progress rounded-3 mb-6 bg-transparent overflow-hidden"
          style="height: 46px;">
          <div class="progress-bar fw-medium text-start shadow-none bg-lighter text-heading px-4 rounded-0"
            role="progressbar" style="width: 39.7%" aria-valuenow="39.7" aria-valuemin="0" aria-valuemax="100">
            39.7%</div>
          <div class="progress-bar fw-medium text-start shadow-none bg-primary px-4" role="progressbar"
            style="width: 28.3%" aria-valuenow="28.3" aria-valuemin="0" aria-valuemax="100">28.3%</div>
          <div class="progress-bar fw-medium text-start shadow-none text-bg-info px-2 px-sm-4" role="progressbar"
            style="width: 17.4%" aria-valuenow="17.4" aria-valuemin="0" aria-valuemax="100">17.4%</div>
          <div class="progress-bar fw-medium text-start shadow-none snackbar text-paper px-1 px-sm-3 rounded-0 px-lg-4"
            role="progressbar" style="width: 14.6%" aria-valuenow="14.6" aria-valuemin="0" aria-valuemax="100">
            14.6%</div>
        </div>
        <div class="table-responsive">
          <table class="table card-table table-border-top-0">
            <tbody class="table-border-bottom-0">
              <tr>
                <td class="w-50 ps-0">
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="me-2">
                      <i class="icon-base bx bx-car icon-lg text-heading"></i>
                    </div>
                    <h6 class="mb-0 fw-normal">On the way</h6>
                  </div>
                </td>
                <td class="text-end pe-0 text-nowrap">
                  <h6 class="mb-0">2hr 10min</h6>
                </td>
                <td class="text-end pe-0">
                  <span>39.7%</span>
                </td>
              </tr>
              <tr>
                <td class="w-50 ps-0">
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="me-2">
                      <i class="icon-base bx bx-down-arrow-circle icon-lg text-heading"></i>
                    </div>
                    <h6 class="mb-0 fw-normal">Unloading</h6>
                  </div>
                </td>
                <td class="text-end pe-0 text-nowrap">
                  <h6 class="mb-0">3hr 15min</h6>
                </td>
                <td class="text-end pe-0">
                  <span>28.3%</span>
                </td>
              </tr>
              <tr>
                <td class="w-50 ps-0">
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="me-2">
                      <i class="icon-base bx bx-up-arrow-circle icon-lg text-heading"></i>
                    </div>
                    <h6 class="mb-0 fw-normal">Loading</h6>
                  </div>
                </td>
                <td class="text-end pe-0 text-nowrap">
                  <h6 class="mb-0">1hr 24min</h6>
                </td>
                <td class="text-end pe-0">
                  <span>17.4%</span>
                </td>
              </tr>
              <tr>
                <td class="w-50 ps-0">
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="me-2">
                      <i class="icon-base bx bx-time-five icon-lg text-heading"></i>
                    </div>
                    <h6 class="mb-0 fw-normal">Waiting</h6>
                  </div>
                </td>
                <td class="text-end pe-0 text-nowrap">
                  <h6 class="mb-0">5hr 19min</h6>
                </td>
                <td class="text-end pe-0">
                  <span>14.6%</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!--/ Vehicles overview -->

  <!-- Shipment statistics-->
  <div class="col-xxl-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Shipment statistics</h5>
          <p class="card-subtitle">Total number of deliveries 23.8k</p>
        </div>
        <div class="btn-group">
          <button type="button" class="btn btn-label-primary">January</button>
          <button type="button" class="btn btn-label-primary dropdown-toggle dropdown-toggle-split"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0);">January</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">February</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">March</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">April</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">May</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">June</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">July</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">August</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">September</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">October</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">November</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">December</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <div id="shipmentStatisticsChart"></div>
      </div>
    </div>
  </div>
  <!--/ Shipment statistics -->

  <!-- Courses-->
  <div class="col-12 col-xxl-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Topic you are interested in</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="topic" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topic">
            <a class="dropdown-item" href="javascript:void(0);">Highest Views</a>
            <a class="dropdown-item" href="javascript:void(0);">See All</a>
          </div>
        </div>
      </div>
      <div class="card-body row g-3">
        <div class="col-md-8">
          <div id="horizontalBarChart"></div>
        </div>
        <div class="col-md-4 d-flex justify-content-around align-items-center">
          <div>
            <div class="d-flex align-items-baseline">
              <span class="text-primary me-2"><i class="icon-base bx bxs-circle bx-12px"></i></span>
              <div>
                <p class="mb-0">UI Design</p>
                <h5>35%</h5>
              </div>
            </div>
            <div class="d-flex align-items-baseline my-12">
              <span class="text-success me-2"><i class="icon-base bx bxs-circle bx-12px"></i></span>
              <div>
                <p class="mb-0">Music</p>
                <h5>14%</h5>
              </div>
            </div>
            <div class="d-flex align-items-baseline">
              <span class="text-danger me-2"><i class="icon-base bx bxs-circle bx-12px"></i></span>
              <div>
                <p class="mb-0">React</p>
                <h5>10%</h5>
              </div>
            </div>
          </div>

          <div>
            <div class="d-flex align-items-baseline">
              <span class="text-info me-2"><i class="icon-base bx bxs-circle bx-12px"></i></span>
              <div>
                <p class="mb-0">UX Design</p>
                <h5>20%</h5>
              </div>
            </div>
            <div class="d-flex align-items-baseline my-12">
              <span class="text-secondary me-2"><i class="icon-base bx bxs-circle bx-12px"></i></span>
              <div>
                <p class="mb-0">Animation</p>
                <h5>12%</h5>
              </div>
            </div>
            <div class="d-flex align-items-baseline">
              <span class="text-warning me-2"><i class="icon-base bx bxs-circle bx-12px"></i></span>
              <div>
                <p class="mb-0">SEO</p>
                <h5>9%</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Courses-->

  <!-- Reasons for delivery exceptions -->
  <div class="col-lg-6 col-xxl-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="m-0 me-2">Reasons for delivery exceptions</h5>
        </div>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="deliveryExceptions" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryExceptions">
            <a class="dropdown-item" href="javascript:void(0);">Select All</a>
            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
            <a class="dropdown-item" href="javascript:void(0);">Share</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="deliveryExceptionsChart"></div>
      </div>
    </div>
  </div>
  <!--/ Reasons for delivery exceptions -->

  <!-- Session Overview -->
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Session Overview</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="sessionOverview" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sessionOverview">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row align-items-center g-5 mb-8">
          <div class="col-lg-6">
            <h3 class="mb-0 text-nowrap">32,754</h3>
            <span class="text-success">+0.7645%</span>
          </div>
          <div class="col-lg-6">
            <div class="d-flex justify-content-between">
              <div class="d-flex flex-column">
                <small class="text-body-secondary">Today</small>
                <span class="fw-medium">+ $340</span>
              </div>
              <div class="d-flex flex-column">
                <small class="text-body-secondary">Last Week</small>
                <span class="fw-medium">+ $680</span>
              </div>
              <div class="d-flex flex-column">
                <small class="text-body-secondary">Today</small>
                <span class="fw-medium">+ $3,540</span>
              </div>
            </div>
          </div>
        </div>
        <div class="row align-items-center g-4">
          <div class="col-md-5">
            <div id="sessionOverviewChart" class="ms-n3"></div>
          </div>
          <div class="col-md-7 d-flex flex-column justify-content-between">
            <div class="progress-wrapper mb-4">
              <div class="mb-3">
                <small class="text-body-secondary">Effective Return</small>
                <div class="d-flex align-items-center">
                  <div class="progress w-100 me-2" style="height:8px">
                    <div class="progress-bar bg-primary shadow-none" style="width: 74%" role="progressbar"
                      aria-valuenow="74" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <small class="text-body-secondary">74%</small>
                </div>
              </div>
              <div>
                <small class="text-body-secondary">Invalid Session</small>
                <div class="d-flex align-items-center">
                  <div class="progress w-100 me-2" style=" height:8px">
                    <div class="progress-bar bg-primary shadow-none" style="width: 40%" role="progressbar"
                      aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <small class="text-body-secondary">40%</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Session Overview -->
</div>
@endsection
