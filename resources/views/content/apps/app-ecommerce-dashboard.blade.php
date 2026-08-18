@extends('layouts/layoutMaster')

@section('title', __('eCommerce Analytics') . ' — AK-Mart')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/card-analytics.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
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
            <h5 class="card-title mb-1 text-nowrap">{{ __('Welcome to AK-Mart') }} 🎉</h5>
            <p class="card-subtitle text-nowrap mb-3">{{ __('Smart Management for Modern Stores') }}</p>

            <h4 class="text-primary mb-2">${{ number_format($todaySales, 2) }}</h4>
            <p class="mb-3 text-{{ $dailyGrowth >= 0 ? 'success' : 'danger' }} fw-medium">
              {{ $dailyGrowth >= 0 ? '+' : '' }}{{ number_format($dailyGrowth, 1) }}% {{ __('today growth') }}
            </p>

            <a href="{{ route('app-ecommerce-order-list') }}" class="btn btn-sm btn-primary mb-1">{{ __('View orders') }}</a>
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
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('View More') }}</a>
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('Delete') }}</a>
                </div>
              </div>
            </div>
            <p class="mb-1">{{ __('Sales') }}</p>
            <h4 class="card-title mb-3">${{ number_format($totalSales, 2) }}</h4>
            <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +28.42%</small>
          </div>
        </div>
      </div>
      <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
        <div class="card h-100">
          <div class="card-body pb-2">
            <span class="d-block fw-medium mb-1">{{ __('Profit') }}</span>
            <h4 class="card-title mb-4">${{ number_format($totalProfit / 1000, 1) }}k</h4>
            <div id="profitChart"></div>
          </div>
        </div>
      </div>
      <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
        <div class="card h-100">
          <div class="card-body pb-0">
            <span class="d-block fw-medium mb-1">{{ __('Expenses') }}</span>
          </div>
          <div id="expensesChart" class="mb-2"></div>
          <div class="p-4 pt-2">
            <small class="d-block text-center">${{ number_format($totalExpenses / 1000, 1) }}k {{ __('Expenses more than last month') }}</small>
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
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('View More') }}</a>
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('Delete') }}</a>
                </div>
              </div>
            </div>
            <p class="mb-1">{{ __('Transactions') }}</p>
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
              <h5 class="card-title mb-1">{{ __('Total Income') }}</h5>
              <p class="card-subtitle">{{ __('Yearly report overview') }}</p>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalIncome" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalIncome">
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
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
              <h5 class="card-title mb-1">{{ __('Report') }}</h5>
              <p class="card-subtitle">{{ __('Monthly Avg.') }} ${{ number_format($totalSales / 12 / 1000, 1) }}k</p>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="totalReport" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalReport">
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
                <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
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
                      <span>{{ __('Income') }}</span>
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
                      <span>{{ __('Expense') }}</span>
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
                      <span>{{ __('Profit') }}</span>
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
        <h5 class="card-title m-0 me-2">{{ __('Performance') }}</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="performanceId" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="performanceId">
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row justify-content-between mb-5">
          <div class="col-6">
            <p class="mb-0">{{ __('Earnings:') }} ${{ number_format($totalRevenue, 2) }}</p>
          </div>
          <div class="col-6">
            <p class="mb-0 text-end">{{ __('Sales:') }} {{ number_format($totalSales / 1000, 1) }}k</p>
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
          <h5 class="mb-1 me-2">{{ __('Conversion Rate') }}</h5>
          <p class="card-subtitle">{{ __('Compared To Last Month') }}</p>
        </div>
        <div class="dropdown">
          <button class="btn text-body-secondary p-0" type="button" id="conversionRate" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="conversionRate">
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Select All') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Refresh') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Share') }}</a>
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
                <h6 class="mb-0 fw-normal">{{ __('Impressions') }}</h6>
                <small>{{ number_format($totalOrders * 50) }} {{ __('Visits') }}</small>
              </div>
              <div class="user-progress"><i class="icon-base bx icon-lg bx-up-arrow-alt text-success me-2"></i>
                <span>12.8%</span>
              </div>
            </div>
          </li>
          <li class="d-flex mb-6">
            <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-normal">{{ __('Added To Cart') }}</h6>
                <small>{{ number_format($totalOrders * 5) }} {{ __('Product in cart') }}</small>
              </div>
              <div class="user-progress"><i class="icon-base bx icon-lg bx-down-arrow-alt text-danger me-2"></i> <span>-
                  8.5% </span></div>
            </div>
          </li>
          <li class="d-flex mb-6">
            <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-normal">{{ __('Checkout') }}</h6>
                <small>{{ number_format($totalOrders * 1.5) }} {{ __('Products checkout') }}</small>
              </div>
              <div class="user-progress"><i class="icon-base bx icon-lg bx-up-arrow-alt text-success me-2"></i>
                <span>9.12%</span>
              </div>
            </div>
          </li>
          <li class="d-flex">
            <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-normal">{{ __('Purchased') }}</h6>
                <small>{{ $totalOrders }} {{ __('Orders') }}</small>
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
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('View More') }}</a>
                  <a class="dropdown-item" href="javascript:void(0);">{{ __('Delete') }}</a>
                </div>
              </div>
            </div>
            <p class="mb-1">{{ __('Revenue') }}</p>
            <h4 class="card-title mb-3">${{ number_format($totalRevenue, 2) }}</h4>
            <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +52.18%</small>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3 col-lg-6 mb-6">
        <div class="card">
          <div class="card-body">
            <span class="d-block fw-medium mb-1">{{ __('Sales') }}</span>
            <h4 class="card-title mb-3">{{ number_format($totalSales / 1000, 1) }}k</h4>
            <span class="badge bg-label-info mb-5">+34%</span>
            <small class="d-block mb-1">{{ __('Sales Target') }}</small>
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
                  <h5 class="mb-0">{{ __('Expenses') }}</h5>
                </div>
                <div class="d-flex justify-content-between">
                  <div class="mt-auto">
                    <h4 class="mb-0">${{ number_format($totalExpenses, 2) }}</h4>
                    <span class="text-danger text-nowrap fw-medium"><i class="icon-base bx bx-down-arrow-alt"></i>
                      8.2%</span>
                  </div>
                </div>
                <span class="badge bg-label-secondary">{{ date('Y') }} {{ __('YEAR') }}</span>
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
        <h5 class="card-title mb-0">{{ __('Recent Orders') }}</h5>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-sm text-nowrap table-border-top-0">
          <thead>
            <tr>
              <th>{{ __('Order ID') }}</th>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Customer') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach($recentOrders as $order)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                   <div class="d-flex flex-column">
                    <h6 class="mb-0">{{ $order->order_number ?? ('ORD-'.$order->id) }}</h6>
                    <small class="text-body">{{ $order->customer?->email ?? __('Store Customer') }}</small>
                  </div>
                </div>
              </td>
              <td>
                {{ $order->created_at?->format('M d, Y') ?? 'N/A' }}
              </td>
              <td>
                <div class="text-body">{{ $order->customer?->name ?? __('Guest') }}</div>
              </td>
              <td><span class="text-primary fw-medium">${{ number_format($order->total_amount, 2) }}</span></td>
              <td>
                @php
                  $statusColors = [
                    'completed' => 'success',
                    'pending' => 'warning',
                    'processing' => 'info',
                    'cancelled' => 'danger'
                  ];
                  $status = $order->order_status ?? 'pending';
                  $badgeColor = $statusColors[$status] ?? 'primary';
                @endphp
                <span class="badge bg-label-{{ $badgeColor }}">{{ ucfirst($status) }}</span>
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
                      class="icon-base bx bx-dots-vertical-rounded"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('app-ecommerce-order-details', $order->id) }}"><i class="icon-base bx bx-show me-1"></i>
                      {{ __('View Details') }}</a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
            @if(count($recentOrders) === 0)
            <tr>
              <td colspan="6" class="text-center text-muted py-4">{{ __('No recent orders found.') }}</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Total Balance -->
  <div class="col-lg-5 col-xxl-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ __('Total Balance') }}</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="totalBalance" data-bs-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalBalance">
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
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
              <h6 class="mb-0">${{ number_format($walletBalance / 1000, 2) }}k</h6>
              <small>{{ __('Wallet') }}</small>
            </div>
          </div>
          <div class="col d-flex">
            <div class="me-3">
              <span class="badge rounded-2 bg-label-secondary p-2"><i
                  class="icon-base bx bx-dollar icon-lg text-secondary"></i></span>
            </div>
            <div>
              <h6 class="mb-0">${{ number_format($paypalBalance / 1000, 2) }}k</h6>
              <small>{{ __('Paypal') }}</small>
            </div>
          </div>
        </div>
        <div id="totalBalanceChart"></div>
      </div>
      <hr class="m-0" />
      <div class="card-footer">
        <div class="d-flex justify-content-between">
          <small class="text-body">{{ __('You have done 57.6% more sales.') }}<br />{{ __('Check your new badge in your profile.') }}</small>
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
@endsection