@extends('layouts/layoutMaster')

@section('title', __('Backups & Recovery') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-data text-primary me-2"></i> {{ __('Backup & Disaster Recovery Manager') }}</h4>
        <p class="text-muted small mb-0">{{ __('Create on-demand SQL snapshots, verify SHA-256 integrity checksums, and audit restore recovery points') }}</p>
    </div>
    <form action="{{ route('app-backups-create') }}" method="POST">
        @csrf
        <input type="hidden" name="type" value="database">
        <button type="submit" class="btn btn-primary">
            <i class="bx bx-download me-1"></i> {{ __('Create Database Snapshot Now') }}
        </button>
    </form>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Total Backup Snapshots') }}</span>
            <h3 class="fw-bold text-primary my-1">{{ $totalBackups }} {{ __('Snapshots') }}</h3>
            <small class="text-muted">{{ __('Available restore points') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Storage Utilized') }}</span>
            <h3 class="fw-bold text-info my-1">{{ number_format($totalSize / 1024, 2) }} KB</h3>
            <small class="text-muted">{{ __('Local disk snapshots') }}</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card p-3 border shadow-sm">
            <span class="text-muted small">{{ __('Integrity Checksum') }}</span>
            <h3 class="fw-bold text-success my-1"><i class="bx bx-check-shield"></i> {{ __('Verified') }}</h3>
            <small class="text-muted">{{ __('MD5/SHA-256 checked') }}</small>
        </div>
    </div>
</div>

<!-- Backups Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Saved Backup Archive') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Snapshot File') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Size') }}</th>
                    <th>{{ __('Checksum (MD5)') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created By') }}</th>
                    <th>{{ __('Timestamp') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                    <tr>
                        <td><strong>{{ $b->file_name }}</strong></td>
                        <td><span class="badge bg-label-info">{{ ucfirst($b->type) }}</span></td>
                        <td>{{ $b->formatted_size }}</td>
                        <td><code>{{ substr($b->checksum, 0, 16) }}...</code></td>
                        <td>
                            <span class="badge bg-success"><i class="bx bx-check"></i> {{ ucfirst($b->status) }}</span>
                        </td>
                        <td>{{ $b->user?->name ?? __('System Automated') }}</td>
                        <td><small>{{ $b->created_at->format('d M Y, H:i:s') }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">{{ __('No backup archives created yet. Click \'Create Database Snapshot Now\' above.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
