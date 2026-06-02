@extends('layouts/layoutMaster')

@section('title', 'eCommerce Dashboard - Apps')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/card-analytics.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js',)
@endsection

@section('page-script')
@vite('resources/assets/js/app-ecommerce-dashboard.js')
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 col-xxl-4 mb-6">
    <div class="card h-100">
      <div class="d-flex align-items-end row">
        <div class="col-7">
          <div class="card-body">
            <h5 class="card-title mb-1 text-nowrap">{{ __('Congratulations Katie!') }} 🎉</h5>
            <p class="card-subtitle text-nowrap mb-3">{{ __('Best seller of the month') }}</p>

            <h4 class="text-primary mb-2">${{ number_format($todaySales, 2) }}</h4>
            <p class="mb-3 text-{{ $dailyGrowth >= 0 ? 'success' : 'danger' }} fw-medium">
              {{ $dailyGrowth >= 0 ? '+' : '' }}{{ number_format($dailyGrowth, 1) }}% {{ __('growth') }}
            </p>

            <a href="javascript:;" class="btn btn-sm btn-primary mb-1">{{ __('View sales') }}</a>
          </div>
        </div>
        <div class="col-5">
          <div class="card-body pb-0 text-end">
            <img src="{{asset('assets/img/illustrations/prize-light.png')}}" width="91" height="144"
              class="rounded-start" alt="View Sales" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- New Visitors & Activity -->
  <div class="col-xxl-8 mb-6">
    <div class="card h-100">
      <div class="card-body row g-4 p-0">
        <div class="col-md-6 card-separator">
          <div class="p-6">
            <div class="card-title d-flex align-items-start justify-content-between">
              <h5 class="mb-0">{{ __('New Visitors') }}</h5>
              <small>{{ __('Last Week') }}</small>
            </div>
            <div class="d-flex justify-content-between">
              <div class="mt-auto">
                <h3 class="mb-1">${{ number_format($thisWeekSales, 2) }}</h3>
                <small class="text-{{ $weeklyGrowth >= 0 ? 'success' : 'danger' }} text-nowrap fw-medium">
                  <i class="icon-base bx bx-{{ $weeklyGrowth >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                  {{ number_format($weeklyGrowth, 1) }}%</small>
              </div>
              <div id="visitorsChart"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-6">
            <div class="card-title d-flex align-items-start justify-content-between">
              <h5 class="mb-0">{{ __('Activity') }}</h5>
              <small>{{ __('Last Week') }}</small>
            </div>
            <div class="d-flex justify-content-between">
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
  <!--/ New Visitors & Activity -->

  <div class="col-lg-12 col-xxl-4">
    <div class="row">
      <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0 w-px-40 h-px-40">
                <img src="{{asset('assets/img/icons/unicons/wallet-info.png')}}" alt="wallet info" class="rounded" />
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
            <h4 class="card-title mb-3">${{ number_format($totalSales, 2) }}</h4>
            <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +28.42%</small>
          </div>
        </div>
      </div>
      <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
        <div class="card h-100">
          <div class="card-body pb-2">
            <span class="d-block fw-medium mb-1">Profit</span>
            <h4 class="card-title mb-4">${{ number_format($totalProfit / 1000, 1) }}k</h4>
            <div id="profitChart"></div>
          </div>
        </div>
      </div>
      <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
        <div class="card h-100">
          <div class="card-body pb-0">
            <span class="d-block fw-medium mb-1">Expenses</span>
          </div>
          <div id="expensesChart" class="mb-2"></div>
          <div class="p-4 pt-2">
            <small class="d-block text-center">${{ number_format($totalExpenses / 1000, 1) }}k Expenses more than last month</small>
          </div>
        </div>
      </div>
      <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
        <div class="card h-100">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="{{asset('assets/img/icons/unicons/cc-primary.png')}}" alt="Credit Card" class="rounded" />
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
            <h4 class="card-title mb-3">{{ $totalTransactions }}</h4>
            <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +28.14%</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Income -->
  <div class="col-md-12 col-xxl-8 mb-6">
    <div class="card h-100">
      <div class="row row-bordered g-0">
        <div class="col-md-8">
          <div class="card-header d-flex justify-content-between">
            <div>
              <h5 class="card-title mb-1">Total Income</h5>
              <p class="card-subtitle">Yearly report overview</p>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalIncome" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
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
              <p class="card-subtitle">Monthly Avg. ${{ number_format($totalSales / 12 / 1000, 1) }}k</p>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalReport" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalReport">
                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
              </div>
            </div>
          </div>
          <div class="card-body pt-lg-6">
            <div class="report-list">
              <div class="report-list-item rounded-2 mb-4">
                <div class="d-flex align-items-center">
                  <div class="report-list-icon shadow-xs me-4">
                    <img src="{{asset('assets/svg/icons/paypal-icon.svg')}}" width="22" height="22" alt="Paypal" />
                  </div>
                  <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <div class="d-flex flex-column">
                      <span>Income</span>
                      <h5 class="mb-0">${{ number_format($totalRevenue, 2) }}</h5>
                    </div>
                    <small class="text-success">+2.34k</small>
                  </div>
                </div>
              </div>
              <div class="report-list-item rounded-2 mb-4">
                <div class="d-flex align-items-center">
                  <div class="report-list-icon shadow-xs me-4">
                    <img src="{{asset('assets/svg/icons/credit-card-icon.svg')}}" width="22" height="22"
                      alt="Shopping Bag" />
                  </div>
                  <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <div class="d-flex flex-column">
                      <span>Expense</span>
                      <h5 class="mb-0">${{ number_format($totalExpenses, 2) }}</h5>
                    </div>
                    <small class="text-danger">-1.15k</small>
                  </div>
                </div>
              </div>
              <div class="report-list-item rounded-2">
                <div class="d-flex align-items-center">
                  <div class="report-list-icon shadow-xs me-4">
                    <img src="{{asset('assets/svg/icons/wallet-icon.svg')}}" width="22" height="22" alt="Wallet" />
                  </div>
                  <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                    <div class="d-flex flex-column">
                      <span>Profit</span>
                      <h5 class="mb-0">${{ number_format($totalProfit, 2) }}</h5>
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
</div>
<div class="row">
  <!-- Performance -->
  <div class="col-md-6 col-xxl-4 mb-6">
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
            <p class="mb-0">Earnings: ${{ number_format($totalRevenue, 2) }}</p>
          </div>
          <div class="col-6">
            <p class="mb-0 text-end">Sales: {{ number_format($totalSales / 1000, 1) }}k</p>
          </div>
        </div>
        <div id="performanceChart"></div>
      </div>
    </div>
  </div>
  <!--/ Performance -->

  <!-- Conversion rate -->
  <div class="col-md-6 col-xxl-4 mb-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1 me-2">Conversion Rate</h5>
          <p class="card-subtitle">Compared To Last Month</p>
        </div>
        <div class="dropdown">
          <button class="btn text-body-secondary p-0" type="button" id="conversionRate" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
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
            <h3 class="mb-0">{{ number_format(($totalOrders / max($totalSales, 1)) * 100, 2) }}%</h3>
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
                <small>{{ number_format($totalOrders * 50) }} Visits</small>
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
                <small>{{ number_format($totalOrders * 5) }} Product in cart</small>
              </div>
              <div class="user-progress"><i class="icon-base bx icon-lg bx-down-arrow-alt text-danger me-2"></i> <span>-
                  8.5% </span></div>
            </div>
          </li>
          <li class="d-flex mb-6">
            <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-normal">Checkout</h6>
                <small>{{ number_format($totalOrders * 1.5) }} Products checkout</small>
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
                <small>{{ $totalOrders }} Orders</small>
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

  <div class="col-md-12 col-xxl-4">
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3 col-lg-6 mb-6">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between mb-4">
              <div class="avatar flex-shrink-0">
                <img src="{{asset('assets/img/icons/unicons/computer.png')}}" alt="computer" class="rounded" />
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
            <h4 class="card-title mb-3">${{ number_format($totalRevenue, 2) }}</h4>
            <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +52.18%</small>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3 col-lg-6 mb-6">
        <div class="card">
          <div class="card-body">
            <span class="d-block fw-medium mb-1">Sales</span>
            <h4 class="card-title mb-3">{{ number_format($totalSales / 1000, 1) }}k</h4>
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
      <div class="col-12 col-md-6 col-lg-12 mb-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap">
              <div class="d-flex align-items-start flex-column justify-content-between">
                <div class="card-title">
                  <h5 class="mb-0">Expenses</h5>
                </div>
                <div class="d-flex justify-content-between">
                  <div class="mt-auto">
                    <h4 class="mb-0">${{ number_format($totalExpenses, 2) }}</h4>
                    <span class="text-danger text-nowrap fw-medium"><i class="icon-base bx bx-down-arrow-alt"></i>
                      8.2%</span>
                  </div>
                </div>
                <span class="badge bg-label-secondary">{{ date('Y') }} YEAR</span>
              </div>
              <div id="expensesBarChart"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-7 col-xxl-8 mb-6 mb-lg-0">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Recent Orders</h5>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-sm text-nowrap table-border-top-0">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach($recentOrders as $item)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                   <div class="d-flex flex-column">
                    <h6 class="mb-0">{{ $item->order?->order_number ?? 'N/A' }}</h6>
                    <small class="text-body">{{ $item->product_name }}</small>
                  </div>
                </div>
              </td>
              <td>
                {{ $item->order?->created_at?->format('M d, Y') ?? 'N/A' }}
              </td>
              <td>
                <div class="text-body">{{ $item->order?->customer?->name ?? 'Guest' }}</div>
              </td>
              <td><span class="text-primary fw-medium">${{ number_format($item->price * $item->qty, 2) }}</span></td>
              <td><span class="badge bg-label-{{ ($item->order?->order_status ?? '') == 'Delivered' ? 'success' : 'primary' }}">{{ $item->order?->order_status ? ucfirst($item->order->order_status) : 'N/A' }}</span></td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
                      class="icon-base bx bx-dots-vertical-rounded"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('app-ecommerce-order-details', $item->order_id) }}"><i class="icon-base bx bx-show me-1"></i>
                      View Details</a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Total Balance -->
  <div class="col-lg-5 col-xxl-4">
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
</div>
</div>

