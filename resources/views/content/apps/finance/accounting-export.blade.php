@extends('layouts/layoutMaster')

@section('title', __('Accounting & Financial Ledger Exports') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-export text-primary me-2"></i> {{ __('Financial Data & Accounting Export Center') }}</h4>
        <p class="text-muted small mb-0">{{ __('Export auditable sales records, GST filing ledgers, and operational expenses for accounting ERP integration') }}</p>
    </div>
</div>

<div class="row g-4">
    <!-- Sales Ledger Export -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 p-4 border shadow-sm rounded-3 d-flex flex-column justify-content-between">
            <div>
                <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center mb-3">
                    <i class="bx bx-receipt fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ __('Sales & Revenue Ledger') }}</h5>
                <p class="text-muted small mb-3">{{ __('Download itemized order transactions with taxable breakdown and payment methods.') }}</p>
            </div>
            <a href="{{ route('app-accounting-export-sales') }}" class="btn btn-primary rounded-pill w-100">
                <i class="bx bx-download me-1"></i> {{ __('Export Sales (CSV)') }}
            </a>
        </div>
    </div>

    <!-- GST Tax Ledger Export -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 p-4 border shadow-sm rounded-3 d-flex flex-column justify-content-between">
            <div>
                <div class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center mb-3">
                    <i class="bx bx-calculator fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ __('GST & Tax Compliance Ledger') }}</h5>
                <p class="text-muted small mb-3">{{ __('Generate GSTR-1 compliant tax reports with CGST, SGST, and IGST calculated breakdowns.') }}</p>
            </div>
            <a href="{{ route('app-accounting-export-gst') }}" class="btn btn-success rounded-pill w-100">
                <i class="bx bx-download me-1"></i> {{ __('Export GST Ledger (CSV)') }}
            </a>
        </div>
    </div>

    <!-- Operating Expenses Ledger Export -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 p-4 border shadow-sm rounded-3 d-flex flex-column justify-content-between">
            <div>
                <div class="avatar avatar-md bg-label-danger rounded-3 d-flex align-items-center justify-content-center mb-3">
                    <i class="bx bx-wallet-alt fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ __('Operating Expenses Ledger') }}</h5>
                <p class="text-muted small mb-3">{{ __('Extract branch utilities, rents, staff expenses, and logistical operating costs.') }}</p>
            </div>
            <a href="{{ route('app-accounting-export-expenses') }}" class="btn btn-danger rounded-pill w-100">
                <i class="bx bx-download me-1"></i> {{ __('Export Expenses (CSV)') }}
            </a>
        </div>
    </div>
</div>
@endsection
