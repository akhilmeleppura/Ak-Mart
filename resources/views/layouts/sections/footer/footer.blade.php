@php
$containerFooter =
isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
? 'container-xxl'
: 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
      <div class="mb-2 mb-md-0 small text-muted">
        &#169; <script>document.write(new Date().getFullYear())</script> 
        <span class="fw-semibold text-heading">AK-Mart</span> — Smart Mini-Mart & E-Commerce Management Platform.
      </div>
      <div class="d-none d-lg-inline-block small">
        <a href="{{ route('app-ecommerce-dashboard') }}" class="footer-link me-4">Dashboard</a>
        <a href="{{ route('app-vendor-pos') }}" class="footer-link me-4">POS Terminal</a>
        <a href="{{ route('app-reports') }}" class="footer-link me-4">Reports</a>
        <a href="{{ route('app-access-hub') }}" class="footer-link me-4">Access Hub</a>
        <a href="mailto:support@ak-mart.com" class="footer-link">Support</a>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->