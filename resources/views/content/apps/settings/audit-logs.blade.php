@extends('layouts/layoutMaster')

@section('title', __('Settings Audit Trail') . ' — AK-Mart')

@section('content')
<div class="row g-6">
  <div class="col-12 col-lg-4 col-xl-3">
    @include('content.apps._settings-sidebar')
  </div>

  <div class="col-12 col-lg-8 col-xl-9">
    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-history text-info fs-4"></i>
            <span>{{ __('Settings Change & Security Audit Trail') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Immutable log of all store configuration modifications, administrator actions, and security events.') }}</p>
        </div>
        <span class="badge bg-label-primary">{{ count($auditLogs) }} {{ __('Recent Logs') }}</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Action / Event') }}</th>
              <th>{{ __('User') }}</th>
              <th>{{ __('IP Address') }}</th>
              <th>{{ __('Timestamp') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($auditLogs as $log)
            <tr>
              <td>
                <span class="badge bg-label-primary">{{ $log->event }}</span>
              </td>
              <td>
                <span class="fw-semibold text-heading">{{ $log->user ? $log->user->name : 'System / Auto' }}</span>
                <small class="d-block text-muted">{{ $log->user ? $log->user->email : '' }}</small>
              </td>
              <td>
                <code>{{ $log->ip_address ?: '127.0.0.1' }}</code>
              </td>
              <td>
                <small class="text-muted">{{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : '—' }}</small>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center py-5 text-muted">
                <i class="bx bx-history fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-0">{{ __('No audit log records found.') }}</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
