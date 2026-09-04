@extends('layouts/layoutMaster')

@section('title', __('Inventory Batches & Expiry Management'))

@section('content')
<div class="row g-6">
    <!-- Near Expiry Alert Banner -->
    @if($nearExpiry->isNotEmpty())
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <div>
                    <strong>{{ __('Near-Expiry Alert:') }}</strong> {{ __('There are :count batches approaching expiration within 7 days.', ['count' => $nearExpiry->count()]) }}
                </div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bx bx-calendar-event text-danger me-2"></i>{{ __('Batch, Lot & Expiry Management (FEFO)') }}</h5>
                <form method="POST" action="{{ route('admin-inventory-batches-sweep') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bx bx-trash me-1"></i>{{ __('Sweep Expired Inventory') }}
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Batch / Lot #') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Available Qty') }}</th>
                                <th>{{ __('Mfg Date') }}</th>
                                <th>{{ __('Expiry Date') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $b)
                                <tr>
                                    <td><strong>{{ $b->batch_number }}</strong></td>
                                    <td>{{ $b->product?->name ?? 'N/A' }}</td>
                                    <td class="fw-bold">{{ $b->qty }}</td>
                                    <td>{{ $b->mfg_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                    <td>
                                        @if($b->expiry_date)
                                            <span class="{{ $b->expiry_date->isPast() ? 'text-danger fw-bold' : ($b->expiry_date->diffInDays(now()) <= 7 ? 'text-warning fw-bold' : '') }}">
                                                {{ $b->expiry_date->format('Y-m-d') }}
                                                @if($b->expiry_date->isPast())
                                                    <span class="badge bg-label-danger ms-1">{{ __('EXPIRED') }}</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('No Expiry') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $bMap = ['active'=>'success', 'near_expiry'=>'warning', 'expired'=>'danger', 'depleted'=>'secondary'];
                                        @endphp
                                        <span class="badge bg-label-{{ $bMap[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">{{ __('No inventory batches recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $batches->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
