@php
$currentPath = trim(request()->path(), '/');

$categories = [
    'Core Store' => [
        [
            'key'      => 'store',
            'title'    => 'Store Details',
            'subtitle' => 'Identity, address & contact',
            'url'      => url('settings/store'),
            'match'    => ['settings/store', 'app/ecommerce/settings/details', 'settings'],
            'icon'     => 'bx bx-store-alt',
            'color'    => 'primary',
            'keywords' => 'name logo email phone address vat gst legal currency timezone footer'
        ],
        [
            'key'      => 'general',
            'title'    => 'General Operations',
            'subtitle' => 'Maintenance & feature toggles',
            'url'      => url('settings/general'),
            'match'    => ['settings/general'],
            'icon'     => 'bx bx-slider',
            'color'    => 'secondary',
            'keywords' => 'maintenance guest checkout reviews wishlist coupons refunds enable disable'
        ],
        [
            'key'      => 'branding',
            'title'    => 'Logos & Branding',
            'subtitle' => 'App icons, logo & colors',
            'url'      => url('settings/branding'),
            'match'    => ['settings/branding', 'app/ecommerce/settings/branding'],
            'icon'     => 'bx bx-palette',
            'color'    => 'info',
            'keywords' => 'logo dark logo favicon invoice logo primary color brand appearance'
        ],
        [
            'key'      => 'localization',
            'title'    => 'Localization & RTL',
            'subtitle' => 'Languages, RTL & formats',
            'url'      => url('settings/localization'),
            'match'    => ['settings/localization'],
            'icon'     => 'bx bx-globe',
            'color'    => 'success',
            'keywords' => 'language english malayalam hindi arabic french german rtl date format'
        ],
    ],
    'E-Commerce & Operations' => [
        [
            'key'      => 'ecommerce',
            'title'    => 'E-Commerce & Catalog',
            'subtitle' => 'Catalog, cart & reviews',
            'url'      => url('settings/ecommerce'),
            'match'    => ['settings/ecommerce'],
            'icon'     => 'bx bx-shopping-bag',
            'color'    => 'primary',
            'keywords' => 'catalog sorting pagination cart expiration min order compare reviews backorders'
        ],
        [
            'key'      => 'products',
            'title'    => 'Products & AI Tools',
            'subtitle' => 'Auto SKU, barcode & smart tools',
            'url'      => url('settings/products'),
            'match'    => ['settings/products'],
            'icon'     => 'bx bx-box',
            'color'    => 'warning',
            'keywords' => 'sku barcode auto generate image upload smart product amazon url ai generator'
        ],
        [
            'key'      => 'inventory',
            'title'    => 'Inventory Management',
            'subtitle' => 'Stock tracking & low-stock alerts',
            'url'      => url('settings/inventory'),
            'match'    => ['settings/inventory'],
            'icon'     => 'bx bx-layer',
            'color'    => 'danger',
            'keywords' => 'inventory stock tracking low stock threshold critical threshold auto deduct reservation'
        ],
        [
            'key'      => 'orders',
            'title'    => 'Order Settings',
            'subtitle' => 'Prefix, status & return rules',
            'url'      => url('settings/orders'),
            'match'    => ['settings/orders'],
            'icon'     => 'bx bx-receipt',
            'color'    => 'info',
            'keywords' => 'order prefix format invoice auto confirm cancellation return window refund'
        ],
        [
            'key'      => 'checkout',
            'title'    => 'Checkout & Policies',
            'subtitle' => 'Guest checkout & policy terms',
            'url'      => url('settings/checkout'),
            'match'    => ['settings/checkout', 'app/ecommerce/settings/checkout'],
            'icon'     => 'bx bx-cart-alt',
            'color'    => 'primary',
            'keywords' => 'guest checkout phone address required terms privacy refund order notes'
        ],
        [
            'key'      => 'customers',
            'title'    => 'Customers & Loyalty',
            'subtitle' => 'Registration & reward points',
            'url'      => url('settings/customers'),
            'match'    => ['settings/customers'],
            'icon'     => 'bx bx-user-check',
            'color'    => 'success',
            'keywords' => 'customer registration verification approval loyalty points redeem reward'
        ],
        [
            'key'      => 'pricing',
            'title'    => 'Pricing & Tax (GST)',
            'subtitle' => 'Currency, display & GST/VAT',
            'url'      => url('settings/pricing'),
            'match'    => ['settings/pricing'],
            'icon'     => 'bx bx-dollar-circle',
            'color'    => 'warning',
            'keywords' => 'pricing currency position decimals tax included gst rate vat rate hsn'
        ],
        [
            'key'      => 'payments',
            'title'    => 'Payment Gateways',
            'subtitle' => 'Gateways, Stripe & COD',
            'url'      => url('settings/payments'),
            'match'    => ['settings/payments', 'app/ecommerce/settings/payments'],
            'icon'     => 'bx bx-credit-card',
            'color'    => 'success',
            'keywords' => 'payments stripe paypal cod phonepe upi bank transfer credentials sandbox'
        ],
        [
            'key'      => 'shipping',
            'title'    => 'Shipping & Delivery',
            'subtitle' => 'Rates, zones & fulfillment',
            'url'      => url('settings/shipping'),
            'match'    => ['settings/shipping', 'app/ecommerce/settings/shipping'],
            'icon'     => 'bx bx-package',
            'color'    => 'warning',
            'keywords' => 'shipping delivery free shipping threshold flat rate weight distance'
        ],
        [
            'key'      => 'locations',
            'title'    => 'Branches & Warehouses',
            'subtitle' => 'Multi-branch & order routing',
            'url'      => url('settings/locations'),
            'match'    => ['settings/locations', 'app/ecommerce/settings/locations'],
            'icon'     => 'bx bx-map-pin',
            'color'    => 'danger',
            'keywords' => 'branch warehouse locations default branch stock availability routing'
        ],
    ],
    'Communications & Reminders' => [
        [
            'key'      => 'email',
            'title'    => 'Email & SMTP Hub',
            'subtitle' => 'Mail servers & test connection',
            'url'      => url('settings/email'),
            'match'    => ['settings/email'],
            'icon'     => 'bx bx-envelope',
            'color'    => 'primary',
            'keywords' => 'email smtp host port username password encryption test email mail driver'
        ],
        [
            'key'      => 'email-templates',
            'title'    => 'Email Templates',
            'subtitle' => 'Transactional templates & tags',
            'url'      => url('settings/email-templates'),
            'match'    => ['settings/email-templates'],
            'icon'     => 'bx bx-layout',
            'color'    => 'info',
            'keywords' => 'templates welcome order confirmation shipped invoice variables customer_name'
        ],
        [
            'key'      => 'email-reminders',
            'title'    => 'Email Reminders & Scheduler',
            'subtitle' => 'Unpaid order & cart reminders',
            'url'      => url('settings/email-reminders'),
            'match'    => ['settings/email-reminders'],
            'icon'     => 'bx bx-time-five',
            'color'    => 'warning',
            'keywords' => 'reminders unpaid order abandoned cart pending payment retry cooldown scheduler'
        ],
        [
            'key'      => 'whatsapp',
            'title'    => 'WhatsApp Management',
            'subtitle' => 'Cloud API, templates & logs',
            'url'      => url('settings/whatsapp'),
            'match'    => ['settings/whatsapp'],
            'icon'     => 'bx bxl-whatsapp',
            'color'    => 'success',
            'keywords' => 'whatsapp cloud api meta phone id access token template automation logs test'
        ],
        [
            'key'      => 'notifications',
            'title'    => 'Notification Matrix',
            'subtitle' => 'Channel-level event toggles',
            'url'      => url('settings/notifications'),
            'match'    => ['settings/notifications', 'app/ecommerce/settings/notifications'],
            'icon'     => 'bx bx-bell',
            'color'    => 'info',
            'keywords' => 'notifications in-app email whatsapp orders payments inventory security alerts'
        ],
    ],
    'Automation & Intelligence' => [
        [
            'key'      => 'automation',
            'title'    => 'Workflow Automation',
            'subtitle' => 'Event-driven triggers & actions',
            'url'      => url('settings/automation'),
            'match'    => ['settings/automation'],
            'icon'     => 'bx bx-git-repo-forked',
            'color'    => 'primary',
            'keywords' => 'automation workflow triggers events order created stock low payment failed'
        ],
        [
            'key'      => 'ai',
            'title'    => 'AI & Copilot',
            'subtitle' => 'Gemini API & smart tools',
            'url'      => url('settings/ai'),
            'match'    => ['settings/ai', 'app/ecommerce/settings/ai', 'apps/ai-copilot'],
            'icon'     => 'bx bx-bot',
            'color'    => 'primary',
            'keywords' => 'ai copilot gemini api key model temperature prompt assistant product generation'
        ],
        [
            'key'      => 'seo',
            'title'    => 'SEO & Marketing',
            'subtitle' => 'Meta tags, sitemap & analytics',
            'url'      => url('settings/seo'),
            'match'    => ['settings/seo'],
            'icon'     => 'bx bx-search-alt',
            'color'    => 'info',
            'keywords' => 'seo meta title description keywords open graph sitemap analytics pixel utm'
        ],
        [
            'key'      => 'integrations',
            'title'    => 'Integrations & Maps',
            'subtitle' => 'Third-party connected apps',
            'url'      => url('settings/integrations'),
            'match'    => ['settings/integrations', 'settings/maps', 'app/ecommerce/settings/maps'],
            'icon'     => 'bx bx-grid-alt',
            'color'    => 'dark',
            'keywords' => 'integrations google maps aws s3 storage stripe paypal twilio cloud'
        ],
    ],
    'Security & Control' => [
        [
            'key'      => 'security',
            'title'    => 'Security Center',
            'subtitle' => 'Password policy & sessions',
            'url'      => url('settings/security'),
            'match'    => ['settings/security'],
            'icon'     => 'bx bx-shield-quarter',
            'color'    => 'danger',
            'keywords' => 'security password policy lockout session timeout audit failed logins'
        ],
        [
            'key'      => 'users-roles',
            'title'    => 'Users & RBAC',
            'subtitle' => 'Default roles & permissions',
            'url'      => url('settings/users-roles'),
            'match'    => ['settings/users-roles'],
            'icon'     => 'bx bx-group',
            'color'    => 'secondary',
            'keywords' => 'roles permissions spatie staff access default role user registration'
        ],
        [
            'key'      => 'api-webhooks',
            'title'    => 'API & Webhooks',
            'subtitle' => 'API keys & webhook events',
            'url'      => url('settings/api-webhooks'),
            'match'    => ['settings/api-webhooks'],
            'icon'     => 'bx bx-code-block',
            'color'    => 'primary',
            'keywords' => 'api keys webhooks secret order.created payment.succeeded logs retry'
        ],
        [
            'key'      => 'backup',
            'title'    => 'Backup & Maintenance',
            'subtitle' => 'Database snapshot & cache',
            'url'      => url('settings/backup'),
            'match'    => ['settings/backup'],
            'icon'     => 'bx bx-data',
            'color'    => 'warning',
            'keywords' => 'backup database snapshot cache clear optimize maintenance mode system'
        ],
        [
            'key'      => 'audit-logs',
            'title'    => 'Audit Trail',
            'subtitle' => 'Searchable system audit logs',
            'url'      => url('settings/audit-logs'),
            'match'    => ['settings/audit-logs'],
            'icon'     => 'bx bx-history',
            'color'    => 'info',
            'keywords' => 'audit logs activity user ip timestamp settings change password login'
        ],
    ]
];
@endphp

