@extends('layouts/layoutMaster')

@section('title', __('System Health Diagnostics') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-pulse text-primary me-2"></i> {{ __('System Health & Server Diagnostics') }}</h4>
        <p class="text-muted small mb-0">{{ __('Real-time telemetry probing MySQL latency, Cache write speeds, Storage disk I/O, and Queue worker heartbeats') }}</p>
    </div>
    <a href="{{ route('app-system-health') }}" class="btn btn-primary">
        <i class="bx bx-refresh me-1"></i> {{ __('Run Live Diagnostics') }}
    </a>
</div>

<!-- Score Banner -->
<div class="card p-4 shadow-sm border mb-4 bg-label-primary">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-primary mb-1">{{ __('Health Score:') }} {{ $diagnostics['score'] }}%</span>
            <h3 class="fw-bold mb-0 text-heading">{{ __('System Status:') }} <span class="text-success">{{ $diagnostics['status'] }}</span></h3>
            <small class="text-muted">{{ __('Diagnostic evaluated at') }} {{ $diagnostics['timestamp'] }}</small>
        </div>
        <div class="display-5 fw-bold text-primary">
            {{ $diagnostics['score'] }} / 100
        </div>
    </div>
</div>

<!-- Diagnostics Grid -->
<div class="row g-4">
    @foreach($diagnostics['checks'] as $key => $check)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-heading">{{ $check['name'] }}</h6>
                        <span class="badge {{ ($check['status'] ?? '') === 'healthy' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($check['status'] ?? __('Active')) }}
                        </span>
                    </div>
                    @if(isset($check['latency']))
                        <p class="mb-1 small">{{ __('Latency:') }} <strong>{{ $check['latency'] }}</strong></p>
                    @endif
                    @if(isset($check['php_version']))
                        <p class="mb-1 small">PHP: <strong>{{ $check['php_version'] }}</strong> | Laravel: <strong>{{ $check['laravel'] }}</strong></p>
                    @endif
                    <p class="text-muted small mb-0">{{ $check['message'] ?? __('Service functioning normally.') }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
