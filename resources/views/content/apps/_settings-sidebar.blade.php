@php
$currentPath = trim(request()->path(), '/');
$settingsLinks = [
    [
        'title'    => 'Store Details',
        'subtitle' => 'General info & currency',
        'url'      => url('settings/store'),
        'match'    => ['settings/store', 'app/ecommerce/settings/details'],
        'icon'     => 'bx bx-store-alt',
        'color'    => 'primary'
    ],
    [
        'title'    => 'Payments',
        'subtitle' => 'Gateways, Stripe & COD',
        'url'      => url('settings/payments'),
        'match'    => ['settings/payments', 'app/ecommerce/settings/payments'],
        'icon'     => 'bx bx-credit-card',
        'color'    => 'success'
    ],
    [
        'title'    => 'Checkout',
        'subtitle' => 'Guest checkout & policy',
        'url'      => url('settings/checkout'),
        'match'    => ['settings/checkout', 'app/ecommerce/settings/checkout'],
        'icon'     => 'bx bx-cart-alt',
        'color'    => 'info'
    ],
    [
        'title'    => 'Shipping & Delivery',
        'subtitle' => 'Rates, zones & fulfillment',
        'url'      => url('settings/shipping'),
        'match'    => ['settings/shipping', 'app/ecommerce/settings/shipping'],
        'icon'     => 'bx bx-package',
        'color'    => 'warning'
    ],
    [
        'title'    => 'Locations',
        'subtitle' => 'Branches & warehouses',
        'url'      => url('settings/locations'),
        'match'    => ['settings/locations', 'app/ecommerce/settings/locations'],
        'icon'     => 'bx bx-map-pin',
        'color'    => 'danger'
    ],
    [
        'title'    => 'Notifications',
        'subtitle' => 'Email alerts & events',
        'url'      => url('settings/notifications'),
        'match'    => ['settings/notifications', 'app/ecommerce/settings/notifications'],
        'icon'     => 'bx bx-bell',
        'color'    => 'info'
    ],
    [
        'title'    => 'AI & Copilot',
        'subtitle' => 'Gemini API & automations',
        'url'      => url('settings/ai'),
        'match'    => ['settings/ai', 'app/ecommerce/settings/ai', 'apps/ai-copilot'],
        'icon'     => 'bx bx-bot',
        'color'    => 'primary'
    ],
    [
        'title'    => 'Logos & Branding',
        'subtitle' => 'App icons, logo & colors',
        'url'      => url('settings/branding'),
        'match'    => ['settings/branding', 'app/ecommerce/settings/branding'],
        'icon'     => 'bx bx-palette',
        'color'    => 'secondary'
    ],
    [
        'title'    => 'Maps & Geolocation',
        'subtitle' => 'Google Maps API & pins',
        'url'      => url('settings/maps'),
        'match'    => ['settings/maps', 'app/ecommerce/settings/maps'],
        'icon'     => 'bx bx-map-alt',
        'color'    => 'dark'
    ],
];
@endphp

<div class="card shadow-sm border rounded-4 overflow-hidden mb-4 mb-lg-0 settings-sidebar-card">
  <!-- Accent Top Border -->
  <div style="height: 3px; background: linear-gradient(90deg, #696cff, #585be4);"></div>

  <div class="card-header border-bottom py-3 px-4 bg-light-subtle d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="settings-header-icon-badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-label-primary">
        <i class="icon-base bx bx-slider-alt fs-5"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold text-heading" style="font-size: 0.95rem;">{{ __('Settings Menu') }}</h6>
        <small class="text-muted fs-tiny">{{ __('Store Configuration') }}</small>
      </div>
    </div>
    <span class="badge bg-label-primary rounded-pill px-2.5 py-1 fs-tiny fw-semibold">{{ count($settingsLinks) }} {{ __('Sections') }}</span>
  </div>

  <div class="card-body p-2 p-md-3">
    <div class="list-group list-group-flush gap-1">
      @foreach($settingsLinks as $item)
        @php
          $isActive = in_array($currentPath, $item['match']) || request()->is($item['match']);
        @endphp
        <a href="{{ $item['url'] }}"
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2.5 rounded-3 border-0 transition-all {{ $isActive ? 'active-settings-tab shadow-sm' : 'inactive-settings-tab' }}">
          <div class="d-flex align-items-center gap-3">
            <span class="settings-icon-wrapper rounded-3 p-2 d-flex align-items-center justify-content-center {{ $isActive ? 'bg-white text-primary shadow-xs' : 'bg-label-' . $item['color'] }}">
              <i class="icon-base {{ $item['icon'] }} fs-5"></i>
            </span>
            <div>
              <span class="d-block fw-semibold {{ $isActive ? 'text-white' : 'text-heading' }}" style="font-size: 0.88rem;">{{ __($item['title']) }}</span>
              <small class="d-block {{ $isActive ? 'text-white-75' : 'text-muted' }}" style="font-size: 0.75rem;">{{ __($item['subtitle']) }}</small>
            </div>
          </div>
          <i class="icon-base bx bx-chevron-right fs-5 {{ $isActive ? 'text-white' : 'text-muted opacity-50 settings-chevron' }}"></i>
        </a>
      @endforeach
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
    width: 38px;
    height: 38px;
    min-width: 38px;
    transition: all 0.2s ease;
  }
  .settings-header-icon-badge {
    width: 34px;
    height: 34px;
  }
  .settings-chevron {
    transition: all 0.2s ease;
  }
</style>
