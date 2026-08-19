@extends('layouts/layoutMaster')

@section('title', __('Email Notification Templates') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-envelope text-primary me-2"></i> {{ __('Transactional Email Templates') }}</h4>
        <p class="text-muted small mb-0">{{ __('Manage automated system emails for order receipts, tracking notifications, cart recovery, and welcome perks') }}</p>
    </div>
</div>

<div class="row g-4">
    @foreach($templates as $tpl)
        <div class="col-md-6">
            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-primary">{{ $tpl->name }}</h6>
                        <small class="text-muted">Key: <code>{{ $tpl->key }}</code></small>
                    </div>
                    <span class="badge {{ $tpl->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                        {{ $tpl->is_active ? __('Active') : __('Disabled') }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('app-email-templates-update', $tpl->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __('Email Subject Line') }}</label>
                            <input type="text" name="subject" class="form-control" value="{{ $tpl->subject }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __('Email Body Content') }}</label>
                            <textarea name="body" class="form-control font-monospace small" rows="7" required>{{ $tpl->body }}</textarea>
                        </div>

                        @if(!empty($tpl->variables))
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">{{ __('Supported Dynamic Variables:') }}</label>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($tpl->variables as $var)
                                        <span class="badge bg-label-info font-monospace small cursor-pointer" onclick="navigator.clipboard.writeText('@{{ ' . '{{' . $var . '}}' . ' }}'); alert('Copied variable');">
                                            @{{ '{{' . $var . '}}' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $tpl->is_active ? 'checked' : '' }}>
                                <label class="form-check-label small">{{ __('Enabled') }}</label>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="bx bx-save me-1"></i> {{ __('Save Template') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
