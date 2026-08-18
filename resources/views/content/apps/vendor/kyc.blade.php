@extends('layouts/layoutMaster')

@section('title', 'KYC Verification')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-9">

    {{-- Status Banner --}}
    @if($kyc)
      @php
        $banners = [
          'pending'      => ['warning', 'bx-time', 'KYC Submitted', 'Your application is in the queue. We\'ll notify you once reviewed (2-3 business days).'],
          'under_review' => ['info',    'bx-search', 'Under Review', 'Our compliance team is actively reviewing your documents.'],
          'approved'     => ['success', 'bx-check-circle', 'KYC Approved!', 'Your identity is verified. You can now request payouts from your wallet.'],
          'rejected'     => ['danger',  'bx-x-circle', 'KYC Rejected', 'Reason: ' . ($kyc->rejection_reason ?? 'Not provided. Please resubmit.')],
        ];
        $b = $banners[$kyc->status];
      @endphp
      <div class="alert alert-{{ $b[0] }} d-flex align-items-center mb-6">
        <i class="bx {{ $b[1] }} bx-md me-3"></i>
        <div>
          <strong>{{ $b[2] }}</strong><br>
          <span>{{ $b[3] }}</span>
          @if($kyc->reviewed_at)
            <br><small class="text-muted">Reviewed on: {{ $kyc->reviewed_at->format('M d, Y') }}</small>
          @endif
        </div>
      </div>
    @endif

    {{-- Locked or Approved: show info only --}}
    @if($kyc && $kyc->status === 'approved')
      <div class="card">
        <div class="card-body text-center py-8">
          <i class="bx bx-shield-check bx-lg text-success mb-3"></i>
          <h4>Verified Business</h4>
          <p class="mb-1"><strong>{{ $kyc->business_name }}</strong> ({{ ucwords(str_replace('_', ' ', $kyc->business_type)) }})</p>
          <p class="text-muted">Your account is verified. All payout features are active.</p>
        </div>
      </div>

    @elseif($kyc && in_array($kyc->status, ['pending', 'under_review']))
      <div class="card">
        <div class="card-body text-center py-8">
          <i class="bx bx-loader-circle bx-lg text-warning mb-3 bx-spin"></i>
          <h4>Application Under Review</h4>
          <p class="text-muted">No action needed. We'll email you when the review is complete.</p>
        </div>
      </div>

    @else
      {{-- KYC Form (new submission or resubmit after rejection) --}}
      <div class="card">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">{{ $kyc ? 'Resubmit KYC Application' : 'KYC Verification' }}</h5>
          <p class="text-muted small mb-0 mt-1">Complete this form to unlock vendor payouts. All documents are encrypted and stored securely.</p>
        </div>
        <div class="card-body">
          <form id="kycForm" enctype="multipart/form-data">
            @csrf

            {{-- Section 1: Business Details --}}
            <h6 class="text-muted fw-semibold text-uppercase mb-4 mt-2">Business Information</h6>
            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label">Business Name <span class="text-danger">*</span></label>
                <input type="text" name="business_name" class="form-control" placeholder="Your Registered Business Name" value="{{ $kyc->business_name ?? '' }}" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Business Type <span class="text-danger">*</span></label>
                <select name="business_type" class="form-select" required>
                  <option value="">Select Type</option>
                  <option value="sole_proprietor" {{ ($kyc->business_type ?? '') === 'sole_proprietor' ? 'selected' : '' }}>Sole Proprietor</option>
                  <option value="llc" {{ ($kyc->business_type ?? '') === 'llc' ? 'selected' : '' }}>LLC</option>
                  <option value="partnership" {{ ($kyc->business_type ?? '') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                  <option value="company" {{ ($kyc->business_type ?? '') === 'company' ? 'selected' : '' }}>Company</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">PAN Number</label>
                <input type="text" name="pan_number" class="form-control" placeholder="ABCDE1234F" value="{{ $kyc->pan_number ?? '' }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control" placeholder="22AAAAA0000A1Z5" value="{{ $kyc->gst_number ?? '' }}">
              </div>
            </div>

            <hr class="my-5">

            {{-- Section 2: Bank Details --}}
            <h6 class="text-muted fw-semibold text-uppercase mb-4">Bank Account Details</h6>
            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" placeholder="State Bank of India" value="{{ $kyc->bank_name ?? '' }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">IFSC Code</label>
                <input type="text" name="bank_ifsc_code" class="form-control" placeholder="SBIN0001234" value="{{ $kyc->bank_ifsc_code ?? '' }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Account Number</label>
                <input type="text" name="bank_account_number" class="form-control" placeholder="Account Number" value="{{ $kyc->bank_account_number ?? '' }}">
              </div>
            </div>

            <hr class="my-5">

            {{-- Section 3: Identity Documents --}}
            <h6 class="text-muted fw-semibold text-uppercase mb-4">Identity Verification</h6>
            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                <select name="document_type" class="form-select" required>
                  <option value="">Select Document</option>
                  <option value="aadhar">Aadhar Card</option>
                  <option value="passport">Passport</option>
                  <option value="driving_license">Driving License</option>
                  <option value="voter_id">Voter ID</option>
                </select>
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-md-4 mb-3">
                <label class="form-label">Document Front <span class="text-danger">*</span></label>
                <input type="file" name="document_front" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="form-text">JPG, PNG or PDF. Max 4MB.</div>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Document Back</label>
                <input type="file" name="document_back" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Selfie with Document</label>
                <input type="file" name="selfie" class="form-control" accept=".jpg,.jpeg,.png">
              </div>
            </div>

            <div class="d-flex justify-content-end mt-6">
              <button type="submit" class="btn btn-primary btn-lg px-6" id="kycSubmitBtn">
                <i class="bx bx-upload me-2"></i> Submit for Verification
              </button>
            </div>
          </form>
        </div>
      </div>
    @endif

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('kycForm');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('kycSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';

    const formData = new FormData(this);
    fetch('{{ route("app-vendor-kyc-store") }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'KYC Submitted',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => location.reload());
        } else {
          alert(data.message);
          location.reload();
        }
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Submission Failed',
            text: data.message || 'Please check your submitted details.'
          });
        } else {
          alert('Error: ' + data.message);
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-upload me-2"></i> Submit for Verification';
      }
    });
  });
});
</script>
@endsection
