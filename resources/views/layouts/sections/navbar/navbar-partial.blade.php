@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
  <a href="{{ url('/') }}" class="app-brand-link gap-2">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo menu-text fw-bold text-heading">{{ config('variables.templateName') }}</span>
  </a>

  <!-- Display menu close icon only for horizontal-menu with navbar-full -->
  @if (isset($menuHorizontal))
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
    <i class="icon-base bx bx-chevron-left d-flex align-items-center justify-content-center"></i>
  </a>
  @endif
</div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
<div
  class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
  <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="icon-base bx bx-menu icon-md"></i>
  </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

  @if (!isset($menuHorizontal))
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper mb-0">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </div>
  </div>
  <!-- /Search -->
  @endif

  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
    @if (isset($menuHorizontal))
    <!-- Search -->
    <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </li>
    <!-- /Search -->
    @endif

    <!-- Language -->
    <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base bx bx-globe icon-md"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ url('lang/en') }}"
            data-language="en" data-text-direction="ltr">
            <span>English</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'ml' ? 'active' : '' }}" href="{{ url('lang/ml') }}"
            data-language="ml" data-text-direction="ltr">
            <span class="fw-semibold">മലയാളം (Malayalam)</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'hi' ? 'active' : '' }}" href="{{ url('lang/hi') }}"
            data-language="hi" data-text-direction="ltr">
            <span>हिन्दी (Hindi)</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ url('lang/ar') }}"
            data-language="ar" data-text-direction="rtl">
            <span>العربية (Arabic)</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ url('lang/fr') }}"
            data-language="fr" data-text-direction="ltr">
            <span>Français (French)</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="{{ url('lang/de') }}"
            data-language="de" data-text-direction="ltr">
            <span>Deutsch (German)</span>
          </a>
        </li>
      </ul>
    </li>
    <!--/ Language -->
    
    <!-- Branch Switcher -->
    @php
      $authUser = Auth::user();
      $currentBranchId = session('branch_id') ?? $authUser?->branch_id ?? request()->cookie('akmart_branch_id', 1);
      $accessibleBranches = $authUser && method_exists($authUser, 'accessibleBranches') 
          ? $authUser->accessibleBranches() 
          : \App\Models\Branch\Branch::all();
      $canSwitch = $authUser && method_exists($authUser, 'canSwitchBranch') ? $authUser->canSwitchBranch() : true;
    @endphp
    <li class="nav-item dropdown me-2 me-xl-0">
      @if($canSwitch && $accessibleBranches->count() > 1)
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base bx bx-store icon-md text-primary"></i>
          <span class="d-none d-md-inline-block ms-1 fw-medium">{{ App\Models\Branch\Branch::find($currentBranchId)?->name ?? __('Select Branch') }}</span>
          <i class="icon-base bx bx-chevron-down icon-xs ms-1 text-muted"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
          <li class="dropdown-header small text-uppercase text-muted">{{ __('Available Branches') }}</li>
          @foreach($accessibleBranches as $branch)
          <li>
            <a class="dropdown-item d-flex align-items-center justify-content-between {{ $currentBranchId == $branch->id ? 'active' : '' }}" href="{{ route('branch-swap', $branch->id) }}">
              <span><i class="icon-base bx bx-buildings me-2"></i>{{ $branch->name }}</span>
              @if($currentBranchId == $branch->id)
                <i class="icon-base bx bx-check text-success"></i>
              @endif
            </a>
          </li>
          @endforeach
        </ul>
      @else
        <div class="nav-link d-flex align-items-center px-2 py-1 bg-label-secondary rounded">
          <i class="icon-base bx bx-store icon-sm text-secondary me-1"></i>
          <span class="d-none d-md-inline-block small fw-semibold text-truncate" style="max-width: 150px;">
            {{ App\Models\Branch\Branch::find($currentBranchId)?->name ?? __('Main Store') }}
          </span>
          <i class="icon-base bx bx-lock-alt icon-xs ms-1 text-muted" title="{{ __('Branch locked to your user profile') }}"></i>
        </div>
      @endif
    </li>
    <!--/ Branch Switcher -->

    @if ($configData['hasCustomizer'] == true)
    <!-- Style Switcher -->
    <li class="nav-item dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
        <span class="d-none ms-2" id="nav-theme-text">{{ __('Toggle theme') }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
        <li>
          <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
            aria-pressed="false">
            <span><i class="icon-base bx bx-sun icon-md me-3" data-icon="sun"></i>{{ __('Light') }}</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark" aria-pressed="true">
            <span><i class="icon-base bx bx-moon icon-md me-3" data-icon="moon"></i>{{ __('Dark') }}</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
            aria-pressed="false">
            <span><i class="icon-base bx bx-desktop icon-md me-3" data-icon="desktop"></i>{{ __('System') }}</span>
          </button>
        </li>
      </ul>
    </li>
    <!-- / Style Switcher-->
    @endif

    <!-- Quick links  -->
    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
        data-bs-auto-close="outside" aria-expanded="false">
        <i class="icon-base bx bx-grid-alt icon-md"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end p-0">
        <div class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">{{ __('Shortcuts') }}</h6>
            <a href="javascript:void(0)" class="dropdown-shortcuts-add py-2" title="{{ __('Add / Customize shortcuts') }}"><i class="icon-base bx bx-plus-circle text-heading"></i></a>
          </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container" id="navbar-shortcuts-container">
          {{-- Dynamically populated and initialized from localStorage / defaults --}}
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-calendar icon-26px text-heading"></i>
              </span>
              <a href="{{ route('app-calendar') }}" class="stretched-link">{{ __('Calendar') }}</a>
              <small>{{ __('Appointments') }}</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-food-menu icon-26px text-heading"></i>
              </span>
              <a href="{{ route('app-invoice-list') }}" class="stretched-link">{{ __('Invoice App') }}</a>
              <small>{{ __('Manage Accounts') }}</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-import icon-26px text-heading"></i>
              </span>
              <a href="{{ route('app-product-importer') }}" class="stretched-link">{{ __('Product Importer') }}</a>
              <small>{{ __('Universal Scraper') }}</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-cart-alt icon-26px text-heading"></i>
              </span>
              <a href="{{ route('app-vendor-pos') }}" class="stretched-link">{{ __('POS Terminal') }}</a>
              <small>{{ __('Point of Sale') }}</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-box icon-26px text-heading"></i>
              </span>
              <a href="{{ route('app-ecommerce-product-list') }}" class="stretched-link">{{ __('Products') }}</a>
              <small>{{ __('Manage Catalog') }}</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-pie-chart-alt-2 icon-26px text-heading"></i>
              </span>
              <a href="{{ route('app-ecommerce-dashboard') }}" class="stretched-link">{{ __('Dashboard') }}</a>
              <small>{{ __('Store Dashboard') }}</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-bot icon-26px text-heading"></i>
              </span>
              <a href="{{ Route::has('app-ecommerce-settings-ai') ? route('app-ecommerce-settings-ai') : url('settings/ai') }}" class="stretched-link">{{ __('AI Settings') }}</a>
              <small>{{ __('AI & Copilot Tools') }}</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-bar-chart-alt-2 icon-26px text-heading"></i>
              </span>
              <a href="{{ Route::has('app-reports') ? route('app-reports') : url('reports') }}" class="stretched-link">{{ __('Reports') }}</a>
              <small>{{ __('Analytics & Profit') }}</small>
            </div>
          </div>
        </div>
      </div>
    </li>
    <!-- Quick links -->

    <!-- Notification -->
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
        data-bs-auto-close="outside" aria-expanded="false">
        <span class="position-relative">
          <i class="icon-base bx bx-bell icon-md"></i>
          @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
          @endif
        </span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end p-0">
        <li class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">{{ __('Notifications') }}</h6>
            <div class="d-flex align-items-center h6 mb-0">
              <span class="badge bg-label-primary me-2">{{ auth()->check() ? auth()->user()->unreadNotifications->count() : 0 }} {{ __('New') }}</span>
              <form action="{{ route('app-notifications-mark-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Mark all as read') }}">
                  <i class="icon-base bx bx-envelope-open text-heading"></i>
                </button>
              </form>
            </div>
          </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
          <ul class="list-group list-group-flush">
            @if(auth()->check())
              @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
              <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                      <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx {{ $notification->data['icon'] ?? 'bx-bell' }}"></i></span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="small mb-0">{{ $notification->data['title'] ?? __('New Alert') }}</h6>
                    <small class="mb-1 d-block text-body">{{ $notification->data['message'] ?? '' }}</small>
                    <small class="text-body-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                  </div>
                  <div class="flex-shrink-0 dropdown-notifications-actions">
                    <form action="{{ route('app-notifications-read', $notification->id) }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-link dropdown-notifications-read"><span class="badge badge-dot"></span></button>
                    </form>
                  </div>
                </div>
              </li>
              @empty
              <li class="list-group-item text-center py-4 text-muted small">{{ __('No new notifications') }}</li>
              @endforelse
            @else
              <li class="list-group-item text-center py-4 text-muted small">{{ __('Please login to see notifications') }}</li>
            @endif
          </ul>
        </li>
        <li class="border-top">
          <div class="d-grid p-4">
            <a class="btn btn-primary btn-sm d-flex" href="{{ route('app-notifications') }}">
              <small class="align-middle">{{ __('View all notifications') }}</small>
            </a>
          </div>
        </li>
      </ul>
    </li>
    <!--/ Notification -->
    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}" alt="{{ Auth::user()?->name ?? 'User' }}"
            class="rounded-circle" style="object-fit: cover; width: 38px; height: 38px;" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="{{ route('pages-profile-user') }}">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                    alt="{{ Auth::user()?->name ?? 'User' }}" class="w-px-40 h-px-40 rounded-circle" style="object-fit: cover;" />
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">
                  @if (Auth::check())
                  {{ Auth::user()->name }}
                  @else
                  John Doe
                  @endif
                </h6>
                @if (Auth::check())
                  @php
                    $authUser = Auth::user();
                    $displayRole = $authUser->roles->first()?->name 
                      ?? ($authUser->isSupremeAdmin() ? __('Supreme Admin') 
                      : ($authUser->isSuperAdmin() ? __('Super Admin') 
                      : ucfirst(str_replace('_', ' ', $authUser->user_type ?? 'User'))));
                  @endphp
                  <small class="text-body-secondary">{{ $displayRole }}</small>
                @else
                  <small class="text-body-secondary">{{ __('Admin') }}</small>
                @endif
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('pages-profile-user') }}">
            <i class="icon-base bx bx-user icon-md me-3"></i><span>{{ __('My Profile') }}</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('pages-account-settings-security') }}">
            <i class="icon-base bx bx-shield-quarter icon-md me-3"></i><span>{{ __('Security') }}</span>
          </a>
        </li>
        @if (Auth::check() && (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Branch Manager') || Auth::user()->branch_id))
        <li>
          <a class="dropdown-item" href="{{ route('app-saas-billing') }}">
            <span class="d-flex align-items-center align-middle">
              <i class="flex-shrink-0 icon-base bx bx-credit-card icon-md me-3"></i>
              <span class="flex-grow-1 align-middle">{{ __('Billing Plan') }}</span>
              <span class="flex-shrink-0 badge rounded-pill bg-label-primary">{{ __('SaaS') }}</span>
            </span>
          </a>
        </li>
        @endif
        <li>
          <a class="dropdown-item" href="{{ route('app-notifications') }}">
            <i class="icon-base bx bx-bell icon-md me-3"></i><span>{{ __('Notifications') }}</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        @if (Auth::check())
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-base bx bx-power-off icon-md me-3 text-danger"></i><span class="text-danger">{{ __('Logout') }}</span>
          </a>
        </li>
        <form method="POST" id="logout-form" action="{{ route('logout') }}" class="d-none">
          @csrf
        </form>
        @else
        <li>
          <a class="dropdown-item" href="{{ route('auth-login-basic') }}">
            <i class="icon-base bx bx-log-in icon-md me-3"></i><span>{{ __('Login') }}</span>
          </a>
        </li>
        @endif
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>

<!-- Modal: Manage & Customize Navigation Shortcuts -->
<div class="modal fade" id="modalAddShortcut" tabindex="-1" aria-labelledby="modalAddShortcutLabel" aria-hidden="true" style="z-index: 1090;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border">
      <div class="modal-header border-bottom">
        <h5 class="modal-title d-flex align-items-center gap-2 mb-0" id="modalAddShortcutLabel">
          <i class="icon-base bx bx-grid-alt text-primary fs-4"></i>
          <span>Customize Quick Shortcuts</span>
          <span class="badge bg-label-primary fs-tiny" id="active-shortcuts-count">8 Active</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Tabs -->
        <ul class="nav nav-pills mb-3" id="shortcutTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-presets-btn" data-bs-toggle="pill" data-bs-target="#tab-presets" type="button" role="tab">
              <i class="icon-base bx bx-star me-1"></i> Quick Presets
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-custom-btn" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button" role="tab">
              <i class="icon-base bx bx-plus-circle me-1"></i> Add Custom
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-manage-btn" data-bs-toggle="pill" data-bs-target="#tab-manage" type="button" role="tab">
              <i class="icon-base bx bx-list-ul me-1"></i> Active List (<span id="tab-count-badge">8</span>)
            </button>
          </li>
        </ul>

        <div class="tab-content border-0 p-0" id="shortcutTabsContent">
          <!-- Tab 1: Presets -->
          <div class="tab-pane fade show active" id="tab-presets" role="tabpanel">
            <p class="text-muted small mb-3">Click on any module below to toggle it on/off in your navigation shortcuts grid:</p>
            <div class="row g-2" id="preset-shortcuts-grid">
              <!-- Dynamically populated from JS -->
            </div>
          </div>

          <!-- Tab 2: Custom -->
          <div class="tab-pane fade" id="tab-custom" role="tabpanel">
            <form id="formAddCustomShortcut" onsubmit="event.preventDefault(); window.akMartShortcuts.addCustom();">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Shortcut Title <span class="text-danger">*</span></label>
                  <input type="text" id="custom-sc-title" class="form-control" placeholder="e.g. My Supplier Orders" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Subtitle / Description</label>
                  <input type="text" id="custom-sc-subtitle" class="form-control" placeholder="e.g. Pending Approvals">
                </div>
                <div class="col-md-8">
                  <label class="form-label fw-semibold">Target URL / Route <span class="text-danger">*</span></label>
                  <input type="text" id="custom-sc-url" class="form-control" placeholder="e.g. /app/ecommerce/order/list or https://..." required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Select Icon</label>
                  <select id="custom-sc-icon" class="form-select">
                    <option value="bx-cart-alt">🛒 Cart / POS</option>
                    <option value="bx-import">⚡ Importer</option>
                    <option value="bx-box">📦 Box / Products</option>
                    <option value="bx-bot">🤖 AI Copilot</option>
                    <option value="bx-bar-chart-alt-2">📊 Analytics</option>
                    <option value="bx-food-menu">📄 Invoices</option>
                    <option value="bx-calendar">📅 Calendar</option>
                    <option value="bx-user">👤 Customers</option>
                    <option value="bx-wallet">💰 Wallet / Expenses</option>
                    <option value="bx-purchase-tag-alt">🏷️ Coupons</option>
                    <option value="bx-buildings">🏭 B2B / Quotes</option>
                    <option value="bx-scan">🔍 Scanner</option>
                    <option value="bx-cog">⚙️ Settings</option>
                    <option value="bx-check-shield">🛡️ Security</option>
                    <option value="bx-star">⭐ Star</option>
                    <option value="bx-link">🔗 Link</option>
                  </select>
                </div>
                <div class="col-12 mt-3">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base bx bx-plus me-1"></i> Add to Navigation Shortcuts
                  </button>
                </div>
              </div>
            </form>
          </div>

          <!-- Tab 3: Active List -->
          <div class="tab-pane fade" id="tab-manage" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="text-muted small">Manage or remove currently active navigation shortcuts.</span>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.akMartShortcuts.resetToDefaults();">
                <i class="icon-base bx bx-refresh me-1"></i> Reset to Factory Defaults
              </button>
            </div>
            <div class="list-group" id="active-shortcuts-list">
              <!-- Dynamically populated from JS -->
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top bg-light">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
          <i class="icon-base bx bx-check me-1"></i> Done
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const STORAGE_KEY = 'akmart_user_shortcuts_v3';

    const ALL_PRESETS = [
        { id: 'calendar', title: 'Calendar', subtitle: 'Appointments', url: '{{ Route::has("app-calendar") ? route("app-calendar") : url("app/calendar") }}', icon: 'bx-calendar' },
        { id: 'invoice', title: 'Invoice App', subtitle: 'Manage Accounts', url: '{{ Route::has("app-invoice-list") ? route("app-invoice-list") : url("app/invoice/list") }}', icon: 'bx-food-menu' },
        { id: 'importer', title: 'Product Importer', subtitle: 'Universal Scraper', url: '{{ Route::has("app-product-importer") ? route("app-product-importer") : url("catalog/importer") }}', icon: 'bx-import' },
        { id: 'pos', title: 'POS Terminal', subtitle: 'Point of Sale', url: '{{ Route::has("app-vendor-pos") ? route("app-vendor-pos") : url("apps/vendor/pos") }}', icon: 'bx-cart-alt' },
        { id: 'products', title: 'Products', subtitle: 'Manage Catalog', url: '{{ Route::has("app-ecommerce-product-list") ? route("app-ecommerce-product-list") : url("app/ecommerce/product/list") }}', icon: 'bx-box' },
        { id: 'dashboard', title: 'Dashboard', subtitle: 'Store Dashboard', url: '{{ Route::has("app-ecommerce-dashboard") ? route("app-ecommerce-dashboard") : url("app/ecommerce/dashboard") }}', icon: 'bx-pie-chart-alt-2' },
        { id: 'ai', title: 'AI Settings', subtitle: 'AI Tools & Copilot', url: '{{ Route::has("app-ecommerce-settings-ai") ? route("app-ecommerce-settings-ai") : url("settings/ai") }}', icon: 'bx-bot' },
        { id: 'reports', title: 'Reports', subtitle: 'Analytics & Profit', url: '{{ Route::has("app-reports") ? route("app-reports") : url("reports") }}', icon: 'bx-bar-chart-alt-2' },
        { id: 'orders', title: 'Orders', subtitle: 'Order Processing', url: '{{ Route::has("app-ecommerce-order-list") ? route("app-ecommerce-order-list") : url("app/ecommerce/order/list") }}', icon: 'bx-shopping-bag' },
        { id: 'customers', title: 'Customers', subtitle: 'Manage Users', url: '{{ Route::has("app-ecommerce-customer-all") ? route("app-ecommerce-customer-all") : url("app/ecommerce/customer/all") }}', icon: 'bx-user' },
        { id: 'scanner', title: 'Catalog Scanner', subtitle: 'Health Check', url: '{{ Route::has("app-catalog-scanner") ? route("app-catalog-scanner") : url("catalog/scanner") }}', icon: 'bx-scan' },
        { id: 'coupons', title: 'Promotions', subtitle: 'Discounts & Deals', url: '{{ Route::has("app-ecommerce-coupon-list") ? route("app-ecommerce-coupon-list") : url("app/ecommerce/coupon/list") }}', icon: 'bx-purchase-tag-alt' },
        { id: 'b2b', title: 'B2B Quotes', subtitle: 'Wholesale Pricing', url: '{{ Route::has("app-b2b-quotes") ? route("app-b2b-quotes") : url("apps/b2b/quotes") }}', icon: 'bx-buildings' },
        { id: 'expenses', title: 'Expenses', subtitle: 'Cost Tracking', url: '{{ Route::has("app-expenses") ? route("app-expenses") : url("expenses") }}', icon: 'bx-wallet' },
        { id: 'automation', title: 'Automation', subtitle: 'Trigger Workflows', url: '{{ Route::has("app-automation-rules") ? route("app-automation-rules") : url("automation") }}', icon: 'bx-git-repo-forked' },
        { id: 'settings', title: 'Setting', subtitle: 'Account & Store', url: '{{ Route::has("app-ecommerce-settings-details") ? route("app-ecommerce-settings-details") : url("settings/store") }}', icon: 'bx-cog' },
        { id: 'roles', title: 'Role Management', subtitle: 'Permissions', url: '{{ Route::has("app-access-roles") ? route("app-access-roles") : url("app/access-roles") }}', icon: 'bx-check-shield' }
    ];

    const DEFAULT_IDS = ['calendar', 'invoice', 'importer', 'pos', 'products', 'dashboard', 'ai', 'reports'];

    window.akMartShortcuts = {
        getItems: function () {
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored) {
                    return JSON.parse(stored);
                }
            } catch (e) {
                console.error(e);
            }
            return ALL_PRESETS.filter(p => DEFAULT_IDS.includes(p.id));
        },

        saveItems: function (items) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
            this.renderNavbar();
            this.renderModal();
        },

        togglePreset: function (id) {
            let current = this.getItems();
            const exists = current.some(i => i.id === id);
            if (exists) {
                if (current.length <= 1) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimum Shortcut Reached',
                            text: 'You must keep at least 1 shortcut active.',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    } else {
                        alert('You must keep at least 1 shortcut active.');
                    }
                    return;
                }
                current = current.filter(i => i.id !== id);
            } else {
                const preset = ALL_PRESETS.find(p => p.id === id);
                if (preset) {
                    current.push(preset);
                }
            }
            this.saveItems(current);
        },

        removeByIndex: function (index) {
            let current = this.getItems();
            if (current.length <= 1) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum Shortcut Reached',
                        text: 'You must keep at least 1 shortcut active.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                } else {
                    alert('You must keep at least 1 shortcut active.');
                }
                return;
            }
            current.splice(index, 1);
            this.saveItems(current);
        },

        addCustom: function () {
            const title = document.getElementById('custom-sc-title').value.trim();
            const subtitle = document.getElementById('custom-sc-subtitle').value.trim() || 'Quick Link';
            const url = document.getElementById('custom-sc-url').value.trim();
            const icon = document.getElementById('custom-sc-icon').value;

            if (!title || !url) return;

            const newItem = {
                id: 'custom_' + Date.now(),
                title: title,
                subtitle: subtitle,
                url: url,
                icon: icon
            };

            const current = this.getItems();
            current.push(newItem);
            this.saveItems(current);

            document.getElementById('formAddCustomShortcut').reset();
            const manageTabBtn = document.getElementById('tab-manage-btn');
            if (manageTabBtn) {
                bootstrap.Tab.getOrCreateInstance(manageTabBtn).show();
            }
        },

        resetToDefaults: function () {
            localStorage.removeItem(STORAGE_KEY);
            this.renderNavbar();
            this.renderModal();
        },

        renderNavbar: function () {
            const container = document.getElementById('navbar-shortcuts-container');
            if (!container) return;

            const items = this.getItems();
            let html = '';

            for (let i = 0; i < items.length; i += 2) {
                const item1 = items[i];
                const item2 = items[i + 1];

                html += '<div class="row row-bordered overflow-visible g-0">';
                html += `
                    <div class="dropdown-shortcuts-item col">
                      <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                        <i class="icon-base bx ${item1.icon} icon-26px text-heading"></i>
                      </span>
                      <a href="${item1.url}" class="stretched-link">${this.escapeHtml(item1.title)}</a>
                      <small>${this.escapeHtml(item1.subtitle)}</small>
                    </div>
                `;

                if (item2) {
                    html += `
                        <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                            <i class="icon-base bx ${item2.icon} icon-26px text-heading"></i>
                          </span>
                          <a href="${item2.url}" class="stretched-link">${this.escapeHtml(item2.title)}</a>
                          <small>${this.escapeHtml(item2.subtitle)}</small>
                        </div>
                    `;
                } else {
                    html += '<div class="dropdown-shortcuts-item col bg-transparent"></div>';
                }

                html += '</div>';
            }

            container.innerHTML = html;
        },

        renderModal: function () {
            const current = this.getItems();
            const count = current.length;

            const countBadge = document.getElementById('active-shortcuts-count');
            const tabCountBadge = document.getElementById('tab-count-badge');
            if (countBadge) countBadge.textContent = count + ' Active';
            if (tabCountBadge) tabCountBadge.textContent = count;

            // Render Presets Grid
            const presetsGrid = document.getElementById('preset-shortcuts-grid');
            if (presetsGrid) {
                let phtml = '';
                ALL_PRESETS.forEach(p => {
                    const isActive = current.some(i => i.id === p.id);
                    phtml += `
                        <div class="col-md-4 col-sm-6">
                            <div class="card border h-100 ${isActive ? 'border-primary bg-label-primary' : 'bg-transparent'} p-2 cursor-pointer" onclick="window.akMartShortcuts.togglePreset('${p.id}')">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="icon-base bx ${p.icon} fs-4 ${isActive ? 'text-primary' : 'text-muted'}"></i>
                                        <div>
                                            <strong class="d-block text-truncate" style="max-width: 140px;">${this.escapeHtml(p.title)}</strong>
                                            <small class="text-muted text-truncate d-block" style="max-width: 140px;">${this.escapeHtml(p.subtitle)}</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" ${isActive ? 'checked' : ''} onclick="event.stopPropagation(); window.akMartShortcuts.togglePreset('${p.id}')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                presetsGrid.innerHTML = phtml;
            }

            // Render Active List
            const activeList = document.getElementById('active-shortcuts-list');
            if (activeList) {
                let ahtml = '';
                current.forEach((item, idx) => {
                    ahtml += `
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-label-secondary p-2 rounded">
                                    <i class="icon-base bx ${item.icon} fs-5"></i>
                                </span>
                                <div>
                                    <strong>${this.escapeHtml(item.title)}</strong>
                                    <small class="text-muted d-block">${this.escapeHtml(item.subtitle)} • <code>${this.escapeHtml(item.url)}</code></small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" onclick="window.akMartShortcuts.removeByIndex(${idx})" title="Remove shortcut">
                                <i class="icon-base bx bx-trash fs-5"></i>
                            </button>
                        </div>
                    `;
                });
                activeList.innerHTML = ahtml;
            }
        },

        escapeHtml: function (str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    };

    window.akMartShortcuts.renderNavbar();
    window.akMartShortcuts.renderModal();

    // Move modal to document.body to prevent navbar stacking context backdrop issues
    const modalEl = document.getElementById('modalAddShortcut');
    if (modalEl && modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    // Attach click handler to "+" button to cleanly hide dropdown and show modal
    document.querySelectorAll('.dropdown-shortcuts-add').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Hide parent dropdown if open
            const dropdown = btn.closest('.dropdown');
            if (dropdown && window.bootstrap && bootstrap.Dropdown) {
                const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                    if (bsDropdown) bsDropdown.hide();
                }
            }

            if (modalEl && window.bootstrap && bootstrap.Modal) {
                const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                bsModal.show();
            }
        });
    });
});
</script>
