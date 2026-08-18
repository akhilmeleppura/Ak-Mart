@extends('layouts/layoutMaster')

@section('title', 'Developer Webhooks & APIs — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-code-curly text-primary me-2"></i> Outbound Webhook Subscriptions</h4>
        <p class="text-muted small mb-0">Subscribe external ERPs, CRM systems, and warehouse automation endpoints to real-time e-commerce events</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddWebhook">
        <i class="bx bx-plus me-1"></i> Register Webhook Endpoint
    </button>
</div>

<!-- Webhook Subscriptions Table -->
<div class="card shadow-sm border mb-4">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Active Webhook Endpoints</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Endpoint Name</th>
                    <th>Target URL</th>
                    <th>Subscribed Events</th>
                    <th>Deliveries</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                    <tr>
                        <td><strong>{{ $sub->name }}</strong></td>
                        <td><code>{{ $sub->target_url }}</code></td>
                        <td>
                            @foreach((array)$sub->events as $evt)
                                <span class="badge bg-label-info">{{ $evt }}</span>
                            @endforeach
                        </td>
                        <td><span class="badge bg-label-primary">{{ $sub->logs_count }} Dispatches</span></td>
                        <td>
                            <span class="badge {{ $sub->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $sub->is_active ? 'Active' : 'Paused' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('app-developer-webhooks-ping', $sub->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-info" title="Send Test Ping">
                                    <i class="bx bx-podcast"></i> Ping
                                </button>
                            </form>
                            <form action="{{ route('app-developer-webhooks-toggle', $sub->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    {{ $sub->is_active ? 'Pause' : 'Resume' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No webhook subscriptions registered. Click 'Register Webhook Endpoint' above.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Delivery Logs -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Recent Webhook Dispatch Logs</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Event</th>
                    <th>Endpoint</th>
                    <th>Response Code</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td><code>{{ $log->event }}</code></td>
                        <td>{{ $log->subscription?->name ?? 'Endpoint' }}</td>
                        <td>
                            <span class="badge {{ $log->response_status == 200 ? 'bg-success' : 'bg-danger' }}">
                                {{ $log->response_status ?: 'Error' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $log->status === 'delivered' ? 'bg-label-success' : 'bg-label-danger' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No dispatch logs recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add Webhook -->
<div class="modal fade" id="modalAddWebhook" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-developer-webhooks-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Register Outbound Webhook</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Endpoint Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. ERP Warehouse Sync" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Webhook URL <span class="text-danger">*</span></label>
                        <input type="url" name="target_url" class="form-control" placeholder="https://api.myerp.com/webhooks/orders" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trigger Events</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]" value="order.created" id="ev1" checked>
                                    <label class="form-check-label" for="ev1">order.created</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]" value="order.paid" id="ev2" checked>
                                    <label class="form-check-label" for="ev2">order.paid</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]" value="order.shipped" id="ev3" checked>
                                    <label class="form-check-label" for="ev3">order.shipped</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]" value="product.updated" id="ev4">
                                    <label class="form-check-label" for="ev4">product.updated</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]" value="inventory.updated" id="ev5">
                                    <label class="form-check-label" for="ev5">inventory.updated</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]" value="customer.created" id="ev6">
                                    <label class="form-check-label" for="ev6">customer.created</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Subscribe Endpoint</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
