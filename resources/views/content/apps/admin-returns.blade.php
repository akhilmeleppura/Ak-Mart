@extends('layouts/layoutMaster')

@section('title', __('RMA Returns & Reverse Logistics'))

@section('content')
<div class="row g-6">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bx bx-undo text-warning me-2"></i>{{ __('RMA Returns & Credit Notes Management') }}</h5>
                <span class="badge bg-label-warning">{{ __('Reverse Logistics Engine') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('RMA #') }}</th>
                                <th>{{ __('Order #') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Reason') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Credit Note') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $r)
                                <tr>
                                    <td><strong>{{ $r->rma_number ?? ('#RMA-' . $r->id) }}</strong></td>
                                    <td>#{{ $r->order?->order_number ?? 'N/A' }}</td>
                                    <td>{{ $r->order?->customer?->name ?? 'Guest' }}</td>
                                    <td><small>{{ Str::limit($r->reason, 35) }}</small></td>
                                    <td class="fw-bold">${{ number_format($r->refund_amount, 2) }}</td>
                                    <td>
                                        @if($r->creditNote)
                                            <span class="badge bg-label-success">{{ $r->creditNote->credit_note_number }}</span>
                                        @else
                                            <span class="text-muted small">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $cMap = ['pending'=>'warning', 'approved'=>'info', 'rejected'=>'danger', 'refunded'=>'success'];
                                        @endphp
                                        <span class="badge bg-label-{{ $cMap[$r->status] ?? 'secondary' }}">{{ ucfirst($r->status) }}</span>
                                    </td>
                                    <td>
                                        @if($r->status === 'pending')
                                            <form method="POST" action="{{ route('admin-returns-rma-inspect', $r->id) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="decision" value="approved_restock">
                                                <button class="btn btn-sm btn-label-success" title="{{ __('Approve & Restock') }}"><i class="bx bx-check"></i> {{ __('Approve') }}</button>
                                            </form>
                                        @elseif($r->status === 'approved')
                                            <form method="POST" action="{{ route('admin-returns-rma-refund', $r->id) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="method" value="wallet">
                                                <button class="btn btn-sm btn-success" title="{{ __('Issue Refund & Credit Note') }}"><i class="bx bx-wallet"></i> {{ __('Issue Refund') }}</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">{{ __('No customer return requests found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $returns->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
