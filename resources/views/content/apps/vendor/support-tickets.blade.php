@extends('layouts/layoutMaster')

@section('title', 'Support Tickets - Vendor')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Customer Support Tickets</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Customer</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td><strong>#{{ $ticket->id }}</strong></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="avatar-wrapper">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($ticket->user->name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-body text-truncate fw-semibold">{{ $ticket->user->name }}</span>
                                                <small class="text-muted">{{ $ticket->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php $pColors = ['low'=>'info', 'medium'=>'primary', 'high'=>'warning', 'urgent'=>'danger']; @endphp
                                        <span class="badge bg-label-{{ $pColors[$ticket->priority] ?? 'secondary' }}">{{ ucfirst($ticket->priority) }}</span>
                                    </td>
                                    <td>
                                        @php $sColors = ['open'=>'success', 'in_progress'=>'info', 'resolved'=>'secondary', 'closed'=>'dark']; @endphp
                                        <span class="badge bg-label-{{ $sColors[$ticket->status] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $ticket->status)) }}</span>
                                    </td>
                                    <td class="small">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('app-vendor-support-show', $ticket->id) }}" class="btn btn-sm btn-icon btn-label-primary">
                                            <i class="bx bx-chat"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-muted">No support tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
