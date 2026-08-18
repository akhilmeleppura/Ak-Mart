@extends('layouts/layoutMaster')

@section('title', 'Abandoned Cart Recovery — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-cart-alt text-primary me-2"></i> Abandoned Cart Recovery</h4>
        <p class="text-muted small mb-0">Track dropped checkout sessions and trigger automated email win-back campaigns with discount vouchers</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm bg-label-danger">
            <span class="text-muted small">Active Abandoned Carts</span>
            <h3 class="fw-bold text-danger my-1">{{ $totalAbandoned }} Carts</h3>
            <small class="text-muted">Unfinished checkouts</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm bg-label-warning">
            <span class="text-muted small">Potential Recoverable Revenue</span>
            <h3 class="fw-bold text-warning my-1">${{ number_format($potentialRevenue, 2) }}</h3>
            <small class="text-muted">Pending pipeline</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm bg-label-success">
            <span class="text-muted small">Successfully Recovered Carts</span>
            <h3 class="fw-bold text-success my-1">{{ $totalRecovered }} Carts</h3>
            <small class="text-muted">Converted via campaigns</small>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Abandoned Checkout Sessions</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Customer</th>
                    <th>Cart Value</th>
                    <th>Items</th>
                    <th>Recovery Link</th>
                    <th>Emails Sent</th>
                    <th>Status</th>
                    <th>Abandoned Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carts as $cart)
                    <tr>
                        <td>
                            <strong>{{ $cart->user?->name ?? 'Guest Visitor' }}</strong><br>
                            <small class="text-muted">{{ $cart->email ?: $cart->phone }}</small>
                        </td>
                        <td class="fw-bold text-primary">${{ number_format($cart->total_amount, 2) }}</td>
                        <td><span class="badge bg-label-info">{{ count($cart->cart_data) }} Items</span></td>
                        <td>
                            <small><code>?recover={{ substr($cart->recovery_token, 0, 10) }}...</code></small>
                        </td>
                        <td><span class="badge bg-label-secondary">{{ $cart->recovery_emails_sent }} Sent</span></td>
                        <td>
                            <span class="badge {{ $cart->recovered_at ? 'bg-success' : 'bg-danger' }}">
                                {{ $cart->recovered_at ? 'Recovered' : 'Abandoned' }}
                            </span>
                        </td>
                        <td><small>{{ $cart->created_at->diffForHumans() }}</small></td>
                        <td>
                            @if(!$cart->recovered_at)
                                <form action="{{ route('app-abandoned-carts-send', $cart->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-send me-1"></i> Send Recovery
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-label-success"><i class="bx bx-check"></i> Converted</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No abandoned carts detected.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $carts->links() }}
    </div>
</div>
@endsection
