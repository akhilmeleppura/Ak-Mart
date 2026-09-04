<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Tax Invoice') }} #{{ $order->order_number }} — AK-Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F8FAFC;
            color: #1E293B;
            font-family: system-ui, -apple-system, sans-serif;
            padding: 30px 15px;
        }
        .invoice-card {
            background: #FFFFFF;
            max-width: 820px;
            margin: 0 auto;
            border-radius: 20px;
            padding: 40px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }
        .invoice-header-brand {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .invoice-table thead th {
            background-color: #F8FAFC;
            color: #64748B;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #E2E8F0;
            padding: 12px 14px;
        }
        .invoice-table tbody td {
            padding: 14px;
            border-bottom: 1px solid #F1F5F9;
            font-size: 14px;
        }
        .qr-placeholder {
            width: 90px;
            height: 90px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            padding: 6px;
        }
        @media print {
            body {
                background-color: #FFFFFF;
                padding: 0;
            }
            .invoice-card {
                border: none;
                box-shadow: none;
                padding: 10px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Top Print & Return Toolbar (Hidden when printing) -->
    <div class="d-flex justify-content-between align-items-center mb-4 max-w-820 mx-auto no-print" style="max-width: 820px;">
        <a href="{{ route('storefront.track', ['order_number' => $order->order_number]) }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 small fw-semibold">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Order Tracking') }}
        </a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4 py-1.5 fw-bold shadow-xs" onclick="window.print()">
                <i class="bx bx-printer me-1"></i> {{ __('Print / Save as PDF') }}
            </button>
        </div>
    </div>

    <!-- Official Invoice Document -->
    <div class="invoice-card">
        <!-- Header -->
        <div class="row align-items-start mb-4 pb-4 border-bottom g-3">
            <div class="col-sm-7">
                <div class="invoice-header-brand mb-1">AK-MART</div>
                <div class="small text-muted mb-2">{{ __('Enterprise Multi-Branch Superstore & Fresh Commerce') }}</div>
                <div class="small text-secondary">
                    <strong>GSTIN / Tax ID:</strong> 32AAACA0000A1Z5<br>
                    <strong>Support Email:</strong> support@akmart.com<br>
                    <strong>Hotline:</strong> +1 (800) 555-AKMART
                </div>
            </div>
            <div class="col-sm-5 text-sm-end">
                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-1.5 rounded-pill fw-bold text-uppercase mb-2">
                    {{ __('ORIGINAL TAX INVOICE') }}
                </span>
                <h5 class="fw-bold text-dark mb-1 font-monospace">#{{ $order->order_number }}</h5>
                <div class="small text-muted"><strong>{{ __('Date:') }}</strong> {{ $order->created_at->format('M d, Y h:i A') }}</div>
                <div class="small text-muted"><strong>{{ __('Payment Mode:') }}</strong> <span class="text-uppercase fw-semibold">{{ $order->payment_method ?? 'Online / Prepaid' }}</span></div>
                <div class="small text-muted"><strong>{{ __('Status:') }}</strong> <span class="badge bg-success rounded-pill px-2 py-0.5">{{ strtoupper($order->payment_status ?? 'PAID') }}</span></div>
            </div>
        </div>

        <!-- Billing & Shipping Details -->
        <div class="row mb-4 pb-2 g-3">
            <div class="col-sm-6">
                <span class="text-uppercase small fw-bold text-muted letter-spacing-1 d-block mb-1">{{ __('Billed / Shipped To:') }}</span>
                <h6 class="fw-bold text-dark mb-1">{{ $order->customer?->name ?? ($order->shipping_name ?? 'Valued Customer') }}</h6>
                <div class="small text-muted">
                    {{ $order->shipping_address ?? '100 Market Street, Tech Hub District' }}<br>
                    {{ $order->shipping_city ?? 'Metropolis' }}, {{ $order->shipping_state ?? 'CA' }} - {{ $order->shipping_pincode ?? '560001' }}<br>
                    <strong>Email:</strong> {{ $order->customer?->email ?? ($order->shipping_email ?? 'customer@akmart.com') }}<br>
                    <strong>Phone:</strong> {{ $order->shipping_phone ?? '+1 (555) 019-2834' }}
                </div>
            </div>
            <div class="col-sm-6 text-sm-end d-flex flex-column align-items-sm-end justify-content-center">
                <div class="qr-placeholder mb-1">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(route('storefront.track', ['order_number' => $order->order_number])) }}" alt="QR Verification" width="76" height="76">
                </div>
                <small class="text-muted font-monospace" style="font-size: 11px;">Scan to Verify Authenticity</small>
            </div>
        </div>

        <!-- Itemized Products Table -->
        <div class="table-responsive mb-4">
            <table class="table invoice-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('Item Description') }}</th>
                        <th class="text-center" style="width: 90px;">{{ __('Qty') }}</th>
                        <th class="text-end" style="width: 120px;">{{ __('Unit Price') }}</th>
                        <th class="text-end" style="width: 130px;">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $idx => $item)
                        <tr>
                            <td class="text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->product_name ?? $item->product?->name }}</div>
                                @if(!empty($item->sku))
                                    <small class="text-muted font-monospace">SKU: {{ $item->sku }}</small>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">{{ $item->qty }}</td>
                            <td class="text-end font-monospace">${{ number_format($item->price, 2) }}</td>
                            <td class="text-end fw-bold font-monospace">${{ number_format($item->total ?? ($item->price * $item->qty), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Calculation Summary -->
        <div class="row justify-content-end mb-4">
            <div class="col-sm-6 col-md-5">
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">{{ __('Subtotal:') }}</span>
                    <strong class="font-monospace">${{ number_format($order->items->sum(fn($i) => $i->total ?? ($i->price * $i->qty)), 2) }}</strong>
                </div>

                @if(!empty($order->discount_amount) && $order->discount_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom small text-success">
                        <span><i class="bx bxs-purchase-tag me-1"></i>{{ __('Coupon Discount:') }}</span>
                        <strong class="font-monospace">-${{ number_format($order->discount_amount, 2) }}</strong>
                    </div>
                @endif

                @if(!empty($order->store_credit_amount) && $order->store_credit_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom small text-primary">
                        <span><i class="bx bx-wallet me-1"></i>{{ __('Store Credit Balance:') }}</span>
                        <strong class="font-monospace">-${{ number_format($order->store_credit_amount, 2) }}</strong>
                    </div>
                @endif

                @if(!empty($order->tax_amount) && $order->tax_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom small text-muted">
                        <span>{{ __('Estimated VAT / Sales Tax:') }}</span>
                        <strong class="font-monospace">${{ number_format($order->tax_amount, 2) }}</strong>
                    </div>
                @endif

                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">{{ __('Express Delivery:') }}</span>
                    <strong class="text-success">{{ __('FREE') }}</strong>
                </div>

                <div class="d-flex justify-content-between py-2 mt-1 fs-5 fw-bold text-dark">
                    <span>{{ __('Total Paid:') }}</span>
                    <span class="text-primary font-monospace">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Terms & Authorized Signatory Footer -->
        <div class="pt-4 border-top text-muted small mt-4">
            <div class="row g-3 align-items-center">
                <div class="col-sm-8">
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 12px;">{{ __('Terms & Conditions:') }}</h6>
                    <p class="mb-0" style="font-size: 11.5px; line-height: 1.5;">
                        {{ __('Goods once sold are covered under AK-Mart\'s 7-Day Hassle-Free Return Policy. Computer-generated invoice requires no physical signature.') }}
                    </p>
                </div>
                <div class="col-sm-4 text-sm-end">
                    <div class="border-top pt-2 d-inline-block text-center" style="min-width: 140px;">
                        <span class="fw-bold text-dark d-block" style="font-size: 11.5px;">AK-Mart Authorized</span>
                        <small class="text-muted font-monospace" style="font-size: 10px;">Automated Tax Clearance</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