<div class="card shadow-sm border rounded-4 overflow-hidden mb-4 mb-lg-0 settings-sidebar-card">
  <!-- Accent Gradient Header -->
  <div style="height: 4px; background: linear-gradient(90deg, #696cff, #00d25b, #ffab00, #ff3e1d);"></div>

  <div class="card-header border-bottom py-3 px-4 bg-light-subtle">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-label-primary">
          <i class="icon-base bx bx-slider-alt fs-5"></i>
        </div>
        <div>
          <h6 class="mb-0 fw-bold text-heading" style="font-size: 0.95rem;">{{ __('Settings Center') }}</h6>
          <small class="text-muted fs-tiny">{{ __('Management Hub') }}</small>
        </div>
      </div>
      <span class="badge bg-label-primary rounded-pill px-2.5 py-1 fs-tiny fw-semibold">30 {{ __('Sections') }}</span>
    </div>

    <!-- Live Quick Search Bar -->
    <div class="position-relative">
      <div class="input-group input-group-merge shadow-none">
        <span class="input-group-text bg-white border-end-0 py-1.5"><i class="bx bx-search fs-5 text-muted"></i></span>
        <input type="text" id="settingsQuickSearch" class="form-control bg-white border-start-0 py-1.5 ps-0" placeholder="{{ __('Search settings (e.g. SMTP, WhatsApp, Tax)...') }}" autocomplete="off" />
      </div>
    </div>
  </div>

  <div class="card-body p-2 p-md-3 sidebar-scroll-area" style="max-height: calc(100vh - 220px); overflow-y: auto;">
    <div id="settingsItemsContainer">
      @foreach($categories as $categoryName => $items)
        <div class="settings-category-group mb-3">
          <div class="text-uppercase text-muted fw-bold px-2.5 py-1 mb-1 settings-category-title" style="font-size: 0.72rem; letter-spacing: 0.5px;">
            {{ __($categoryName) }}
          </div>
          <div class="list-group list-group-flush gap-1">
            @foreach($items as $item)
              @php
                $isActive = in_array($currentPath, $item['match']) || request()->is($item['match']) || (isset($currentSection) && $currentSection === $item['key']);
              @endphp
              <a href="{{ $item['url'] }}"
                 data-title="{{ strtolower($item['title']) }}"
                 data-subtitle="{{ strtolower($item['subtitle']) }}"
                 data-keywords="{{ strtolower($item['keywords']) }}"
                 class="settings-nav-item list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2.5 rounded-3 border-0 transition-all {{ $isActive ? 'active-settings-tab shadow-sm' : 'inactive-settings-tab' }}">
                <div class="d-flex align-items-center gap-3">
                  <span class="settings-icon-wrapper rounded-3 p-2 d-flex align-items-center justify-content-center {{ $isActive ? 'bg-white text-primary shadow-xs' : 'bg-label-' . $item['color'] }}">
                    <i class="icon-base {{ $item['icon'] }} fs-5"></i>
                  </span>
                  <div>
                    <span class="d-block fw-semibold {{ $isActive ? 'text-white' : 'text-heading' }}" style="font-size: 0.86rem;">{{ __($item['title']) }}</span>
                    <small class="d-block {{ $isActive ? 'text-white-75' : 'text-muted' }}" style="font-size: 0.73rem;">{{ __($item['subtitle']) }}</small>
                  </div>
                </div>
                <i class="icon-base bx bx-chevron-right fs-5 {{ $isActive ? 'text-white' : 'text-muted opacity-50 settings-chevron' }}"></i>
              </a>
            @endforeach
          </div>
        </div>
      @endforeach
      <div id="noSettingsFound" class="text-center py-4 d-none text-muted">
        <i class="bx bx-search-alt fs-1 text-secondary mb-1"></i>
        <p class="small mb-0">{{ __('No matching settings found.') }}</p>
      </div>
    </div>
  </div>
