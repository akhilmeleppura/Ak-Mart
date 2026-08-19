@extends('layouts/layoutMaster')

@section('title', __('KYC Application Details') . ' — AK-Mart')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1">{{ __('KYC Review') }} — {{ $vendorKyc->business_name }}</h4>
        <p class="mb-0 text-muted">{{ __('Submitted by') }} {{ $vendorKyc->branch->name ?? 'Branch #' . $vendorKyc->branch_id }} &bull; {{ $vendorKyc->created_at->format('M d, Y H:i') }}</p>
      </div>
      <a href="{{ route('app-saas-kyc-admin') }}" class="btn btn-label-secondary">
        <i class="bx bx-arrow-back me-1"></i> {{ __('Back to KYC List') }}
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="row g-4">
      {{-- Business Details --}}
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0"><i class="bx bx-building me-2 text-primary"></i>{{ __('Business Information') }}</h5>
          </div>
          <div class="card-body pt-4">
            <table class="table table-borderless">
              <tr>
                <td class="fw-medium text-heading ps-0" style="width: 35%;">{{ __('Business Name') }}:</td>
                <td>{{ $vendorKyc->business_name }}</td>
              </tr>
              <tr>
                <td class="fw-medium text-heading ps-0">{{ __('Business Type') }}:</td>
                <td><span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $vendorKyc->business_type)) }}</span></td>
              </tr>
              <tr>
                <td class="fw-medium text-heading ps-0">{{ __('Tax / Reg ID') }}:</td>
                <td><code>{{ $vendorKyc->tax_id ?? $vendorKyc->registration_number ?? '—' }}</code></td>
              </tr>
              <tr>
                <td class="fw-medium text-heading ps-0">{{ __('Store Branch') }}:</td>
                <td>{{ $vendorKyc->branch->name ?? 'Store #' . $vendorKyc->branch_id }}</td>
              </tr>
              <tr>
                <td class="fw-medium text-heading ps-0">{{ __('Current Status') }}:</td>
                <td>
                  @php $colors = ['pending'=>'warning','under_review'=>'info','approved'=>'success','rejected'=>'danger']; @endphp
                  <span class="badge bg-{{ $colors[$vendorKyc->status] ?? 'secondary' }} fs-6">{{ ucwords(str_replace('_', ' ', $vendorKyc->status)) }}</span>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Document & Verification Section --}}
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0"><i class="bx bx-file me-2 text-info"></i>{{ __('Identity & Verification Document') }}</h5>
          </div>
          <div class="card-body pt-4">
            <p><strong>{{ __('Document Type') }}:</strong> {{ ucwords(str_replace('_', ' ', $vendorKyc->document_type)) }}</p>
            @if($vendorKyc->document_path)
              <div class="border rounded p-3 text-center mb-4 bg-lighter">
                <i class="bx bx-file-blank fs-1 text-primary mb-2"></i>
                <p class="mb-2">{{ basename($vendorKyc->document_path) }}</p>
                <a href="{{ Storage::url($vendorKyc->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="bx bx-download me-1"></i> {{ __('View / Download Document') }}
                </a>
              </div>
            @else
              <div class="alert alert-warning">{{ __('No physical document uploaded.') }}</div>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 justify-content-end pt-3 border-top">
              @if($vendorKyc->status !== 'approved')
                <form action="{{ route('app-saas-kyc-approve', $vendorKyc->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-success">
                    <i class="bx bx-check me-1"></i> {{ __('Approve KYC') }}
                  </button>
                </form>
              @endif
              @if($vendorKyc->status !== 'rejected')
                <form action="{{ route('app-saas-kyc-reject', $vendorKyc->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-danger">
                    <i class="bx bx-x me-1"></i> {{ __('Reject KYC') }}
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
