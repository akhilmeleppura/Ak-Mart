@extends('layouts/layoutMaster')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('app-vendor-support') }}" class="btn btn-sm btn-icon btn-label-secondary me-3">
                        <i class="bx bx-chevron-left"></i>
                    </a>
                    <div>
                        <h5 class="card-title mb-0">{{ $ticket->subject }}</h5>
                        <small class="text-muted">Ticket #{{ $ticket->id }} — Customer: {{ $ticket->user->name }}</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('app-vendor-support-status', $ticket->id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(['open', 'in_progress', 'resolved', 'closed'] as $st)
                                <option value="{{ $st }}" {{ $ticket->status === $st ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $st)) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body p-6" style="background: #f8f9fa; max-height: 500px; overflow-y: auto;">
                <div class="d-flex flex-column gap-4">
                    @foreach($ticket->messages as $msg)
                        @php $isMe = $msg->user_id === auth()->id(); @endphp
                        <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 80%;">
                                <div class="p-3 rounded {{ $isMe ? 'bg-primary text-white' : 'bg-white border' }}">
                                    <p class="mb-0">{{ $msg->message }}</p>
                                </div>
                                <small class="text-muted mt-1" style="font-size: 10px;">{{ $msg->user->name }} • {{ $msg->created_at->format('H:i') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer border-top p-4">
                @if($ticket->status !== 'closed')
                    <form action="{{ route('app-vendor-support-reply', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <textarea name="message" class="form-control" rows="1" placeholder="Type your reply..." required></textarea>
                            <button class="btn btn-primary" type="submit">
                                <i class="bx bx-send"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-secondary text-center mb-0">This ticket is closed and cannot be replied to.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
