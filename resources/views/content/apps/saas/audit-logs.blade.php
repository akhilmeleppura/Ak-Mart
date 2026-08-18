@extends('layouts/layoutMaster')

@section('title', __('Audit Logs') . ' — AK-Mart')

@section('content')
<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('System Audit Logs') }}</h5>
        <p class="text-muted small mb-0">{{ __('Tracking all administrative and sensitive actions across the platform.') }}</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Timestamp') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('Resource') }}</th>
                        <th>{{ __('IP Address') }}</th>
                        <th>{{ __('Metadata') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><small class="text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</small></td>
                            <td>
                                @if($log->user)
                                    <span class="fw-semibold">{{ $log->user->name }}</span><br>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                @else
                                    <span class="text-muted">{{ __('System / Guest') }}</span>
                                @endif
                            </td>
                            <td>
                                @php $color = \Illuminate\Support\Str::contains($log->event, 'deleted') ? 'danger' : (\Illuminate\Support\Str::contains($log->event, 'login') ? 'success' : 'primary'); @endphp
                                <span class="badge bg-label-{{ $color }}">{{ strtoupper(str_replace('_', ' ', $log->event)) }}</span>
                            </td>
                            <td>
                                @if($log->auditable_type)
                                    <small>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td><code class="small">{{ $log->ip_address }}</code></td>
                            <td>
                                @if($log->new_values)
                                    <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}">{{ __('Details') }}</button>
                                @endif
                            </td>
                        </tr>
                        @if($log->new_values)
                        <tr class="collapse" id="logDetails{{ $log->id }}">
                            <td colspan="6" class="bg-light p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="small fw-bold">{{ __('Changes:') }}</h6>
                                        <pre class="small mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="small fw-bold">{{ __('User Agent:') }}</h6>
                                        <small class="text-muted">{{ $log->user_agent }}</small>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-muted">{{ __('No audit logs found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
