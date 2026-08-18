@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"
    @foreach ($configData['menuAttributes'] as $attribute => $value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  <!-- ! Hide app brand if navbar-full -->
  @if (!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center me-2">
      <span class="app-brand-logo demo">@include('_partials.macros')</span>
      <span class="app-brand-text demo menu-text fw-bold ms-2 text-heading" style="font-size: 1.25rem; letter-spacing: -0.3px;">AK-Mart</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base bx bx-chevron-left"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)
    {{-- adding active and open class if child is active --}}

    {{-- menu headers --}}
    @if (isset($menu->menuHeader))
    <li class="menu-header small">
      <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
    </li>
    @else
    {{-- active menu method --}}
    @php
    $activeClass = null;
    $currentRouteName = Route::currentRouteName();

    if ($currentRouteName === $menu->slug) {
    $activeClass = 'active';
    } elseif (gettype($menu->slug) === 'array') {
    foreach ($menu->slug as $slug) {
    if (str_contains($currentRouteName, $slug) and strpos($currentRouteName, $slug) === 0) {
    $activeClass = isset($menu->submenu) ? 'active open' : 'active';
    }
    }
    } else {
    if (
    str_contains($currentRouteName, $menu->slug) and
    strpos($currentRouteName, $menu->slug) === 0
    ) {
    $activeClass = isset($menu->submenu) ? 'active open' : 'active';
    }
    }
    @endphp

    {{-- main menu --}}
    <li class="menu-item {{ $activeClass }}">
      <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and
        !empty($menu->target)) target="_blank" @endif>
        @isset($menu->icon)
        <i class="{{ $menu->icon }}"></i>
        @endisset
        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
        @isset($menu->badge)
        <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
        @endisset
      </a>

      {{-- submenu --}}
      @isset($menu->submenu)
      @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
      @endisset
    </li>
    @endif
    @endforeach
  </ul>

  <style>
    /* Upgraded Modern Sidebar Styling */
    #layout-menu {
      border-right: 1px solid rgba(67, 89, 113, 0.08);
      transition: all 0.25s ease-in-out;
    }
    #layout-menu .menu-inner {
      scrollbar-width: thin;
      scrollbar-color: rgba(105, 108, 255, 0.2) transparent;
      padding-bottom: 2rem;
    }
    #layout-menu .menu-inner::-webkit-scrollbar {
      width: 4px;
    }
    #layout-menu .menu-inner::-webkit-scrollbar-thumb {
      background: rgba(105, 108, 255, 0.2);
      border-radius: 10px;
    }
    #layout-menu .menu-header {
      margin-top: 1.25rem;
      margin-bottom: 0.4rem;
      padding-left: 1.25rem !important;
      padding-right: 1.25rem !important;
    }
    #layout-menu .menu-header-text {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: #8e94a9;
    }
    #layout-menu .menu-item .menu-link {
      margin: 0.15rem 0.75rem;
      padding: 0.6rem 0.9rem;
      border-radius: 0.5rem;
      font-weight: 500;
      font-size: 0.88rem;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #layout-menu .menu-item:not(.active) > .menu-link:hover {
      background-color: rgba(105, 108, 255, 0.06);
      color: #696cff;
      transform: translateX(4px);
    }
    #layout-menu .menu-inner > .menu-item.active:not(.open) > .menu-link {
      background: linear-gradient(135deg, #696cff 0%, #5b5ee6 100%) !important;
      color: #ffffff !important;
      box-shadow: 0 4px 14px rgba(105, 108, 255, 0.38) !important;
      font-weight: 600;
    }
    #layout-menu .menu-inner > .menu-item.active:not(.open) > .menu-link * {
      color: #ffffff !important;
    }
    #layout-menu .menu-inner > .menu-item.active.open > .menu-link {
      background-color: rgba(105, 108, 255, 0.08) !important;
      color: #696cff !important;
      font-weight: 600;
    }
    #layout-menu .menu-inner > .menu-item.active.open > .menu-link * {
      color: #696cff !important;
    }
    #layout-menu .menu-item .menu-icon {
      font-size: 1.2rem;
      margin-right: 0.75rem;
      transition: transform 0.2s ease;
    }
    #layout-menu .menu-item:hover .menu-icon {
      transform: scale(1.12);
    }
    #layout-menu .menu-sub .menu-link {
      padding-left: 2.75rem !important;
      font-size: 0.84rem;
      position: relative;
    }
    #layout-menu .menu-sub .menu-item.active > .menu-link {
      background-color: rgba(105, 108, 255, 0.14) !important;
      color: #696cff !important;
      box-shadow: none !important;
      font-weight: 600 !important;
      border-radius: 0.5rem;
    }
    #layout-menu .menu-sub .menu-item.active > .menu-link * {
      color: #696cff !important;
      font-weight: 600 !important;
    }
    #layout-menu .menu-sub .menu-item.active > .menu-link::before {
      background-color: #696cff !important;
      border-color: #696cff !important;
      box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.25) !important;
    }
    #layout-menu .menu-sub .menu-item:not(.active) > .menu-link:hover {
      background-color: rgba(105, 108, 255, 0.06) !important;
      color: #696cff !important;
    }
    #layout-menu .menu-sub .menu-item:not(.active) > .menu-link:hover * {
      color: #696cff !important;
    }
    #layout-menu .app-brand {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid rgba(67, 89, 113, 0.06);
    }
  </style>

</aside>