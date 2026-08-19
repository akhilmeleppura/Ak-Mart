/**
 * AK-Mart — Searchable Select Initializer
 *
 * Finds all elements with [data-searchable-select="true"] and initializes
 * Select2 with appropriate configuration (AJAX or static, single/multi).
 *
 * Usage (Blade):
 *   <x-searchable-select name="product_id" ajax-url="/api/select/products" />
 *
 * Usage (manual HTML):
 *   <select data-searchable-select="true" data-ajax-url="/api/select/products" ...>
 *
 * Select2 is loaded via the global window (assumed to be available from build).
 */

(function ($) {
    'use strict';

    // Templates
    const defaultResultTemplate = (data) => {
        if (data.loading) return $('<span>').text('Searching...');
        const $item = $('<span>').text(data.text);
        if (data.sku)   $item.append($('<small>').css({ display: 'block', color: '#888', fontSize: '11px' }).text(`SKU: ${data.sku}  ·  ₹${data.price ?? '—'}  ·  Stock: ${data.stock ?? 0}`));
        if (data.email) $item.append($('<small>').css({ display: 'block', color: '#888', fontSize: '11px' }).text(`${data.email}  ·  ${data.phone ?? ''}`));
        if (data.code)  $item.append($('<small>').css({ display: 'block', color: '#888', fontSize: '11px' }).text(`${data.code}  ·  ${data.city ?? ''}`));
        return $item;
    };

    /**
     * Build the Select2 AJAX config for a given URL.
     */
    function buildAjaxConfig(url, minLength) {
        return {
            url: url,
            dataType: 'json',
            delay: 300,
            data: (params) => ({
                q:    params.term || '',
                page: params.page || 1,
            }),
            processResults: (data, params) => ({
                results:    data.results || [],
                pagination: data.pagination || { more: false },
            }),
            cache: true,
            error: (xhr) => {
                console.warn('[AK-Mart SearchSelect] AJAX error:', xhr.status);
            },
        };
    }

    /**
     * Initialize a single Select2 element.
     */
    function initSelect2(el) {
        const $el           = $(el);
        const ajaxUrl       = $el.data('ajax-url') || null;
        const minLength     = parseInt($el.data('min-length') || 2, 10);
        const placeholder   = $el.data('placeholder') || 'Search or select...';
        const allowClear    = $el.data('allow-clear') !== 'false';
        const dropdownParent= $el.data('dropdown-parent') || null;
        const templateName  = $el.data('template-result') || null;

        // Use custom template function if provided as global window function name
        const resultTemplate = (templateName && typeof window[templateName] === 'function')
            ? window[templateName]
            : defaultResultTemplate;

        const opts = {
            theme:               'bootstrap-5',
            width:               '100%',
            placeholder:         placeholder,
            allowClear:          allowClear,
            minimumInputLength:  ajaxUrl ? minLength : 0,
            language: {
                inputTooShort: () => `Type at least ${minLength} character${minLength !== 1 ? 's' : ''} to search`,
                noResults:     () => 'No results found',
                searching:     () => 'Searching...',
                loadingMore:   () => 'Loading more results...',
            },
            templateResult:    resultTemplate,
            templateSelection: (data) => data.text || data.id,
            // CSRF token in request headers
            ajax: ajaxUrl ? buildAjaxConfig(ajaxUrl, minLength) : undefined,
        };

        if (dropdownParent) {
            opts.dropdownParent = $(dropdownParent);
        }

        // RTL support
        if (document.documentElement.dir === 'rtl' || $el.closest('[dir="rtl"]').length) {
            opts.dir = 'rtl';
        }

        $el.select2(opts);

        // Accessibility: focus original input when Select2 opens
        $el.on('select2:open', () => {
            const searchInput = document.querySelector('.select2-search__field');
            if (searchInput) searchInput.focus();
        });

        // Clear validation errors on change
        $el.on('change', () => {
            $el.closest('.form-group, .mb-3').find('.invalid-feedback').hide();
            $el.removeClass('is-invalid');
        });
    }

    /**
     * Initialize all searchable selects on the page.
     */
    function initAll(context) {
        const $context = context ? $(context) : $(document);
        $context.find('[data-searchable-select="true"]').each(function () {
            if (! $(this).hasClass('select2-hidden-accessible')) {
                initSelect2(this);
            }
        });
    }

    // Auto-init on DOM ready
    $(document).ready(() => initAll());

    // Re-init when new content is loaded (modals, turbolinks, etc.)
    $(document).on('shown.bs.modal', '.modal', function () {
        initAll(this);
    });

    // Expose for manual invocation
    window.AkMartSelect = {
        init:    initAll,
        initEl:  initSelect2,
    };

})(window.jQuery || window.$);
