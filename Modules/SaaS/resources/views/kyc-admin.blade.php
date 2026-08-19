@extends('layouts/layoutMaster')

@section('title', __('KYC Applications') . ' — AK-Mart')

@section('content')
<div class="row">
  <div class="col-12">

    {{-- Filter Tabs --}}
    <div class="card mb-4">
      <div class="card-body py-3">
        <div class="d-flex gap-2 flex-wrap">
          @foreach(['pending' => 'warning', 'under_review' => 'info', 'approved' => 'success', 'rejected' => 'danger', 'all' => 'secondary'] as $tab => $color)
          <a href="{{ route('app-saas-kyc-admin') }}?status={{ $tab }}"
            class="btn btn-sm btn-{{ $status === $tab ? $color : 'label-'.$color }}">
            {{ ucwords(str_replace('_', ' ', $tab)) }}
            @if($tab !== 'all' && isset($counts[$tab]))
              <span class="badge bg-{{ $color }} ms-1">{{ $counts[$tab] }}</span>
            @endif
          </a>
          @endforeach
        </div>
      </div>
    </div>

    {{-- KYC List --}}
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('KYC Applications') }} — {{ ucwords(str_replace('_', ' ', $status)) }}</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>{{ __('Store') }}</th>
                <th>{{ __('Business Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Document') }}</th>
                <th>{{ __('Submitted') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($kycs as $kyc)
              <tr>
                <td><strong>{{ $kyc->branch->name ?? 'Store #'.$kyc->branch_id }}</strong></td>
                <td>{{ $kyc->business_name }}</td>
                <td><span class="badge bg-label-secondary">{{ ucwords(str_replace('_', ' ', $kyc->business_type)) }}</span></td>
                <td>{{ ucwords(str_replace('_', ' ', $kyc->document_type)) }}</td>
                <td>{{ $kyc->created_at->format('M d, Y') }}</td>
                <td>
                  @php $colors = ['pending'=>'warning','under_review'=>'info','approved'=>'success','rejected'=>'danger']; @endphp
                  <span class="badge bg-label-{{ $colors[$kyc->status] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $kyc->status)) }}</span>
                </td>
                <td>
                  <a href="{{ route('app-saas-kyc-show', $kyc->id) }}" class="btn btn-sm btn-icon btn-label-primary" title="{{ __('Review') }}">
                    <i class="bx bx-search"></i>
                  </a>
                  @if($kyc->status === 'pending')
                  <button class="btn btn-sm btn-icon btn-label-info btn-mark-review" data-id="{{ $kyc->id }}" title="{{ __('Mark Under Review') }}">
                    <i class="bx bx-time"></i>
                  </button>
                  @endif
                  @if(in_array($kyc->status, ['pending', 'under_review']))
                  <button class="btn btn-sm btn-icon btn-label-success btn-approve" data-id="{{ $kyc->id }}" title="{{ __('Approve') }}">
                    <i class="bx bx-check"></i>
                  </button>
                  <button class="btn btn-sm btn-icon btn-label-danger btn-reject" data-id="{{ $kyc->id }}" title="{{ __('Reject') }}">
                    <i class="bx bx-x"></i>
                  </button>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-6">{{ __('No KYC applications with status') }} "{{ $status }}".</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($kycs->hasPages())
        <div class="p-4">{{ $kycs->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Reject Reason Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Reject KYC — Provide Reason') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
        <textarea class="form-control" id="rejectReason" rows="4" placeholder="{{ __('Explain why the documents are being rejected...') }}"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-danger" id="confirmRejectBtn">{{ __('Confirm Rejection') }}</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrf = '{{ csrf_token() }}';
  let rejectTargetId = null;

  function apiCall(url, method = 'POST', body = {}) {
    return fetch(url, {
      method,
      headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(body)
    }).then(r => r.json());
  }

  // Mark Under Review
  document.querySelectorAll('.btn-mark-review').forEach(btn => {
    btn.addEventListener('click', function () {
      apiCall(`/app/saas/kyc/${this.dataset.id}/review`)
        .then(d => {
          if (d.success) location.reload();
          else if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', text: d.message });
          else alert(d.message);
        });
    });
  });

  // Approve
  document.querySelectorAll('.btn-approve').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      const doApprove = () => {
        apiCall(`/app/saas/kyc/${id}/approve`)
          .then(d => {
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: d.success ? 'success' : 'error',
                text: d.message,
                timer: 1500,
                showConfirmButton: false
              }).then(() => { if (d.success) location.reload(); });
            } else {
              alert(d.message);
              if (d.success) location.reload();
            }
          });
      };

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: @json(__('Approve KYC Application?')),
          text: @json(__('This will verify the vendor and unlock merchant payouts.')),
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: @json(__('Yes, approve')),
          cancelButtonText: @json(__('Cancel')),
          customClass: { confirmButton: 'btn btn-success me-3', cancelButton: 'btn btn-label-secondary' },
          buttonsStyling: false
        }).then(res => { if (res.isConfirmed) doApprove(); });
      } else {
        if (confirm('Approve this KYC? This will unlock vendor payouts.')) doApprove();
      }
    });
  });

  // Reject - open modal
  document.querySelectorAll('.btn-reject').forEach(btn => {
    btn.addEventListener('click', function () {
      rejectTargetId = this.dataset.id;
      document.getElementById('rejectReason').value = '';
      new bootstrap.Modal(document.getElementById('rejectModal')).show();
    });
  });

  // Confirm Rejection
  document.getElementById('confirmRejectBtn').addEventListener('click', function () {
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason || reason.length < 10) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', text: @json(__('Please provide a meaningful rejection reason (min 10 chars).')) });
      } else {
        alert('Please provide a meaningful rejection reason (min 10 chars).');
      }
      return;
    }
    apiCall(`/app/saas/kyc/${rejectTargetId}/reject`, 'POST', { reason })
      .then(d => {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: d.success ? 'success' : 'error',
            text: d.message,
            timer: 1500,
            showConfirmButton: false
          }).then(() => { if (d.success) location.reload(); });
        } else {
          alert(d.message);
          if (d.success) location.reload();
        }
      });
  });
});
</script>
@endsection
