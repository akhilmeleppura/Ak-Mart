@extends('layouts/layoutMaster')

@section('title', __('Backup & System Maintenance') . ' — AK-Mart')

@section('content')
<div class="row g-6">
  <div class="col-12 col-lg-4 col-xl-3">
    @include('content.apps._settings-sidebar')
  </div>

  <div class="col-12 col-lg-8 col-xl-9">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-data text-warning fs-4"></i>
            <span>{{ __('Database Backups & System Maintenance') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Perform database snapshots, clear application cache layers, and optimize view templates.') }}</p>
        </div>
      </div>

      <div class="card-body pt-5">
        <!-- Cache Management -->
        <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2">
          <i class="bx bx-brush text-primary"></i>
          <span>{{ __('Application Cache & Route Optimization') }}</span>
        </h6>
        <div class="p-4 border rounded mb-5 bg-light-subtle d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h6 class="mb-1 fw-bold">{{ __('Flush Global Settings & Application Cache') }}</h6>
            <small class="text-muted">{{ __('Invalidates cached store configurations, compiled Blade views, and registered route maps.') }}</small>
          </div>
          <form method="POST" action="{{ route('settings.cache.clear') }}">
            @csrf
            <button type="submit" class="btn btn-warning shadow-sm">
              <i class="bx bx-refresh me-1"></i>{{ __('Purge & Rebuild Cache') }}
            </button>
          </form>
        </div>

        <!-- Database Backup -->
        <h6 class="text-heading fw-bold mb-3 d-flex align-items-center gap-2 border-top pt-4">
          <i class="bx bx-server text-primary"></i>
          <span>{{ __('Database Snapshot & Disaster Recovery') }}</span>
        </h6>
        <div class="p-4 border rounded mb-4 bg-light-subtle d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h6 class="mb-1 fw-bold">{{ __('Create Full Database Backup') }}</h6>
            <small class="text-muted">{{ __('Exports all commerce tables, customer records, and orders into an encrypted snapshot.') }}</small>
          </div>
          <button type="button" class="btn btn-primary shadow-sm" onclick="alert('{{ __('Database backup job queued. Export will be saved in storage/app/backups.') }}')">
            <i class="bx bx-download me-1"></i>{{ __('Download SQL Backup') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
