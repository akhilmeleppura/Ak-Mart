@extends('layouts/layoutMaster')

@section('title', __('Platform Analytics') . ' — AK-Mart')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-6">
  <div>
    <h4 class="mb-1">{{ __('Platform Analytics') }}</h4>
    <p class="mb-0 text-muted">{{ __('Real-time overview of your SaaS marketplace performance.') }}</p>
  </div>
  <span class="badge bg-label-success fs-6 px-3 py-2">
    <i class="bx bx-radio-circle-marked bx-flashing me-1"></i> {{ __('Live Data') }}
  </span>
</div>

{{-- ── Row 1: Key Metrics ── --}}
<div class="row mb-6">

  {{-- GMV --}}
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <div class="badge rounded-pill bg-label-primary p-2"><i class="bx bx-store bx-sm"></i></div>
          <span class="badge bg-label-{{ $gmvGrowth >= 0 ? 'success' : 'danger' }} rounded-pill">
            {{ $gmvGrowth >= 0 ? '+' : '' }}{{ $gmvGrowth }}% MoM
          </span>
        </div>
        <h5 class="mb-1">{{ __('Total GMV') }}</h5>
        <h3 class="text-primary mb-0">${{ number_format($totalGMV, 0) }}</h3>
        <small class="text-muted">{{ __('This month:') }} ${{ number_format($thisMonthGMV, 0) }}</small>
      </div>
    </div>
  </div>

  {{-- MRR --}}
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <div class="badge rounded-pill bg-label-success p-2"><i class="bx bx-trending-up bx-sm"></i></div>
          <span class="badge bg-label-info rounded-pill">ARR ${{ number_format($arr, 0) }}</span>
        </div>
        <h5 class="mb-1">{{ __('MRR') }}</h5>
        <h3 class="text-success mb-0">${{ number_format($mrr, 0) }}</h3>
        <small class="text-muted">{{ __('Monthly Recurring Revenue') }}</small>
      </div>
    </div>
  </div>

  {{-- Platform Fees --}}
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <div class="badge rounded-pill bg-label-warning p-2"><i class="bx bx-percentage bx-sm"></i></div>
          <span class="badge bg-label-warning rounded-pill">{{ __('This month:') }} ${{ number_format($feesThisMonth, 0) }}</span>
        </div>
        <h5 class="mb-1">{{ __('Platform Fees') }}</h5>
        <h3 class="text-warning mb-0">${{ number_format($totalPlatformFees, 0) }}</h3>
        <small class="text-muted">{{ __('Total commission collected') }}</small>
      </div>
    </div>
  </div>

  {{-- Churn Rate --}}
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <div class="badge rounded-pill bg-label-{{ $churnRate > 5 ? 'danger' : 'success' }} p-2">
            <i class="bx bx-user-x bx-sm"></i>
          </div>
          <span class="badge bg-label-{{ $churnRate > 5 ? 'danger' : 'success' }} rounded-pill">
            {{ $churnRate > 5 ? __('High') : __('Healthy') }}
          </span>
        </div>
        <h5 class="mb-1">{{ __('Churn Rate') }}</h5>
        <h3 class="text-{{ $churnRate > 5 ? 'danger' : 'success' }} mb-0">{{ $churnRate }}%</h3>
        <small class="text-muted">{{ __('Canceled this month') }}</small>
      </div>
    </div>
  </div>
</div>

{{-- ── Row 2: Store & User Stats ── --}}
<div class="row mb-6">
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-primary p-2 me-3"><i class="bx bx-buildings bx-md"></i></div>
        <div>
          <small class="text-muted d-block">{{ __('Total Stores') }}</small>
          <h3 class="mb-0">{{ $totalStores }}</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-success p-2 me-3"><i class="bx bx-check-circle bx-md"></i></div>
        <div>
          <small class="text-muted d-block">{{ __('Active Stores') }}</small>
          <h3 class="mb-0 text-success">{{ $activeStores }}</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-info p-2 me-3"><i class="bx bx-time bx-md"></i></div>
        <div>
          <small class="text-muted d-block">{{ __('On Trial') }}</small>
          <h3 class="mb-0 text-info">{{ $trialStores }}</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-secondary p-2 me-3"><i class="bx bx-group bx-md"></i></div>
        <div>
          <small class="text-muted d-block">{{ __('Total Users') }}</small>
          <h3 class="mb-0">{{ $totalUsers }} <small class="text-success fs-6">+{{ $newUsersThisMonth }} {{ __('this month') }}</small></h3>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Row 3: Revenue Chart + Plan Distribution ── --}}
