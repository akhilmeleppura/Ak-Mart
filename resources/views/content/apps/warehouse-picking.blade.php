@extends('layouts/layoutMaster')

@section('title', __('Warehouse Picking & Packing Stations'))

@section('content')
<div class="row g-6">
    <div class="col-12">
        <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bx bx-package text-primary me-2"></i>{{ __('Warehouse Picking & Packing Hub') }}</h5>
                <span class="badge bg-label-primary">{{ __('Live Station Active') }}</span>
            </div>
            <div class="card-body pt-4">
                <ul class="nav nav-tabs nav-fill mb-4" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-picking">
                            <i class="bx bx-barcode-reader me-1"></i> {{ __('Pending Picking Queue') }} ({{ $pendingPicking->total() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-packing">
                            <i class="bx bx-box me-1"></i> {{ __('Packing & Dispatch Station') }} ({{ $pendingPacking->total() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-0">
                    <!-- Tab 1: Picking -->
                    <div class="tab-pane fade show active" id="tab-picking" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Fulfillment #') }}</th>
                                        <th>{{ __('Order') }}</th>
                                        <th>{{ __('Items Count') }}</th>
                                        <th>{{ __('Picker') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingPicking as $f)
                                        <tr>
                                            <td><strong>#{{ $f->fulfillment_number }}</strong></td>
                                            <td>
                                                <span class="fw-semibold">#{{ $f->order?->order_number ?? 'N/A' }}</span>
                                                <br><small class="text-muted">{{ $f->order?->customer?->name ?? 'Guest' }}</small>
                                            </td>
                                            <td>{{ $f->items->count() }} {{ __('lines') }}</td>
                                            <td>{{ $f->picker?->name ?? __('Unassigned') }}</td>
                                            <td><span class="badge bg-label-warning">{{ ucfirst($f->status) }}</span></td>
                                            <td>
                                                @if($f->status === 'unfulfilled')
                                                    <form method="POST" action="{{ route('admin-fulfillment-picking-start', $f->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-primary"><i class="bx bx-play me-1"></i>{{ __('Start Pick') }}</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin-fulfillment-picking-complete', $f->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success"><i class="bx bx-check me-1"></i>{{ __('Complete Pick') }}</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">{{ __('No pending picking orders found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $pendingPicking->links() }}</div>
                    </div>

                    <!-- Tab 2: Packing -->
                    <div class="tab-pane fade" id="tab-packing" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Fulfillment #') }}</th>
                                        <th>{{ __('Order #') }}</th>
                                        <th>{{ __('Packages') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingPacking as $fp)
                                        <tr>
                                            <td><strong>#{{ $fp->fulfillment_number }}</strong></td>
                                            <td>{{ $fp->order?->order_number ?? 'N/A' }}</td>
                                            <td>
                                                @if($fp->packages->isNotEmpty())
                                                    @foreach($fp->packages as $pkg)
                                                        <span class="badge bg-label-info">{{ $pkg->package_barcode }} ({{ $pkg->weight_kg }} kg)</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">{{ __('None sealed') }}</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-label-info">{{ ucfirst($fp->status) }}</span></td>
                                            <td>
                                                <form method="POST" action="{{ route('admin-fulfillment-dispatch', $fp->id) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-label-success"><i class="bx bx-send me-1"></i>{{ __('Dispatch') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">{{ __('No orders awaiting packing.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $pendingPacking->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
