@extends('layouts.storefrontMaster')

@section('title', 'Delivery Driver Portal | AK-Mart Logistics')

@section('content')
<div class="container py-4">
    <!-- Driver Header & Status Bar -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-7">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center p-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                    <i class="ri-truck-line"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Driver Delivery Portal</h3>
                    <p class="text-muted mb-0">
                        Welcome back, <strong class="text-dark">{{ $driver->name ?? 'Courier Partner' }}</strong> 
                        <span class="badge bg-success ms-2"><i class="ri-broadcast-fill me-1"></i> Online & Active</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-5 text-md-end">
            <a href="{{ route('storefront.home') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="ri-store-2-line me-1"></i> Storefront
            </a>
            <button onclick="window.location.reload();" class="btn btn-primary btn-sm">
                <i class="ri-refresh-line me-1"></i> Refresh Tasks
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fs-6 opacity-75">Active Tasks</span>
                        <i class="ri-route-line fs-4 opacity-75"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-white">{{ $stats['active_count'] }}</h2>
                    <small class="opacity-75">Assigned & In Transit</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fs-6 opacity-75">Delivered Today</span>
                        <i class="ri-checkbox-circle-line fs-4 opacity-75"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-white">{{ $stats['delivered_today'] }}</h2>
                    <small class="opacity-75">Orders completed</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted fs-6">COD To Collect</span>
                        <i class="ri-money-dollar-circle-line fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">${{ number_format($stats['cod_to_collect'], 2) }}</h3>
                    <small class="text-muted">Cash on active orders</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted fs-6">Total Fulfilled</span>
                        <i class="ri-medal-line fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_delivered'] }}</h3>
                    <small class="text-muted">All-time deliveries</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workflow Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills nav-fill gap-2" id="driverTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-semibold" id="active-tab" data-bs-toggle="pill" data-bs-target="#active-orders" type="button" role="tab">
                        <i class="ri-e-bike-2-line me-1"></i> My Active Route <span class="badge bg-primary rounded-pill ms-1">{{ $activeOrders->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-semibold" id="available-tab" data-bs-toggle="pill" data-bs-target="#available-orders" type="button" role="tab">
                        <i class="ri-inbox-unarchive-line me-1"></i> Available Orders <span class="badge bg-secondary rounded-pill ms-1">{{ $availableOrders->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-semibold" id="history-tab" data-bs-toggle="pill" data-bs-target="#completed-orders" type="button" role="tab">
                        <i class="ri-history-line me-1"></i> Delivery History
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="driverTabsContent">
                
                <!-- TAB 1: ACTIVE ORDERS -->
                <div class="tab-pane fade show active" id="active-orders" role="tabpanel">
                    @if($activeOrders->isEmpty())
                        <div class="text-center py-5">
                            <i class="ri-checkbox-blank-circle-line text-muted display-4"></i>
                            <h5 class="fw-bold mt-3">No Active Deliveries Assigned</h5>
                            <p class="text-muted mb-3">You have fulfilled all your current deliveries. Check the Available Orders tab to claim new deliveries!</p>
                            <button class="btn btn-outline-primary rounded-pill" onclick="document.getElementById('available-tab').click();">
                                Browse Available Orders
                            </button>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($activeOrders as $order)
                                <div class="col-lg-6">
                                    <div class="card border rounded-4 shadow-sm h-100 {{ $order->order_status === 'in_transit' ? 'border-primary border-2' : '' }}">
                                        <div class="card-header bg-light bg-opacity-50 d-flex justify-content-between align-items-center py-3 border-bottom">
                                            <div>
                                                <span class="fw-bold text-dark fs-5">#{{ $order->order_number }}</span>
                                                <span class="badge bg-info bg-opacity-10 text-info ms-2 text-uppercase fw-semibold">{{ str_replace('_', ' ', $order->order_status) }}</span>
                                            </div>
                                            <span class="fw-bold text-primary fs-5">${{ number_format($order->total_amount, 2) }}</span>
                                        </div>
                                        <div class="card-body p-3">
                                            <!-- Customer & Address -->
                                            <div class="d-flex align-items-start gap-3 mb-3">
                                                <div class="avatar bg-light text-primary rounded-circle p-2 fs-4">
                                                    <i class="ri-user-location-line"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-1">{{ $order->customer->name ?? 'Valued Customer' }}</h6>
                                                    <p class="text-muted small mb-1">
                                                        <i class="ri-map-pin-2-line text-danger me-1"></i>
                                                        {{ $order->shipping_address ?? 'Pickup / Local Store Area' }}
                                                    </p>
                                                    @if($order->customer && $order->customer->phone)
                                                        <a href="tel:{{ $order->customer->phone }}" class="btn btn-xs btn-outline-success rounded-pill py-1 px-2 small">
                                                            <i class="ri-phone-line me-1"></i> Call Customer ({{ $order->customer->phone }})
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Order Items Preview -->
                                            <div class="p-2 bg-light rounded-3 mb-3 small">
                                                <div class="fw-semibold text-secondary mb-1">Package Contents:</div>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($order->items as $item)
                                                        @php
                                                            $qty = $item->qty ?? $item->quantity ?? 1;
                                                            $price = $item->unit_price ?? $item->price ?? 0;
                                                        @endphp
                                                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                                                            <span>{{ $item->product_name ?? $item->product->name ?? 'Item' }} <strong class="text-dark">x{{ $qty }}</strong></span>
                                                            <span class="text-muted">${{ number_format($price * $qty, 2) }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <!-- Payment & Slot Badge -->
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                @if(strtoupper($order->payment_method) === 'COD')
                                                    <span class="badge bg-warning text-dark py-2 px-3 rounded-pill">
                                                        <i class="ri-hand-coin-line me-1"></i> COD: Collect ${{ number_format($order->total_amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success bg-opacity-10 text-success py-2 px-3 rounded-pill">
                                                        <i class="ri-check-double-line me-1"></i> Prepaid Online (${{ number_format($order->total_amount, 2) }})
                                                    </span>
                                                @endif

                                                @if($order->deliverySlot)
                                                    <span class="badge bg-info bg-opacity-10 text-info py-2 px-3 rounded-pill">
                                                        <i class="ri-time-line me-1"></i> {{ $order->deliverySlot->slot_name ?? 'Scheduled Delivery' }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Action Controls -->
                                            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                                @if($order->shipping_address)
                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->shipping_address) }}" target="_blank" class="btn btn-outline-secondary btn-sm flex-fill">
                                                        <i class="ri-navigation-line me-1"></i> Map GPS
                                                    </a>
                                                @endif

                                                @if(in_array($order->order_status, ['assigned', 'Assigned', 'Processing', 'pending']))
                                                    <form method="POST" action="{{ route('driver.orders.status') }}" class="flex-fill">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="status" value="picked_up">
                                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                                            <i class="ri-archive-line me-1"></i> Mark Picked Up
                                                        </button>
                                                    </form>
                                                @elseif($order->order_status === 'picked_up')
                                                    <form method="POST" action="{{ route('driver.orders.status') }}" class="flex-fill">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="status" value="in_transit">
                                                        <button type="submit" class="btn btn-info text-white btn-sm w-100">
                                                            <i class="ri-motorbike-line me-1"></i> Start Navigation (In Transit)
                                                        </button>
                                                    </form>
                                                @elseif($order->order_status === 'in_transit')
                                                    <form method="POST" action="{{ route('driver.orders.status') }}" class="flex-fill">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="status" value="delivered">
                                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                                            <i class="ri-checkbox-circle-fill me-1"></i> Mark Delivered & Complete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- TAB 2: AVAILABLE ORDERS -->
                <div class="tab-pane fade" id="available-orders" role="tabpanel">
                    @if($availableOrders->isEmpty())
                        <div class="text-center py-5">
                            <i class="ri-inbox-line text-muted display-4"></i>
                            <h5 class="fw-bold mt-3">No Unassigned Orders Waiting</h5>
                            <p class="text-muted mb-0">All current orders are assigned to dispatchers. Great job!</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Delivery Location</th>
                                        <th>Items</th>
                                        <th>Total & Payment</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableOrders as $avail)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#{{ $avail->order_number }}</strong>
                                                <div class="small text-muted">{{ $avail->created_at ? $avail->created_at->diffForHumans() : 'Just now' }}</div>
                                            </td>
                                            <td>{{ $avail->customer->name ?? 'Guest Shopper' }}</td>
                                            <td>
                                                <div class="small text-truncate" style="max-width: 250px;">
                                                    <i class="ri-map-pin-line text-danger me-1"></i>
                                                    {{ $avail->shipping_address ?? 'Store Pickup' }}
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-dark">{{ $avail->items->count() }} items</span></td>
                                            <td>
                                                <div class="fw-bold">${{ number_format($avail->total_amount, 2) }}</div>
                                                <span class="badge {{ strtoupper($avail->payment_method) === 'COD' ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success' }}">
                                                    {{ strtoupper($avail->payment_method ?? 'ONLINE') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('driver.orders.assign', $avail->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                                        <i class="ri-hand-coin-line me-1"></i> Accept & Deliver
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- TAB 3: COMPLETED DELIVERIES -->
                <div class="tab-pane fade" id="completed-orders" role="tabpanel">
                    @if($deliveredOrders->isEmpty())
                        <div class="text-center py-5">
                            <i class="ri-history-line text-muted display-4"></i>
                            <h5 class="fw-bold mt-3">No Delivery History Yet</h5>
                            <p class="text-muted mb-0">Delivered packages will appear here for your shift summary.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Delivery Address</th>
                                        <th>Amount</th>
                                        <th>Delivered At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deliveredOrders as $deliv)
                                        <tr>
                                            <td><strong class="text-dark">#{{ $deliv->order_number }}</strong></td>
                                            <td>{{ $deliv->customer->name ?? 'Customer' }}</td>
                                            <td><small class="text-muted">{{ $deliv->shipping_address ?? 'Store Area' }}</small></td>
                                            <td class="fw-bold text-success">${{ number_format($deliv->total_amount, 2) }}</td>
                                            <td><small class="text-muted">{{ $deliv->updated_at ? $deliv->updated_at->format('M d, Y h:i A') : 'N/A' }}</small></td>
                                            <td><span class="badge bg-success rounded-pill"><i class="ri-check-line me-1"></i> Delivered</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
