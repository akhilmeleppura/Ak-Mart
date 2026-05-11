@extends('layouts/layoutMaster')

@section('title', 'Print version - Invoice')

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/app-invoice-print.scss')
@endsection

@section('page-script')
  @vite('resources/assets/js/app-invoice-print.js')
@endsection

@section('content')
  <div class="invoice-print p-12">
    <div class="d-flex justify-content-between flex-row">
      <div class="mb-6">
        <div class="d-flex svg-illustration mb-6 gap-2 align-items-center">
          <span class="app-brand-logo demo">@include('_partials.macros')</span>
          <span class="app-brand-text demo fw-bold ms-50">{{ config('variables.templateName') }}</span>
        </div>
        <p class="mb-1">Office 149, 450 South Brand Brooklyn</p>
        <p class="mb-1">San Diego County, CA 91905, USA</p>
        <p class="mb-0">+1 (123) 456 7891, +44 (876) 543 2198</p>
      </div>
      <div>
        <h5 class="mb-6">INVOICE #{{ ltrim($order->order_number, 'ORD-') }}</h5>
        <div class="mb-1">
          <span>Date Issues:</span>
          <span>{{ $order->created_at->format('M d, Y') }}</span>
        </div>
      </div>
    </div>

    <hr class="mb-6" />

    <div class="row d-flex justify-content-between mb-6">
      <div class="col-sm-6 w-50">
        <h6>Invoice To:</h6>
        <p class="mb-1">{{ $order->customer->name ?? 'Guest Customer' }}</p>
        <p class="mb-1">{{ $order->customer->email ?? '' }}</p>
        <p class="mb-1">{!! nl2br(e($order->shipping_address)) !!}</p>
      </div>
      <div class="col-sm-6 w-50 text-end">
        <h6>Bill To:</h6>
        <p class="mb-1">{!! nl2br(e($order->billing_address)) !!}</p>
        <p class="mb-0">Payment Method: {{ ucfirst($order->payment_method) }}</p>
      </div>
    </div>

    <div class="table-responsive border border-bottom-0 border-top-0 rounded">
      <table class="table m-0">
        <thead>
          <tr>
            <th>Item</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Qty</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->product?->category?->name ?? 'Product' }}</td>
            <td>${{ number_format($item->price, 2) }}</td>
            <td>{{ $item->qty }}</td>
            <td>${{ number_format($item->price * $item->qty, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="table-responsive">
      <table class="table m-0 table-borderless">
        <tbody>
          <tr>
            <td class="align-top px-6 py-6">
              <p class="mb-1">
                <span class="me-2 fw-medium">Order Status:</span>
                <span>{{ ucfirst($order->order_status) }}</span>
              </p>
              <span>Thanks for your business</span>
            </td>
            <td class="px-0 py-12 w-px-100">
              <p class="mb-2">Subtotal:</p>
              <p class="mb-2">Discount:</p>
              <p class="mb-2 border-bottom pb-2">Tax:</p>
              <p class="mb-0 pt-2">Total:</p>
            </td>
            <td class="text-end px-0 py-6 w-px-100">
              <p class="fw-medium mb-2">${{ number_format($order->total_amount, 2) }}</p>
              <p class="fw-medium mb-2">$0.00</p>
              <p class="fw-medium mb-2 border-bottom pb-2">$0.00</p>
              <p class="fw-medium mb-0 pt-2">${{ number_format($order->total_amount, 2) }}</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <hr class="mt-0 mb-6" />
    <div class="row">
      <div class="col-12">
        <span class="fw-medium">Note:</span>
        <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance
          projects. Thank You!</span>
      </div>
    </div>
  </div>
@endsection
