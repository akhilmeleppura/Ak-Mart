@extends('layouts/layoutMaster')

@section('title', __('Reports & Financial Analytics') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-bar-chart-alt-2 text-primary me-2"></i> {{ __('Reports & Financial Analytics Suite') }}</h4>
        <p class="text-muted small mb-0">{{ __('Multi-period sales summaries, full P&L accounting, and deterministic sales forecasting') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('app-reports-export-csv') }}" class="btn btn-primary">
            <i class="bx bx-download me-1"></i> {{ __('Export Sales CSV') }}
        </a>
    </div>
</div>

<!-- Date Filter Form -->
<div class="card shadow-sm border mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('app-reports') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-bold mb-1">{{ __('Start Date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold mb-1">{{ __('End Date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 pt-4">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bx bx-filter-alt me-1"></i> {{ __('Update Report Range') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-label-primary p-3 h-100 border">
            <span class="text-muted small">{{ __('Total Period Sales') }}</span>
            <h3 class="fw-bold text-primary my-1">${{ number_format($totalSales, 2) }}</h3>
            <small class="text-muted"><i class="bx bx-receipt me-1"></i> {{ $totalOrders }} {{ __('Orders Total') }}</small>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-label-success p-3 h-100 border">
            <span class="text-muted small">{{ __('Estimated Gross Profit') }}</span>
            <h3 class="fw-bold text-success my-1">${{ number_format($grossProfit, 2) }}</h3>
            <small class="text-muted">{{ __('Gross Margin: ~35%') }}</small>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-label-danger p-3 h-100 border">
            <span class="text-muted small">{{ __('Recorded Store Expenses') }}</span>
            <h3 class="fw-bold text-danger my-1">${{ number_format($totalExpenses, 2) }}</h3>
            <small class="text-muted">{{ __('Operational Costs') }}</small>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-label-info p-3 h-100 border">
            <span class="text-muted small">{{ __('Net Operating Profit') }}</span>
            <h3 class="fw-bold text-info my-1">${{ number_format($netProfit, 2) }}</h3>
            <small class="text-muted">{{ __('Gross Profit - Expenses') }}</small>
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3 gap-2" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pl" role="tab">
                <i class="bx bx-calculator me-1"></i> {{ __('Profit & Loss (P&L)') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-forecast" role="tab">
                <i class="bx bx-trending-up me-1"></i> {{ __('Sales Forecasting') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-products" role="tab">
                <i class="bx bx-package me-1"></i> {{ __('Best Sellers & Margins') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-purchases" role="tab">
                <i class="bx bx-store-alt me-1"></i> {{ __('Purchases & Suppliers') }}
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        {{-- Tab 1: Profit & Loss Statement --}}
        <div class="tab-pane fade show active" id="tab-pl" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">{{ __('Period Profit & Loss Statement') }} ({{ $startDate }} {{ __('to') }} {{ $endDate }})</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped align-middle mb-0">
                        <tbody>
                            <tr class="table-light">
                                <td colspan="2"><strong class="text-heading">1. {{ __('REVENUE') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="ps-4">{{ __('Gross Sales Revenue') }} ({{ $totalOrders }} {{ __('orders') }})</td>
                                <td class="text-end fw-bold text-primary">${{ number_format($totalSales, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="2"><strong class="text-heading">2. {{ __('COST OF GOODS SOLD (COGS)') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="ps-4">{{ __('Estimated Wholesale Procurement Cost (~65%)') }}</td>
                                <td class="text-end text-danger">-${{ number_format($cogs, 2) }}</td>
                            </tr>
                            <tr class="fw-bold bg-label-success">
                                <td>{{ __('GROSS PROFIT') }}</td>
                                <td class="text-end text-success">${{ number_format($grossProfit, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="2"><strong class="text-heading">3. {{ __('OPERATIONAL EXPENSES') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="ps-4">{{ __('Store Utilities, Rent & Miscellaneous (Recorded Expenses)') }}</td>
                                <td class="text-end text-danger">-${{ number_format($totalExpenses, 2) }}</td>
                            </tr>
                            <tr class="fw-bold bg-label-primary fs-6">
                                <td>{{ __('NET OPERATING PROFIT') }}</td>
                                <td class="text-end text-primary">${{ number_format($netProfit, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 2: Sales Forecasting --}}
        <div class="tab-pane fade" id="tab-forecast" role="tabpanel">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Deterministic 7-Day & 30-Day Sales Forecast') }}</h5>
                        <small class="text-muted">{{ __('Based on moving-average historical daily trends and day-of-week seasonality factors') }}</small>
                    </div>
                    <span class="badge bg-label-info">{{ __('Estimate / Forecast Model') }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light text-center">
                                <span class="text-muted small">{{ __('Projected 7-Day Revenue') }}</span>
                                <h3 class="text-primary fw-bold my-1">${{ number_format($forecast7DayTotal, 2) }}</h3>
                                <small class="text-muted">{{ __('7-Day Sum Estimate') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light text-center">
                                <span class="text-muted small">{{ __('Projected 30-Day Revenue') }}</span>
                                <h3 class="text-success fw-bold my-1">${{ number_format($forecast30DayTotal, 2) }}</h3>
                                <small class="text-muted">{{ __('30-Day Trend Estimate') }}</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">{{ __('7-Day Day-by-Day Forecast Breakdown') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    @foreach($forecast7Day as $day)
                                        <th>{{ $day['date'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach($forecast7Day as $day)
                                        <td class="fw-bold text-primary">${{ number_format($day['predicted'], 2) }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 3: Best Sellers & Margins --}}
        <div class="tab-pane fade" id="tab-products" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">{{ __('Top-Selling Products by Volume & Revenue') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Units Sold') }}</th>
                                <th>{{ __('Gross Revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSellingItems as $item)
                                <tr>
                                    <td><strong>{{ $item->product_name }}</strong></td>
                                    <td><span class="badge bg-label-primary fs-6">{{ $item->total_qty }} {{ __('Units') }}</span></td>
                                    <td class="fw-bold text-success">${{ number_format($item->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">{{ __('No sales items recorded in period.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 4: Purchases & Suppliers --}}
        <div class="tab-pane fade" id="tab-purchases" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Supplier Procurement History') }}</h5>
                    <span class="badge bg-label-primary">{{ __('Total:') }} ${{ number_format($totalPurchasesAmount, 2) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('PO Number') }}</th>
                                <th>{{ __('Supplier') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $p)
                                <tr>
                                    <td><strong>{{ $p->po_number }}</strong></td>
                                    <td>{{ $p->supplier?->name ?? __('Supplier') }}</td>
                                    <td class="fw-bold">${{ number_format($p->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $p->status === 'received' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td><small>{{ $p->created_at->format('d M Y') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No purchase orders on record.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
