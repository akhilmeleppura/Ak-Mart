@extends('layouts/layoutMaster')

@section('title', __('Cycle Counting') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-barcode-reader text-primary me-2"></i> {{ __('Cycle Counting & Stock Auditing') }}</h4>
        <p class="text-muted small mb-0">{{ __('Perform barcode-assisted inventory audits, verify on-hand accuracy, and record variance reconciliations') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNewStockCount">
        <i class="bx bx-plus me-1"></i> {{ __('Start Stock Count Session') }}
    </button>
</div>

<!-- Sessions Table -->
<div class="card shadow-sm border">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Stock Audit Sessions') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Session #') }}</th>
                    <th>{{ __('Warehouse / Location') }}</th>
                    <th>{{ __('Audit Type') }}</th>
                    <th>{{ __('SKUs Audited') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Auditor') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($counts as $c)
                    <tr>
                        <td><strong>{{ $c->count_number }}</strong></td>
                        <td>{{ $c->warehouse?->name ?? __('All Warehouses') }}</td>
                        <td>
                            <span class="badge bg-label-info">{{ ucfirst($c->type) }} {{ __('Count') }}</span>
                        </td>
                        <td><span class="badge bg-label-primary">{{ $c->items_count }} {{ __('Items') }}</span></td>
                        <td>
                            <span class="badge {{ $c->status === 'reconciled' ? 'bg-success' : ($c->status === 'in_progress' ? 'bg-warning' : 'bg-secondary') }}">
                                {{ ucfirst($c->status) }}
                            </span>
                        </td>
                        <td>{{ $c->user?->name ?? __('Staff') }}</td>
                        <td><small>{{ $c->created_at->format('d M Y, H:i') }}</small></td>
                        <td>
                            <a href="{{ route('app-stock-counts-show', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show me-1"></i> {{ __('View Audit') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">{{ __('No stock count sessions recorded. Click \'Start Stock Count Session\' above to begin an audit.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Start Stock Count -->
<div class="modal fade" id="modalNewStockCount" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-stock-counts-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bx bx-barcode text-primary me-1"></i> {{ __('Start Stock Count Session') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Audit Type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="cycle">{{ __('Cycle Count (Regular Periodic Check)') }}</option>
                            <option value="full">{{ __('Full Physical Inventory Count') }}</option>
                            <option value="partial">{{ __('Spot Check / Partial Count') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Target Warehouse') }}</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">{{ __('All Locations / Global') }}</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Notes / Audit Purpose') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('e.g. Monthly electronics cycle audit') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Initialize Audit Sheet') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