<div class="row mb-6">
  {{-- GMV + Fees Chart --}}
  <div class="col-lg-8 mb-4">
    <div class="card h-100">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Revenue Overview (Last 6 Months)') }}</h5>
      </div>
      <div class="card-body">
        <div id="revenueChart"></div>
      </div>
    </div>
  </div>

  {{-- Plan Distribution --}}
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Subscription Plan Mix') }}</h5>
      </div>
      <div class="card-body">
        <div id="planChart"></div>
        <div class="mt-4">
          @foreach($planDistribution as $plan)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span>{{ $plan['name'] }}</span>
            <span class="badge bg-label-primary">{{ $plan['count'] }} {{ __('stores') }}</span>
          </div>
          @endforeach
          @if($planDistribution->isEmpty())
          <p class="text-center text-muted">{{ __('No active subscriptions yet.') }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Row 4: Store Growth + Recent Subscriptions ── --}}
<div class="row mb-6">
  {{-- Store Growth Chart --}}
  <div class="col-lg-5 mb-4">
    <div class="card h-100">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Store Growth (Last 6 Months)') }}</h5>
      </div>
      <div class="card-body">
        <div id="storeGrowthChart"></div>
      </div>
    </div>
  </div>

  {{-- Recent Subscriptions --}}
  <div class="col-lg-7 mb-4">
    <div class="card h-100">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Recent Subscriptions') }}</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>{{ __('Store') }}</th>
                <th>{{ __('Plan') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Period End') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentSubscriptions as $sub)
              <tr>
                <td><strong>{{ $sub->branch->name ?? 'Store #'.$sub->branch_id }}</strong></td>
                <td>{{ $sub->plan->name ?? 'N/A' }}</td>
                <td>
                  @php $colors = ['active'=>'success','trialing'=>'info','past_due'=>'warning','canceled'=>'danger','unpaid'=>'danger']; @endphp
                  <span class="badge bg-label-{{ $colors[$sub->status] ?? 'secondary' }}">{{ ucfirst($sub->status) }}</span>
                </td>
                <td>{{ $sub->current_period_end ? $sub->current_period_end->format('M d, Y') : ($sub->trial_ends_at ? $sub->trial_ends_at->format('M d, Y') : '—') }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-5">{{ __('No subscriptions yet.') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#cdd9e5' : '#566a7f';
  const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

  // ── Revenue Chart ──────────────────────────────────────────────
  const revenueLabels = @json($revenueLabels);
  const gmvData  = @json($revenueChart['gmv'] ?? []);
  const feesData = @json($revenueChart['fees'] ?? []);

  new ApexCharts(document.querySelector('#revenueChart'), {
    chart: { type: 'area', height: 260, toolbar: { show: false }, sparkline: { enabled: false } },
    series: [
      { name: @json(__('Gross GMV ($)')), data: gmvData },
      { name: @json(__('Platform Fees ($)')), data: feesData }
    ],
    colors: ['#7367f0', '#28c76f'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: { categories: revenueLabels, labels: { style: { colors: textColor } } },
    yaxis: { labels: { style: { colors: textColor }, formatter: v => '$'+v.toLocaleString() } },
    grid: { borderColor: gridColor },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => '$'+v.toLocaleString() } },
    legend: { labels: { colors: textColor } }
  }).render();

  // ── Plan Distribution Donut ────────────────────────────────────
  const planData = @json($planDistribution->pluck('count'));
  const planNames = @json($planDistribution->pluck('name'));

  if (planData.length > 0) {
    new ApexCharts(document.querySelector('#planChart'), {
      chart: { type: 'donut', height: 220 },
      series: planData,
      labels: planNames,
      colors: ['#7367f0', '#28c76f', '#ff9f43'],
      legend: { position: 'bottom', labels: { colors: textColor } },
      dataLabels: { enabled: true },
      tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();
  } else {
    document.querySelector('#planChart').innerHTML = '<p class="text-center text-muted pt-4">' + @json(__('No active subscriptions.')) + '</p>';
  }

  // ── Store Growth Bar ───────────────────────────────────────────
  const storeGrowthData = @json($storeGrowth);
  const monthLabels = @json($revenueLabels);

  new ApexCharts(document.querySelector('#storeGrowthChart'), {
    chart: { type: 'bar', height: 250, toolbar: { show: false } },
    series: [{ name: @json(__('New Stores')), data: storeGrowthData }],
    colors: ['#ff9f43'],
    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
    dataLabels: { enabled: false },
    xaxis: { categories: monthLabels, labels: { style: { colors: textColor } } },
    yaxis: { labels: { style: { colors: textColor } } },
    grid: { borderColor: gridColor },
    tooltip: { theme: isDark ? 'dark' : 'light' },
    legend: { labels: { colors: textColor } }
  }).render();
});
</script>
@endsection
