@extends('layouts/layoutMaster')

@section('title', 'Security Center & Access Audits — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-shield-quarter text-primary me-2"></i> Security Center & Access Audits</h4>
        <p class="text-muted small mb-0">Universal access controls, 2FA coverage monitoring, failed login tracking, and immutable audit logs</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">Total User Accounts</span>
            <h3 class="fw-bold text-primary my-1">{{ $totalUsers }}</h3>
            <small class="text-muted">Registered accounts</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-warning">
            <span class="text-muted small">Supreme Admins</span>
            <h3 class="fw-bold text-warning my-1">{{ $supremeAdminsCount }}</h3>
            <small class="text-muted">Full Gate::before bypass</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-success">
            <span class="text-muted small">Two-Factor Authentication</span>
            <h3 class="fw-bold text-success my-1">{{ $twoFactorEnabledCount }} Users</h3>
            <small class="text-muted">2FA TOTP protected</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border shadow-sm bg-label-info">
            <span class="text-muted small">CSRF & XSS Protection</span>
            <h3 class="fw-bold text-info my-1">Enforced</h3>
            <small class="text-muted">Token protected forms</small>
        </div>
    </div>
</div>

<!-- Audit Log Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Live Security & Operational Audit Trail</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Event</th>
                    <th>User</th>
                    <th>Model / Entity</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAuditLogs as $log)
                    <tr>
                        <td>
                            <span class="badge bg-label-primary">{{ $log->event }}</span>
                        </td>
                        <td>{{ $log->user?->name ?? 'System' }}</td>
                        <td><code>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</code></td>
                        <td><small>{{ $log->ip_address ?: '127.0.0.1' }}</small></td>
                        <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No security events logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
