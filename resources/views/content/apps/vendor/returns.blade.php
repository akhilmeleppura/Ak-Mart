@extends('layouts/layoutMaster')

@section('title', 'Return & Refund Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Return Requests</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Refunded</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                                <tr>
                                    <td><span class="fw-bold text-primary">{{ $req->order->order_number }}</span></td>
                                    <td>{{ $req->order->user->name ?? 'Guest' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $req->reason }}</div>
                                        <small class="text-muted">{{ Str::limit($req->details, 40) }}</small>
                                    </td>
                                    <td>
                                        @php $colors = ['pending'=>'warning', 'approved'=>'info', 'rejected'=>'danger', 'refunded'=>'success']; @endphp
                                        <span class="badge bg-label-{{ $colors[$req->status] }}">{{ ucfirst($req->status) }}</span>
                                    </td>
                                    <td>{{ $req->refund_amount ? '$' . number_format($req->refund_amount, 2) : '-' }}</td>
                                    <td>{{ $req->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#editReturnModal{{ $req->id }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editReturnModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Return Request</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('app-vendor-returns-update', $req->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-4">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $req->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="rejected" {{ $req->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                            <option value="refunded" {{ $req->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label">Refund Amount</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" step="0.01" name="refund_amount" class="form-control" value="{{ $req->refund_amount ?? $req->order->total_amount }}">
                                                        </div>
                                                        <small class="text-muted">Original Order Total: ${{ number_format($req->order->total_amount, 2) }}</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10">No return requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
