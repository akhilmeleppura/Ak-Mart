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
<script src="{{ asset('assets/js/ak-notifications.js') }}"></script>
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
    if (window.AKNotify) {
      window.AKNotify.success("{{ session('success') }}");
    }
    @endif

    @if (session('error'))
    if (window.AKNotify) {
      window.AKNotify.error("{{ session('error') }}");
    }
    @endif

    @if (session('warning'))
    if (window.AKNotify) {
      window.AKNotify.warning("{{ session('warning') }}");
    }
    @endif

    @if (session('info'))
    if (window.AKNotify) {
      window.AKNotify.info("{{ session('info') }}");
    }
    @endif

    // Automatic window.alert bridge to AKNotify
    window.alert = function(msg) {
      if (window.AKNotify) {
        window.AKNotify.info(msg);
      }
    };

    // Global AKNotify Form & Delete Interceptor
    document.addEventListener('submit', function(e) {
      const form = e.target;
      if (form.dataset.akConfirmed === 'true') {
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
        msg = window.__('Are you sure you want to delete this item? This action cannot be undone.') || 'Are you sure you want to delete this item? This action cannot be undone.';
      }

      if (msg && window.AKNotify) {
        e.preventDefault();
        e.stopImmediatePropagation();

        window.AKNotify.confirm({
          title: window.__('Are you sure?') || 'Are you sure?',
          message: msg,
          type: isDelete ? 'danger' : 'warning',
          confirmText: window.__('Yes, proceed!') || 'Yes, proceed!',
          cancelText: window.__('Cancel') || 'Cancel'
        }).then(function(res) {
          if (res.isConfirmed) {
            form.dataset.akConfirmed = 'true';
            form.removeAttribute('onsubmit');
            form.submit();
          }
        });
        return false;
      }
    }, true);

    // Global AKNotify Button & Link Click Interceptor
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('button, a');
      if (!btn) return;

      const onclickAttr = btn.getAttribute('onclick');
      const dataConfirm = btn.getAttribute('data-confirm');

      if ((onclickAttr && onclickAttr.includes('confirm(')) || dataConfirm) {
        if (btn.dataset.akConfirmed === 'true') return true;

        let msg = dataConfirm;
        if (!msg && onclickAttr) {
          const m = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
          if (m && m[1]) msg = m[1];
        }

        if (msg && window.AKNotify) {
          e.preventDefault();
          e.stopImmediatePropagation();

          window.AKNotify.confirm({
            title: window.__('Confirmation') || 'Confirmation',
            message: msg,
            type: 'info',
            confirmText: window.__('Yes, proceed!') || 'Yes, proceed!',
            cancelText: window.__('Cancel') || 'Cancel'
          }).then(function(res) {
            if (res.isConfirmed) {
              btn.dataset.akConfirmed = 'true';
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