@can('access_ai_assistant')
<!-- Floating AI Assistant Button (FAB) -->
<button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center position-fixed shadow-lg" id="aiCopilotBtn" style="bottom: 24px; right: 24px; width: 60px; height: 60px; z-index: 1050; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: linear-gradient(135deg, #696cff, #875ef5); border: none; box-shadow: 0 8px 24px rgba(105, 108, 255, 0.4) !important;">
  <i class="icon-base bx bx-bot icon-32px text-white animate-pulse"></i>
</button>

<!-- AI Assistant Copilot Panel -->
<div class="card position-fixed shadow-2xl d-none flex-column overflow-hidden" id="aiCopilotPanel" style="bottom: 96px; right: 24px; width: 400px; height: 580px; z-index: 1050; border-radius: 16px; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(105, 108, 255, 0.2); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);">
  
  <!-- Panel Header -->
  <div class="card-header d-flex align-items-center justify-content-between p-4 text-white" style="background: linear-gradient(135deg, #696cff, #875ef5); border-top-left-radius: 15px; border-top-right-radius: 15px;">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-md bg-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
        <i class="icon-base bx bx-bot text-primary icon-24px"></i>
      </div>
      <div>
        <h6 class="m-0 text-white fw-bold">Ak-Mart Copilot</h6>
        <div class="d-flex align-items-center gap-1">
          <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px; animation: pulse 1.5s infinite;"></span>
          <small class="text-white opacity-80" style="font-size: 0.75rem;">Active • Gemini AI</small>
        </div>
      </div>
    </div>
    <button type="button" class="btn-close btn-close-white" id="closeAiCopilot"></button>
  </div>

  <!-- Panel Body / Message Feed -->
  <div class="card-body p-4 flex-grow-1 overflow-y-auto d-flex flex-column gap-3" id="aiCopilotChatBody" style="background: rgba(248, 249, 250, 0.5);">
    <!-- Welcome message seeded in JS -->
  </div>

  <!-- Quick Prompts Row -->
  <div class="px-4 py-2 border-top d-flex gap-2 overflow-x-auto text-nowrap scrollbar-hidden" id="quickPrompts" style="background: rgba(248, 249, 250, 0.7); max-height: 48px; border-bottom: 1px solid rgba(0,0,0,0.05);">
    <button class="btn btn-xs btn-outline-primary quick-prompt-btn" data-prompt="✓ Show sales insights">✓ Insights</button>
    <button class="btn btn-xs btn-outline-warning quick-prompt-btn" data-prompt="⚠ Low stock warnings">⚠ Warnings</button>
    <button class="btn btn-xs btn-outline-info quick-prompt-btn" data-prompt="📈 Branch performance analysis">📈 Growth</button>
    <button class="btn btn-xs btn-outline-secondary quick-prompt-btn" data-prompt="💡 Recommend a marketing promotion idea">💡 Marketing</button>
  </div>

  <!-- Panel Footer / Input Area -->
  <div class="card-footer p-3 border-top bg-white d-flex align-items-center gap-2">
    <textarea class="form-control border-0 p-2 scrollbar-hidden" id="aiCopilotInput" rows="1" placeholder="Ask your copilot anything..." style="resize: none; background: #f8f9fa; border-radius: 8px; font-size: 0.9rem;"></textarea>
    <button class="btn btn-icon btn-primary d-flex align-items-center justify-content-center rounded-circle" id="aiCopilotSend" style="width: 40px; height: 40px; background: #696cff; border: none; transition: transform 0.2s;">
      <i class="icon-base bx bx-send text-white"></i>
    </button>
  </div>
</div>

<style>
  #quickPrompts::-webkit-scrollbar {
    display: none;
  }
  .scrollbar-hidden::-webkit-scrollbar {
    display: none;
  }
  .quick-prompt-btn {
    border-radius: 20px !important;
    font-size: 0.75rem !important;
    padding: 4px 12px !important;
  }
  .chat-bubble {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.9rem;
    line-height: 1.4;
    word-wrap: break-word;
    animation: slideUp 0.3s ease-out;
  }
  .chat-bubble-user {
    align-self: flex-end;
    background: #696cff;
    color: white;
    border-bottom-right-radius: 2px;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.2);
  }
  .chat-bubble-ai {
    align-self: flex-start;
    background: white;
    color: #435971;
    border-bottom-left-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(105, 108, 255, 0.1);
  }
  .chat-bubble-ai p {
    margin-bottom: 8px;
  }
  .chat-bubble-ai p:last-child {
    margin-bottom: 0;
  }
  .dark-style #aiCopilotPanel {
    background: rgba(43, 44, 64, 0.98) !important;
    border-color: rgba(105, 108, 255, 0.3) !important;
  }
  .dark-style #aiCopilotChatBody {
    background: rgba(35, 36, 51, 0.5) !important;
  }
  .dark-style .chat-bubble-ai {
    background: #2b2c40 !important;
    color: #e4e6fc !important;
    border-color: rgba(105, 108, 255, 0.2) !important;
  }
  .dark-style #aiCopilotInput {
    background: #232433 !important;
    color: #e4e6fc !important;
  }
  .dark-style .card-footer {
    background: #2b2c40 !important;
    border-color: rgba(255,255,255,0.05) !important;
  }
  
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.5); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(105, 108, 255, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
  }
  .animate-pulse {
    animation: iconPulse 2s infinite ease-in-out;
  }
  @keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
  }
  
  .typing-dots span {
    width: 8px;
    height: 8px;
    background-color: #696cff;
    border-radius: 50%;
    display: inline-block;
    animation: bounce 1.4s infinite both;
  }
  .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
  .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
  @keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const panel = document.getElementById('aiCopilotPanel');
  const btn = document.getElementById('aiCopilotBtn');
  const closeBtn = document.getElementById('closeAiCopilot');
  const chatBody = document.getElementById('aiCopilotChatBody');
  const input = document.getElementById('aiCopilotInput');
  const sendBtn = document.getElementById('aiCopilotSend');
  const quickPromptBtns = document.querySelectorAll('.quick-prompt-btn');

  let isPanelOpen = false;
  
  // Local state for chat conversation history
  let conversationHistory = [
    {
      role: 'model',
      content: "Hello! I am **Ak-Mart AI**, your advanced eCommerce Business Copilot. How can I help you manage, analyze, and optimize your business today?"
    }
  ];

  // Helper to format text with Markdown bold and bullet point icons beautifully
  function formatReplyText(text) {
    let formatted = text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/✓/g, '<span class="text-success fw-bold">✓</span>')
      .replace(/⚠/g, '<span class="text-warning fw-bold">⚠</span>')
      .replace(/📈/g, '<span class="text-info fw-bold">📈</span>')
      .replace(/💡/g, '<span class="text-primary fw-bold">💡</span>');
      
    // Replace bullet points or list items
    formatted = formatted.split('\n').map(line => {
      if (line.trim().startsWith('- ')) {
        return `<li>${line.trim().substring(2)}</li>`;
      }
      return `<p class="mb-2">${line}</p>`;
    }).join('');

    return formatted;
  }

  function renderMessages() {
    chatBody.innerHTML = '';
    conversationHistory.forEach(msg => {
      const bubble = document.createElement('div');
      bubble.className = `chat-bubble chat-bubble-${msg.role === 'user' ? 'user' : 'ai'}`;
      if (msg.role === 'model') {
        bubble.innerHTML = formatReplyText(msg.content);
      } else {
        bubble.innerText = msg.content;
      }
      chatBody.appendChild(bubble);
    });
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  // Toggle Panel
  btn.addEventListener('click', function () {
    isPanelOpen = !isPanelOpen;
    if (isPanelOpen) {
      panel.classList.remove('d-none');
      panel.classList.add('d-flex');
      renderMessages();
      input.focus();
    } else {
      panel.classList.add('d-none');
      panel.classList.remove('d-flex');
    }
  });

  closeBtn.addEventListener('click', function () {
    isPanelOpen = false;
    panel.classList.add('d-none');
    panel.classList.remove('d-flex');
  });

  // Sending a message
  function sendMessage(text) {
    if (!text.trim()) return;

    // Add user message to local state
    conversationHistory.push({ role: 'user', content: text });
    renderMessages();
    input.value = '';

    // Show Typing Indicator
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'chat-bubble chat-bubble-ai d-flex align-items-center gap-1 typing-indicator-bubble';
    typingIndicator.innerHTML = `
      <div class="typing-dots d-flex gap-1 py-1">
        <span></span><span></span><span></span>
      </div>
    `;
    chatBody.appendChild(typingIndicator);
    chatBody.scrollTop = chatBody.scrollHeight;

    // Disable input and send button during execution
    input.disabled = true;
    sendBtn.disabled = true;

    // Send payload to backend copilot API
    fetch('{{ route("app-ai-copilot-chat") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        messages: conversationHistory
      })
    })
    .then(res => res.json())
    .then(data => {
      // Remove typing indicator
      const indicators = document.querySelectorAll('.typing-indicator-bubble');
      indicators.forEach(i => i.remove());

      if (data.success) {
        // Add AI reply to history
        conversationHistory.push({ role: 'model', content: data.reply });
      } else {
        conversationHistory.push({
          role: 'model',
          content: "⚠ **Error:** " + (data.message || "Failed to communicate with AI Copilot. Please check your Gemini API key.")
        });
      }
      renderMessages();
    })
    .catch(err => {
      const indicators = document.querySelectorAll('.typing-indicator-bubble');
      indicators.forEach(i => i.remove());
      
      conversationHistory.push({
        role: 'model',
        content: "⚠ **Connection Failure:** Could not connect to the Copilot service. Please try again."
      });
      renderMessages();
    })
    .finally(() => {
      input.disabled = false;
      sendBtn.disabled = false;
      input.focus();
    });
  }

  sendBtn.addEventListener('click', function () {
    sendMessage(input.value);
  });

  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage(input.value);
    }
  });

  // Handle Quick Prompts click
  quickPromptBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const prompt = this.getAttribute('data-prompt');
      sendMessage(prompt);
    });
  });

  // Render initial welcome message
  renderMessages();
});
</script>
@endcan
@endsection