@extends('layouts/layoutMaster')

@section('title', 'Dunning System - SaaS Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-4">
                <div>
                    <h5 class="card-title mb-1">Dunning System Management</h5>
                    <p class="text-muted mb-0 small">Monitor failed payments and automated recovery sequences.</p>
                </div>
                <form action="{{ route('app-saas-dunning-trigger') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-play me-1"></i> Trigger Dunning Process
                    </button>
                </form>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <h6 class="mt-4 mb-3">Subscriptions Currently Past Due</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Store</th>
                                <th>Plan</th>
                                <th>Period End</th>
                                <th>Days Overdue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pastDueSubscriptions as $sub)
                                @php
                                    $daysOverdue = $sub->current_period_end ? (int) $sub->current_period_end->diffInDays(now()) : 0;
                                @endphp
                                <tr>
                                    <td><strong>{{ $sub->branch->name ?? 'Store #'.$sub->branch_id }}</strong></td>
                                    <td>{{ $sub->plan->name ?? 'N/A' }}</td>
                                    <td>{{ $sub->current_period_end ? $sub->current_period_end->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-label-danger">{{ $daysOverdue }} Days</span>
                                    </td>
                                    <td><span class="badge bg-label-warning">Past Due</span></td>
                                    <td>
                                        <a href="{{ route('app-user-view-account') }}" class="btn btn-sm btn-icon btn-label-primary" title="View Store">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No subscriptions are currently past due.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr class="my-6">

                <h6 class="mb-3">Recent Dunning Logs</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Store</th>
                                <th>Attempt</th>
                                <th>Action Taken</th>
                                <th>Email Sent</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dunningLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $log->branch->name ?? 'Store #'.$log->branch_id }}</td>
                                    <td><span class="badge bg-label-secondary">Day {{ $log->attempt_number }}</span></td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'email_reminder' => 'info',
                                                'grace_period_warning' => 'warning',
                                                'subscription_suspended' => 'danger',
                                                'subscription_canceled' => 'dark'
                                            ];
                                            $color = $typeColors[$log->type] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-label-{{ $color }}">{{ ucwords(str_replace('_', ' ', $log->type)) }}</span>
                                    </td>
                                    <td>
                                        @if($log->email_sent)
                                            <span class="badge bg-label-success"><i class="bx bx-check me-1"></i> Yes</span>
                                        @else
                                            <span class="badge bg-label-secondary"><i class="bx bx-x me-1"></i> No</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $log->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No dunning logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $dunningLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
