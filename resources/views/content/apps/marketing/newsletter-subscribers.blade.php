@extends('layouts/layoutMaster')

@section('title', 'Newsletter & Subscriber Management - Marketing')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bx bx-envelope text-primary me-2"></i>{{ __('Newsletter & Subscriber Management') }}</h4>
            <p class="text-muted mb-0">{{ __('Manage email subscribers collected from storefront footer, checkout opt-ins, and send broadcast campaigns.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('app-newsletter-subscribers.export') }}" class="btn btn-label-secondary">
                <i class="bx bx-download me-1"></i> {{ __('Export CSV') }}
            </a>
            <button type="button" class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#sendCampaignModal">
                <i class="bx bx-paper-plane me-1"></i> {{ __('Broadcast Email') }}
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                <i class="bx bx-plus me-1"></i> {{ __('Add Subscriber') }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-xs rounded-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">{{ __('Total Subscribers') }}</span>
                        <h3 class="fw-bold my-1 text-dark">{{ number_format($totalSubscribers) }}</h3>
                        <span class="badge bg-label-secondary small">{{ __('All Time') }}</span>
                    </div>
                    <div class="avatar avatar-lg bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bx bx-group fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-xs rounded-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">{{ __('Active Audience') }}</span>
                        <h3 class="fw-bold my-1 text-success">{{ number_format($activeSubscribers) }}</h3>
                        <span class="badge bg-label-success small">{{ __('Eligible for Emails') }}</span>
                    </div>
                    <div class="avatar avatar-lg bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bx bx-mail-send fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-xs rounded-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">{{ __('Unsubscribed') }}</span>
                        <h3 class="fw-bold my-1 text-warning">{{ number_format($unsubscribedCount) }}</h3>
                        <span class="badge bg-label-warning small">{{ __('Opted Out') }}</span>
                    </div>
                    <div class="avatar avatar-lg bg-label-warning rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bx bx-user-x fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-xs rounded-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">{{ __('New This Month') }}</span>
                        <h3 class="fw-bold my-1 text-info">{{ number_format($newThisMonth) }}</h3>
                        <span class="badge bg-label-info small">{{ __('Recent Growth') }}</span>
                    </div>
                    <div class="avatar avatar-lg bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bx bx-trending-up fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriber Table Card -->
    <div class="card border shadow-xs rounded-4 overflow-hidden">
        <div class="card-header border-bottom bg-light py-3">
            <form action="{{ route('app-newsletter-subscribers') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="{{ __('Search by email address...') }}" value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="subscribed" {{ request('status') === 'subscribed' ? 'selected' : '' }}>{{ __('Subscribed (Active)') }}</option>
                        <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>{{ __('Unsubscribed') }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('app-newsletter-subscribers') }}" class="btn btn-label-secondary">{{ __('Reset') }}</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Subscriber Email') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Source') }}</th>
                        <th>{{ __('Joined Date') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $sub)
                        <tr>
                            <td class="text-muted small">#{{ $sub->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold">
                                        {{ strtoupper(substr($sub->email, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark">{{ $sub->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($sub->status === 'subscribed')
                                    <span class="badge bg-label-success"><i class="bx bx-check me-1"></i>{{ __('Subscribed') }}</span>
                                @else
                                    <span class="badge bg-label-warning"><i class="bx bx-x me-1"></i>{{ __('Unsubscribed') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-secondary font-monospace small">
                                    {{ $sub->source ?: 'storefront_footer' }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    {{ $sub->subscribed_at ? $sub->subscribed_at->format('M d, Y • h:i A') : $sub->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <form action="{{ route('app-newsletter-subscribers.toggle', $sub->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-icon {{ $sub->status === 'subscribed' ? 'btn-label-warning' : 'btn-label-success' }}" title="{{ $sub->status === 'subscribed' ? __('Mark Unsubscribed') : __('Mark Subscribed') }}">
                                            <i class="bx {{ $sub->status === 'subscribed' ? 'bx-user-x' : 'bx-user-check' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('app-newsletter-subscribers.destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this subscriber?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="{{ __('Delete Subscriber') }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bx bx-envelope fs-1 text-muted mb-2"></i>
                                <h6 class="text-muted fw-bold">{{ __('No subscribers found') }}</h6>
                                <p class="small text-muted mb-0">{{ __('Subscribers collected via the storefront footer will appear here automatically.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscribers->hasPages())
            <div class="card-footer d-flex justify-content-end py-2">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Subscriber -->
<div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bx bx-user-plus text-primary me-2"></i>{{ __('Add Newsletter Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('app-newsletter-subscribers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="customer@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Subscription Status') }}</label>
                        <select name="status" class="form-select">
                            <option value="subscribed">{{ __('Subscribed (Active)') }}</option>
                            <option value="unsubscribed">{{ __('Unsubscribed') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Source / Channel') }}</label>
                        <input type="text" name="source" class="form-control" placeholder="admin_manual" value="admin_manual">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold">{{ __('Save Subscriber') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Broadcast Newsletter Campaign -->
<div class="modal fade" id="sendCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bx bx-paper-plane text-primary me-2"></i>{{ __('Broadcast Newsletter & Weekly Flyer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('app-newsletter-subscribers.campaign') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info d-flex align-items-center gap-2 rounded-3 mb-4">
                        <i class="bx bx-info-circle fs-4"></i>
                        <span>{{ __('This campaign will be delivered to all') }} <strong>{{ number_format($activeSubscribers) }} {{ __('active subscribers') }}</strong>.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Email Subject Line') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="🎉 Weekly Supermarket Flyer: 20% OFF Organic Produce & Dairy" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Promo Headline') }}</label>
                        <input type="text" name="headline" class="form-control" placeholder="Fresh Discounts & Special Weekend Deals Just for You!">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Campaign Message / Body') }} <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="6" placeholder="Hi Valued Customer,&#10;&#10;Check out our freshest arrivals and special discounts this week at AK-Mart!&#10;Use coupon WELCOME10 for extra savings on your grocery basket.&#10;&#10;Shop online now at: {{ url('/store') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Discard') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="bx bx-send me-1"></i>{{ __('Send Campaign') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
