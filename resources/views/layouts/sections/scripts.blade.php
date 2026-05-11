<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js', 'resources/assets/vendor/libs/@algolia/autocomplete-js.js'])

@if ($configData['hasCustomizer'])
    @vite('resources/assets/vendor/libs/pickr/pickr.js')
@endif

@vite(['resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/libs/hammer/hammer.js', 'resources/assets/vendor/js/menu.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<!-- app JS -->
@vite(['resources/js/app.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
<!-- END: app JS-->

<script>
  window.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: "{{ session('success') }}",
      timer: 3000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end'
    });
    @endif

    @if (session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: "{{ session('error') }}",
      timer: 3000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end'
    });
    @endif
  });
</script>

@stack('modals')
@livewireScripts
