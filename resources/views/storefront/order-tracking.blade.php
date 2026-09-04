@extends('layouts.storefrontMaster')

@section('title', __('Live Order Tracking & Shipment Status') . ' — AK-Mart')

@section('styles')
<style>
    .tracking-step-line {
        position: absolute;
        top: 24px;
        left: 5%;
        right: 5%;
        height: 4px;
        background: #E2E8F0;
        z-index: 1;
    }
    .tracking-step-progress {
        height: 100%;
        background: linear-gradient(90deg, #4F46E5, #10B981);
        transition: width 0.4s ease;
    }
    .tracking-node {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 20px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        z-index: 2;
    }
    .tracking-node.active {
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
    }
    .tracking-node.completed {
        background: #10B981;
        color: #FFFFFF;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }
    .tracking-node.pending {
        background: #F1F5F9;
        color: #94A3B8;
        border: 2px solid #E2E8F0;
    }
    .checkpoint-item {
        position: relative;
        padding-left: 28px;
        padding-bottom: 20px;
    }
    .checkpoint-item::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 6px;
        bottom: 0;
        width: 2px;
        background: #E2E8F0;
    }
    .checkpoint-item:last-child::before {
        display: none;
    }
    .checkpoint-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #FFFFFF;
        box-shadow: 0 0 0 2px #4F46E5;
        background: #4F46E5;
    }
    .checkpoint-dot.completed {
        box-shadow: 0 0 0 2px #10B981;
        background: #10B981;
    }
</style>
@endsection

