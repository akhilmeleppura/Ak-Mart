@extends('layouts/layoutMaster')

@section('title', __('Invoice') . ' #' . $invoice->invoice_number . ' — AK-Mart')

@section('page-style')
<style>
  @media print {
    .layout-navbar, .layout-menu, .footer, .btn-print-hide {
      display: none !important;
    }
    .content-wrapper {
      padding: 0 !important;
      margin: 0 !important;
    }
    .card {
      border: none !important;
      box-shadow: none !important;
    }
  }
</style>
@endsection

@section('content')
<div class="row invoice-preview justify-content-center">
  <!-- Invoice Actions (Top) -->
  <div class="col-xl-9 col-md-10 col-12 mb-4 btn-print-hide">
    <div class="d-flex justify-content-between align-items-center">
      <a href="{{ route('app-saas-billing') }}" class="btn btn-label-secondary">
        <i class="bx bx-arrow-back me-1"></i>{{ __('Back to Billing') }}
      </a>
      <button onclick="window.print()" class="btn btn-primary shadow-sm">
        <i class="bx bx-printer me-1"></i>{{ __('Print / Save as PDF') }}
      </button>
    </div>
  </div>

  <!-- Invoice Content Card -->
  <div class="col-xl-9 col-md-10 col-12">
    <div class="card p-sm-6 p-4 shadow-sm border-0">
      <div class="card-body">
        <!-- Invoice Header -->
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column pb-6 border-bottom gap-4">
          <div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="app-brand-logo demo">
                <span class="badge bg-primary fs-3 p-2 rounded"><i class="bx bx-shopping-bag"></i></span>
              </span>
              <span class="app-brand-text demo h3 mb-0 fw-bold">AK-Mart</span>
            </div>
            <p class="mb-1 text-muted">{{ __('Enterprise E-Commerce & Mini-Mart SaaS Platform') }}</p>
            <p class="mb-1 text-muted">{{ __('742 Broadway Ave, Manhattan, New York, NY 10003') }}</p>
            <p class="mb-0 text-muted">{{ __('support@ak-mart.com') }}</p>
          </div>
          <div>
            <h4 class="fw-bold mb-2">{{ __('INVOICE') }} <span class="text-primary font-monospace">#{{ $invoice->invoice_number }}</span></h4>
            <div class="mb-1">
              <span class="text-muted">{{ __('Date Issued:') }}</span>
              <span class="fw-semibold text-heading">{{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : now()->format('M d, Y') }}</span>
            </div>
            <div class="mb-1">
              <span class="text-muted">{{ __('Billing Period:') }}</span>
              <span class="fw-semibold text-heading">
                {{ $invoice->billing_period_start ? $invoice->billing_period_start->format('M d, Y') : '—' }} to {{ $invoice->billing_period_end ? $invoice->billing_period_end->format('M d, Y') : '—' }}
              </span>
            </div>
            <div>
              <span class="text-muted">{{ __('Payment Status:') }}</span>
              @if($invoice->status === 'paid')
                <span class="badge bg-success">{{ __('PAID') }}</span>
              @elseif($invoice->status === 'pending')
                <span class="badge bg-warning">{{ __('PENDING') }}</span>
              @else
                <span class="badge bg-secondary">{{ strtoupper($invoice->status) }}</span>
              @endif
            </div>
          </div>
        </div>

        <!-- Billed To -->
        <div class="row pt-6 pb-4">
          <div class="col-sm-6 mb-4">
            <h6 class="text-uppercase text-body-secondary small fw-bold mb-2">{{ __('Billed To:') }}</h6>
            <h5 class="mb-1 fw-bold text-heading">{{ $invoice->branch ? $invoice->branch->name : __('Store Merchant') }}</h5>
            <p class="mb-1 text-muted">{{ $invoice->branch ? $invoice->branch->address : __('Standard Branch Address') }}</p>
            <p class="mb-0 text-muted">{{ __('Account Owner:') }} {{ $user->name }} ({{ $user->email }})</p>
          </div>
          <div class="col-sm-6 mb-4 text-sm-end">
            <h6 class="text-uppercase text-body-secondary small fw-bold mb-2">{{ __('Payment Method:') }}</h6>
            <p class="mb-1 fw-semibold text-heading"><i class="bx bx-credit-card me-1"></i>{{ $invoice->payment_method ?: 'Credit Card' }}</p>
            <p class="mb-0 text-muted">{{ __('Paid at:') }} {{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y H:i') : ($invoice->created_at ? $invoice->created_at->format('M d, Y H:i') : now()->format('M d, Y H:i')) }}</p>
          </div>
        </div>

        <!-- Table of Items -->
        <div class="table-responsive border rounded mb-6">
          <table class="table table-hover m-0">
            <thead class="table-light">
              <tr>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Cycle') }}</th>
                <th>{{ __('Unit Price') }}</th>
                <th class="text-end">{{ __('Total Amount') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <h6 class="mb-0 fw-semibold text-heading">{{ $invoice->plan_name ?: 'SaaS Subscription Plan' }}</h6>
                  <small class="text-muted">{{ __('Includes all multi-branch, POS, and advanced commerce tools for this store branch.') }}</small>
                </td>
                <td>30 {{ __('Days') }}</td>
                <td>${{ number_format($invoice->amount, 2) }}</td>
                <td class="text-end fw-bold">${{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals & Notes -->
        <div class="row">
          <div class="col-md-6 mb-4">
            <h6 class="fw-bold mb-1">{{ __('Terms & Conditions') }}</h6>
            <p class="text-muted small mb-0">
              {{ __('Thank you for choosing AK-Mart. All subscriptions are billed in advance per billing period. Invoices are generated automatically.') }}
            </p>
          </div>
          <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-between justify-content-md-end gap-6 mb-2">
              <span class="text-muted">{{ __('Subtotal:') }}</span>
              <span class="fw-semibold text-heading">${{ number_format($invoice->amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between justify-content-md-end gap-6 mb-2">
              <span class="text-muted">{{ __('Tax / VAT (0%):') }}</span>
              <span class="fw-semibold text-heading">$0.00</span>
            </div>
            <div class="d-flex justify-content-between justify-content-md-end gap-6 border-top pt-2">
              <h5 class="fw-bold mb-0">{{ __('Total Paid:') }}</h5>
              <h5 class="fw-bold text-primary mb-0">${{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
