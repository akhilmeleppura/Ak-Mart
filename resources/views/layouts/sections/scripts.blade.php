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
  window.AK_I18N = {
    locale: "{{ app()->getLocale() }}",
    dir: "{{ in_array(app()->getLocale(), ['ar', 'fa', 'ur', 'he']) ? 'rtl' : 'ltr' }}",
    translations: @json(file_exists(base_path('lang/' . app()->getLocale() . '.json')) ? json_decode(file_get_contents(base_path('lang/' . app()->getLocale() . '.json')), true) : [])
  };

  window.__ = function(key, replace = {}) {
    let translation = (window.AK_I18N.translations && window.AK_I18N.translations[key]) ? window.AK_I18N.translations[key] : key;
    for (const [placeholder, value] of Object.entries(replace)) {
      translation = translation.replace(':' + placeholder, value);
    }
    return translation;
  };

  window.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: window.__('Success') || 'Success',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
    @endif

    @if (session('error'))
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'error',
        title: window.__('Error') || 'Error',
        text: "{{ session('error') }}",
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
    @endif

    // Automatic window.alert bridge to SweetAlert2
    window.alert = function(msg) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'info',
          title: 'Notification',
          text: msg,
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
      }
    };

    // Global SweetAlert2 Form & Delete Interceptor
    document.addEventListener('submit', function(e) {
      const form = e.target;
      if (form.dataset.swalConfirmed === 'true') {
        return true;
      }

      const onsubmitAttr = form.getAttribute('onsubmit');
      const dataConfirm = form.getAttribute('data-confirm');
      const isDelete = form.action && (form.action.includes('destroy') || form.action.includes('delete') || form.action.includes('discard'));

      let msg = null;
      if (dataConfirm) {
        msg = dataConfirm;
      } else if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
        const m = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
        if (m && m[1]) msg = m[1];
      } else if (isDelete) {
        msg = 'Are you sure you want to delete this item? This action cannot be undone.';
      }

      if (msg && typeof Swal !== 'undefined') {
        e.preventDefault();
        e.stopImmediatePropagation();

        Swal.fire({
          title: 'Are you sure?',
          text: msg,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'Cancel',
          customClass: {
            confirmButton: 'btn btn-danger me-3',
            cancelButton: 'btn btn-label-secondary'
          },
          buttonsStyling: false
        }).then(function(res) {
          if (res.isConfirmed) {
            form.dataset.swalConfirmed = 'true';
            form.removeAttribute('onsubmit');
            form.submit();
          }
        });
        return false;
      }
    }, true);

    // Global SweetAlert2 Button & Link Click Interceptor
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('button, a');
      if (!btn) return;

      const onclickAttr = btn.getAttribute('onclick');
      const dataConfirm = btn.getAttribute('data-confirm');

      if ((onclickAttr && onclickAttr.includes('confirm(')) || dataConfirm) {
        if (btn.dataset.swalConfirmed === 'true') return true;

        let msg = dataConfirm;
        if (!msg && onclickAttr) {
          const m = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
          if (m && m[1]) msg = m[1];
        }

        if (msg && typeof Swal !== 'undefined') {
          e.preventDefault();
          e.stopImmediatePropagation();

          Swal.fire({
            title: 'Confirmation',
            text: msg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel',
            customClass: {
              confirmButton: 'btn btn-primary me-3',
              cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
          }).then(function(res) {
            if (res.isConfirmed) {
              btn.dataset.swalConfirmed = 'true';
              btn.removeAttribute('onclick');
              if (btn.tagName === 'BUTTON' && btn.type === 'submit' && btn.form) {
                btn.form.submit();
              } else if (btn.tagName === 'A' && btn.href && !btn.href.startsWith('javascript:')) {
                window.location.href = btn.href;
              } else {
                btn.click();
              }
            }
          });
          return false;
        }
      }
    }, true);
  });
</script>

@stack('modals')
@livewireScripts