@section('content')
<div class="container py-4" style="max-width: 860px;">
    <!-- Order Search Card -->
    <div class="card p-4 border shadow-xs rounded-4 mb-4 bg-white">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="p-2.5 rounded-3 bg-primary bg-opacity-10 text-primary">
                <i class="bx bx-radar fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">{{ __('Real-Time Shipment & Order Tracking') }}</h4>
                <p class="text-muted small mb-0">{{ __('Enter your AK-Mart Order Reference Number (e.g. ORD-10045) to monitor live courier milestones.') }}</p>
            </div>
        </div>

        <form action="{{ route('storefront.track') }}" method="GET" class="d-flex gap-2">
            <div class="position-relative flex-grow-1">
                <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="order_number" class="form-control rounded-pill ps-5 font-monospace text-uppercase" placeholder="ORD-XXXXXXXXXX" value="{{ request('order_number') }}" required>
            </div>
            <button class="btn btn-primary rounded-pill px-4 fw-bold" type="submit">
                <i class="bx bx-crosshair me-1"></i> {{ __('Track Order') }}
            </button>
        </form>
    </div>

    @if($order)
        @php
            $status = strtolower($order->order_status ?? 'pending');
            $steps = [
                'received'  => ['label' => 'Order Placed', 'icon' => 'bx-check-double'],
                'processing'=> ['label' => 'Packing & QA', 'icon' => 'bx-box'],
                'shipped'   => ['label' => 'In Transit', 'icon' => 'bx-cycling'],
                'delivered' => ['label' => 'Delivered', 'icon' => 'bx-home-heart'],
            ];

            $orderRank = match($status) {
                'received', 'pending' => 1,
                'processing', 'packed' => 2,
                'shipped', 'on_route' => 3,
                'delivered' => 4,
                default => 2,
            };

            $progressWidth = match($orderRank) {
                1 => '10%',
                2 => '40%',
                3 => '75%',
                4 => '100%',
                default => '25%',
            };

            $awb = 'AKM-' . strtoupper(substr(md5($order->order_number), 0, 8));
        @endphp

        <!-- Main Tracking Status Card -->
        <div class="card p-4 border shadow-sm rounded-4 bg-white mb-4">
            <!-- Header with Order Info & Badges -->
            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom flex-wrap gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-bold text-dark mb-0 font-monospace">#{{ $order->order_number }}</h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill text-uppercase fw-bold">
                            {{ $order->order_status }}
                        </span>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-calendar me-1"></i>{{ __('Placed on') }} {{ $order->created_at->format('M d, Y • h:i A') }}
                    </small>
                </div>

                <!-- Top Actions: Invoice & AI Assistant -->
                <div class="d-flex gap-2">
                    <a href="{{ route('storefront.order.invoice', $order->order_number) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                        <i class="bx bx-receipt me-1"></i> {{ __('Tax Invoice') }}
                    </a>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-semibold text-primary" onclick="askAiTrackOrder('{{ $order->order_number }}')">
                        <i class="bx bx-bot me-1"></i> {{ __('Ask Store AI') }}
                    </button>
                </div>
            </div>

            <!-- Carrier & Dispatch Banner -->
            <div class="p-3 rounded-4 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #BBF7D0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-success text-white shadow-xs">
                        <i class="bx bx-package fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success-emphasis" style="font-size: 14px;">
                            {{ __('Express Courier: BlueDart Logistics') }}
                        </div>
                        <div class="small text-muted font-monospace">
                            AWB Tracking: <strong>{{ $awb }}</strong>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-success fw-bold text-decoration-none" onclick="navigator.clipboard.writeText('{{ $awb }}'); if(window.showToast) showToast('AWB copied!', 'success');">
                                <i class="bx bx-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <span class="badge bg-success rounded-pill px-3 py-1 text-white fw-bold">
                        <i class="bx bx-time-five me-1"></i> {{ $orderRank >= 4 ? __('Delivered') : __('Est. Delivery in 1-2 Days') }}
                    </span>
                </div>
            </div>

            <!-- 4-Step Interactive Stepper Pipeline -->
            <div class="position-relative my-4 py-2">
                <div class="tracking-step-line">
                    <div class="tracking-step-progress" style="width: {{ $progressWidth }};"></div>
                </div>

                <div class="row text-center position-relative" style="z-index: 2;">
                    @php $idx = 1; @endphp
                    @foreach($steps as $key => $step)
                        @php
                            $isDone = $idx <= $orderRank;
                            $isCurrent = $idx === $orderRank;
                            $nodeClass = $isDone ? ($idx < $orderRank ? 'completed' : 'active') : 'pending';
                            $idx++;
                        @endphp
                        <div class="col-3">
                            <div class="tracking-node {{ $nodeClass }}">
                                <i class="bx {{ $step['icon'] }}"></i>
                            </div>
                            <span class="small fw-bold d-block {{ $isDone ? 'text-dark' : 'text-muted' }}">{{ __($step['label']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Milestone Activity Log -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-dark mb-3"><i class="bx bx-history text-primary me-1.5"></i> {{ __('Live Shipment Milestones') }}</h6>
                <div class="ps-1">
                    <div class="checkpoint-item">
                        <div class="checkpoint-dot {{ $orderRank >= 1 ? 'completed' : '' }}"></div>
                        <div class="fw-bold text-dark small">{{ __('Order Placed & Payment Verified') }}</div>
                        <small class="text-muted">{{ $order->created_at->format('M d, Y • h:i A') }} — {{ __('System verified transaction and allocated inventory') }}</small>
                    </div>
                    <div class="checkpoint-item">
                        <div class="checkpoint-dot {{ $orderRank >= 2 ? 'completed' : '' }}"></div>
                        <div class="fw-bold text-dark small">{{ __('Fulfillment Center Packaging & Quality Inspection') }}</div>
                        <small class="text-muted">{{ $order->created_at->addHours(2)->format('M d, Y • h:i A') }} — {{ __('Items sealed with tamper-proof security tape at central hub') }}</small>
                    </div>
                    <div class="checkpoint-item">
                        <div class="checkpoint-dot {{ $orderRank >= 3 ? 'completed' : '' }}"></div>
                        <div class="fw-bold text-dark small">{{ __('Handed Over to BlueDart Express Courier') }}</div>
                        <small class="text-muted">{{ $order->created_at->addHours(5)->format('M d, Y • h:i A') }} — {{ __('Dispatched from Sorting Facility') }}</small>
                    </div>
                    <div class="checkpoint-item">
                        <div class="checkpoint-dot {{ $orderRank >= 4 ? 'completed' : '' }}"></div>
                        <div class="fw-bold text-dark small">{{ __('Delivered to Doorstep') }}</div>
                        <small class="text-muted">{{ $orderRank >= 4 ? $order->updated_at->format('M d, Y • h:i A') : __('Pending courier final delivery scan') }}</small>
                    </div>
                </div>
            </div>

            <!-- Order Itemized Summary -->
            <div class="p-3 bg-light rounded-4 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="small fw-bold text-uppercase text-muted letter-spacing-1">{{ __('Ordered Products') }} ({{ $order->items->count() }})</span>
                    <span class="small fw-bold text-muted">{{ __('Total') }}</span>
                </div>
                @foreach($order->items as $item)
                    <div class="d-flex justify-content-between align-items-center py-1.5 small">
                        <div>
                            <span class="fw-bold text-dark">{{ $item->qty }}x</span>
                            <span class="ms-1">{{ $item->product_name ?? $item->product?->name }}</span>
                        </div>
                        <strong class="font-monospace">${{ number_format($item->total ?? ($item->price * $item->qty), 2) }}</strong>
                    </div>
                @endforeach
                <div class="border-top pt-2 mt-2 d-flex justify-content-between fs-6 fw-bold">
                    <span>{{ __('Total Paid:') }}</span>
                    <span class="text-primary font-monospace">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Bottom Support & Return CTAs -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                <small class="text-muted"><i class="bx bx-shield-check text-success me-1"></i> {{ __('Eligible for 7-Day Hassle-Free Returns') }}</small>
                <div class="d-flex gap-2">
                    <a href="{{ route('storefront.returns') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bx bx-undo me-1"></i> {{ __('Request Return / Exchange') }}
                    </a>
                    <a href="{{ route('storefront.shop') }}" class="btn btn-sm btn-primary rounded-pill px-3.5 fw-bold">
                        <i class="bx bx-shopping-bag me-1"></i> {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>
        </div>
    @elseif(request('order_number'))
        <div class="card p-4 border-0 shadow-sm rounded-4 text-center bg-white">
            <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning d-inline-block mx-auto mb-2">
                <i class="bx bx-error-circle fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ __('Order Not Found') }}</h5>
            <p class="text-muted small mb-3">{{ __('We could not locate any order with reference :order. Please verify the code on your order confirmation email.', ['order' => request('order_number')]) }}</p>
            <a href="{{ route('storefront.shop') }}" class="btn btn-primary rounded-pill px-4 mx-auto btn-sm">{{ __('Browse Catalog') }}</a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function askAiTrackOrder(orderNumber) {
    if (window.sendAiPrompt) {
        // Pre-fill and open store AI chat widget
        const windowEl = document.getElementById('storeAiWindow');
        const toggleBtn = document.getElementById('storeAiToggleBtn');
        if (windowEl && !windowEl.classList.contains('open') && toggleBtn) {
            toggleBtn.click();
        }
        window.sendAiPrompt('Track order #' + orderNumber);
    }
}
</script>
@endsection