</div>

<style>
  .settings-sidebar-card {
    border-color: rgba(67, 89, 113, 0.12) !important;
    position: sticky;
    top: 85px;
    z-index: 10;
    background: #ffffff;
  }
  .active-settings-tab {
    background: linear-gradient(135deg, #696cff 0%, #585be4 100%) !important;
    color: #ffffff !important;
    transform: translateX(3px);
    box-shadow: 0 4px 14px rgba(105, 108, 255, 0.35) !important;
  }
  .text-white-75 {
    color: rgba(255, 255, 255, 0.8) !important;
  }
  .inactive-settings-tab {
    background-color: transparent;
    color: #566a7f;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .inactive-settings-tab:hover {
    background-color: rgba(105, 108, 255, 0.07) !important;
    color: #696cff !important;
    transform: translateX(4px);
  }
  .inactive-settings-tab:hover .settings-chevron {
    color: #696cff !important;
    opacity: 1 !important;
    transform: translateX(2px);
  }
  .settings-icon-wrapper {
    width: 36px;
    height: 36px;
    min-width: 36px;
    transition: all 0.2s ease;
  }
  .sidebar-scroll-area::-webkit-scrollbar {
    width: 5px;
  }
  .sidebar-scroll-area::-webkit-scrollbar-thumb {
    background-color: rgba(67, 89, 113, 0.2);
    border-radius: 4px;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('settingsQuickSearch');
    const items = document.querySelectorAll('.settings-nav-item');
    const groups = document.querySelectorAll('.settings-category-group');
    const noResults = document.getElementById('noSettingsFound');

    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        items.forEach(item => {
          const title = item.getAttribute('data-title') || '';
          const subtitle = item.getAttribute('data-subtitle') || '';
          const keywords = item.getAttribute('data-keywords') || '';

          if (query === '' || title.includes(query) || subtitle.includes(query) || keywords.includes(query)) {
            item.classList.remove('d-none');
            visibleCount++;
          } else {
            item.classList.add('d-none');
          }
        });

        groups.forEach(group => {
          const visibleInGroup = group.querySelectorAll('.settings-nav-item:not(.d-none)').length;
          if (visibleInGroup === 0) {
            group.classList.add('d-none');
          } else {
            group.classList.remove('d-none');
          }
        });

        if (visibleCount === 0 && query !== '') {
          noResults.classList.remove('d-none');
        } else {
          noResults.classList.add('d-none');
        }
      });
    }
  });
</script>
