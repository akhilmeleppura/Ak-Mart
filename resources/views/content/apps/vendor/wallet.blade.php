@extends('layouts/layoutMaster')

@section('title', 'Vendor Wallet & Payouts')

@section('content')
<div class="row">

  {{-- Balance Cards --}}
  <div class="col-sm-6 col-xl-3 mb-6">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-success p-2 me-3"><i class="bx bx-wallet bx-md"></i></div>
        <div>
          <small class="text-muted">Available Balance</small>
          <h4 class="mb-0 text-success">${{ number_format($wallet->available_balance, 2) }}</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-6">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-warning p-2 me-3"><i class="bx bx-time bx-md"></i></div>
        <div>
          <small class="text-muted">Pending Clearance</small>
          <h4 class="mb-0 text-warning">${{ number_format($wallet->pending_balance, 2) }}</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-6">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-primary p-2 me-3"><i class="bx bx-trending-up bx-md"></i></div>
        <div>
          <small class="text-muted">Total Earned</small>
          <h4 class="mb-0">${{ number_format($wallet->total_earned, 2) }}</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-6">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded-pill bg-label-info p-2 me-3"><i class="bx bx-transfer bx-md"></i></div>
        <div>
          <small class="text-muted">Total Withdrawn</small>
          <h4 class="mb-0">${{ number_format($wallet->total_withdrawn, 2) }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- KYC Warning --}}
  @if(!$wallet->kyc_verified)
  <div class="col-12 mb-6">
    <div class="alert alert-warning d-flex align-items-center">
      <i class="bx bx-shield-x bx-md me-3"></i>
      <div>
        <strong>KYC Verification Required</strong> — Your account has not been verified yet. You cannot request payouts until an admin verifies your identity. Please contact support.
      </div>
    </div>
  </div>
  @endif

  {{-- Request Payout --}}
  <div class="col-12 col-lg-5 mb-6">
    <div class="card h-100">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Request Payout</h5>
      </div>
      <div class="card-body">
        @if(!$wallet->kyc_verified)
          <p class="text-muted text-center py-4">KYC verification pending. Payouts locked.</p>
        @else
        <form id="payoutForm">
          @csrf
          <div class="mb-4">
            <label class="form-label">Amount (USD)</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" id="payoutAmount" name="amount" class="form-control" min="10" step="0.01"
                placeholder="Min. $10.00" max="{{ $wallet->available_balance }}">
            </div>
            <div class="form-text">Available: <strong>${{ number_format($wallet->available_balance, 2) }}</strong></div>
          </div>
          <div class="mb-4">
            <label class="form-label">Payout Method</label>
            <select name="payout_method" class="form-select">
              <option value="bank_transfer">Bank Transfer</option>
              <option value="paypal">PayPal</option>
              <option value="upi">UPI</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100" id="payoutBtn">
            <i class="bx bx-money-withdraw me-1"></i> Request Payout
          </button>
        </form>
        @endif
      </div>
    </div>
  </div>

  {{-- Payout History --}}
  <div class="col-12 col-lg-7 mb-6">
    <div class="card h-100">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Payout History</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Ref</th>
              </tr>
            </thead>
            <tbody>
              @forelse($payoutRequests as $payout)
              <tr>
                <td>{{ $payout->created_at->format('M d, Y') }}</td>
                <td class="fw-bold">${{ number_format($payout->amount, 2) }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $payout->payout_method)) }}</td>
                <td>
                  @php
                    $colors = ['pending'=>'warning','processing'=>'info','completed'=>'success','rejected'=>'danger'];
                    $color = $colors[$payout->status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-label-{{ $color }}">{{ ucfirst($payout->status) }}</span>
                </td>
                <td class="text-muted small">{{ $payout->transaction_reference ?? '—' }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-5">No payout requests yet.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Transaction Ledger --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Recent Order Commissions</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Order</th>
                <th>Order Total</th>
                <th>Platform Fee</th>
                <th>Your Earning</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTransactions as $txn)
              <tr>
                <td>{{ $txn->created_at->format('M d, Y') }}</td>
                <td>
                  @if($txn->order)
                    <a href="{{ route('app-ecommerce-order-details', $txn->order_id) }}">#{{ $txn->order->order_number ?? $txn->order_id }}</a>
                  @else
                    #{{ $txn->order_id }}
                  @endif
                </td>
                <td>${{ number_format($txn->total_amount, 2) }}</td>
                <td class="text-danger">-${{ number_format($txn->platform_fee, 2) }}</td>
                <td class="text-success fw-bold">+${{ number_format($txn->vendor_earning, 2) }}</td>
                <td>
                  @php $tc = ['pending'=>'warning','cleared'=>'success','refunded'=>'danger']; @endphp
                  <span class="badge bg-label-{{ $tc[$txn->status] ?? 'secondary' }}">{{ ucfirst($txn->status) }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-5">No transactions yet. Complete an order to see earnings.</td>
              </tr>
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
  const form = document.getElementById('payoutForm');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('payoutBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

    const formData = new FormData(this);

    fetch('{{ route("app-vendor-wallet-payout") }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: data.success ? 'success' : 'error',
          title: data.success ? 'Payout Requested' : 'Request Failed',
          text: data.message,
          timer: 2000,
          showConfirmButton: !data.success
        }).then(() => { if (data.success) location.reload(); });
      } else {
        alert(data.message);
        if (data.success) location.reload();
      }

      if (!data.success) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-money-withdraw me-1"></i> Request Payout';
      }
    });
  });
});
</script>
@endsection
