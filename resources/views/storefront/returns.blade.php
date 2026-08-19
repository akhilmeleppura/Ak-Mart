@extends('layouts.storefrontMaster')

@section('title', __('Customer Returns & Refunds') . ' — AK-Mart')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="bx bx-revision text-primary me-2"></i> {{ __('Customer Returns & Exchange Portal') }}</h3>
            <p class="text-muted mb-0">{{ __('Hassle-free grocery returns, instant store credit refunds, and doorstep replacement requests.') }}</p>
        </div>
        <a href="{{ route('storefront.shop') }}" class="btn btn-outline-primary rounded-pill btn-sm">
            <i class="bx bx-store me-1"></i> {{ __('Continue Shopping') }}
        </a>
    </div>

    <div class="row g-4">
        <!-- Return Request Form -->
        <div class="col-lg-6">
            <div class="card p-4 border shadow-sm rounded-4 h-100 bg-white">
                <h5 class="fw-bold mb-3"><i class="bx bx-file-blank text-primary me-2"></i> {{ __('Initiate a New Return / Exchange') }}</h5>
                <p class="text-muted small mb-4">{{ __('Enter your Order Number and select the issue. Our inspection team handles requests within 24 hours.') }}</p>

                <form action="{{ route('storefront.returns.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ __('Order Number') }} <span class="text-danger">*</span></label>
                        @if($orders->isNotEmpty())
                            <select name="order_number" class="form-select" required>
                                <option value="">{{ __('Select from your recent orders...') }}</option>
                                @foreach($orders as $o)
                                    <option value="{{ $o->order_number }}">{{ $o->order_number }} — ${{ number_format($o->total_amount, 2) }} ({{ $o->created_at->format('M d, Y') }})</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" name="order_number" class="form-control" placeholder="e.g. ORD-YAVAX6RWOS" required>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ __('Return / Exchange Reason') }} <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="">{{ __('Select reason...') }}</option>
                            <option value="Damaged / Broken Packaging">{{ __('Damaged or Broken Packaging') }}</option>
                            <option value="Expired / Freshness Issue">{{ __('Expired or Freshness Issue') }}</option>
                            <option value="Wrong Item Delivered">{{ __('Wrong Item Delivered') }}</option>
                            <option value="Missing Item in Package">{{ __('Missing Item in Package') }}</option>
                            <option value="Quality Did Not Meet Expectations">{{ __('Quality Did Not Meet Expectations') }}</option>
                            <option value="Other Issue">{{ __('Other Reason') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ __('Additional Details & Comments') }}</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="{{ __('Describe the issue in detail to expedite your refund or replacement...') }}"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">{{ __('Upload Photo / Proof (Optional)') }}</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <small class="text-muted">{{ __('Attach photo of damaged item or packaging (Max 2MB).') }}</small>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2.5">
                        <i class="bx bx-paper-plane me-1"></i> {{ __('Submit Return Request') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Return History & Policies -->
        <div class="col-lg-6">
            <!-- Return Policy Highlights -->
            <div class="card p-4 border shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-primary">
                <h5 class="fw-bold text-primary mb-3"><i class="bx bx-shield-quarter me-2"></i> {{ __('AK-Mart 100% Freshness Guarantee') }}</h5>
                <ul class="text-muted small ps-3 mb-0 d-flex flex-column gap-2">
                    <li><strong>{{ __('Same-Day Pickup') }}:</strong> {{ __('Our courier will collect the return package from your doorstep.') }}</li>
                    <li><strong>{{ __('Instant Store Credit') }}:</strong> {{ __('Get instant wallet credit or original payment method refund.') }}</li>
                    <li><strong>{{ __('No-Questions-Asked Fresh Produce Policy') }}:</strong> {{ __('If any vegetable, fruit, or dairy item is not fresh, we replace it immediately.') }}</li>
                </ul>
            </div>

            <!-- Previous Returns List -->
            <div class="card p-4 border shadow-sm rounded-4 bg-white">
                <h5 class="fw-bold mb-3"><i class="bx bx-history text-primary me-2"></i> {{ __('Your Recent Return Requests') }}</h5>

                @if($myReturns->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-check-double fs-1 mb-2 text-success"></i>
                        <p class="mb-0">{{ __('No active return requests found. All your orders are in good standing!') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Return #') }}</th>
                                    <th>{{ __('Reason') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myReturns as $ret)
                                    <tr>
                                        <td><strong>{{ $ret->return_number }}</strong></td>
                                        <td class="small">{{ $ret->reason }}</td>
                                        <td>
                                            @if($ret->status === 'pending')
                                                <span class="badge bg-label-warning">{{ __('Under Review') }}</span>
                                            @elseif($ret->status === 'approved')
                                                <span class="badge bg-label-info">{{ __('Approved / Pickup Scheduled') }}</span>
                                            @elseif($ret->status === 'refunded')
                                                <span class="badge bg-label-success">{{ __('Refunded') }}</span>
                                            @else
                                                <span class="badge bg-label-danger">{{ __('Rejected') }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $ret->created_at->format('M d') }}</td>
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
@endsection
