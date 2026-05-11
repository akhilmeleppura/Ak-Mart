@extends('layouts/layoutMaster')

@section('title', 'Notification Hub')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">System Alerts & Notifications</h5>
                <form action="{{ route('app-notifications-mark-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-label-primary">Mark all as read</button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item list-group-item-action d-flex align-items-center {{ $notification->read_at ? 'opacity-75' : 'bg-label-primary' }}">
                            <div class="avatar avatar-md me-4">
                                <span class="avatar-initial rounded-circle bg-white text-primary">
                                    <i class="bx {{ $notification->data['icon'] ?? 'bx-bell' }} font-22px"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold">{{ $notification->data['title'] ?? 'New Notification' }}</h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-body">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                            <div class="ms-4">
                                @if(!$notification->read_at)
                                    <form action="{{ route('app-notifications-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-primary" title="Mark as Read">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i class="bx bx-bell-off display-1 text-muted mb-4"></i>
                            <p class="text-muted">You're all caught up! No new notifications.</p>
                        </div>
                    @endforelse
                </div>
                <div class="p-4 border-top">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
