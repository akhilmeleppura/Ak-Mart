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
          <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ url('lang/fr') }}"
            data-language="fr" data-text-direction="ltr">
            <span>French</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ url('lang/ar') }}"
            data-language="ar" data-text-direction="rtl">
            <span>Arabic</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="{{ url('lang/de') }}"
            data-language="de" data-text-direction="ltr">
            <span>German</span>
          </a>
        </li>
      </ul>
    </li>
    <!--/ Language -->
    
    <!-- Branch Switcher -->
    <li class="nav-item dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base bx bx-store icon-md"></i>
        <span class="d-none d-md-inline-block ms-1">{{ App\Models\Branch\Branch::find(session('branch_id'))?->name ?? 'Select Branch' }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        @foreach(App\Models\Branch\Branch::all() as $branch)
        <li>
          <a class="dropdown-item {{ session('branch_id') == $branch->id ? 'active' : '' }}" href="{{ route('branch-swap', $branch->id) }}">
            <span>{{ $branch->name }}</span>
          </a>
        </li>
        @endforeach
      </ul>
    </li>
    <!--/ Branch Switcher -->

    @if ($configData['hasCustomizer'] == true)
    <!-- Style Switcher -->
    <li class="nav-item dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
        <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
        <li>
          <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
            aria-pressed="false">
            <span><i class="icon-base bx bx-sun icon-md me-3" data-icon="sun"></i>Light</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark" aria-pressed="true">
            <span><i class="icon-base bx bx-moon icon-md me-3" data-icon="moon"></i>Dark</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
            aria-pressed="false">
            <span><i class="icon-base bx bx-desktop icon-md me-3" data-icon="desktop"></i>System</span>
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
            <h6 class="mb-0 me-auto">Shortcuts</h6>
            <a href="javascript:void(0)" class="dropdown-shortcuts-add py-2" data-bs-toggle="tooltip"
              data-bs-placement="top" title="Add shortcuts"><i class="icon-base bx bx-plus-circle text-heading"></i></a>
          </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-calendar icon-26px text-heading"></i>
              </span>
              <a href="{{ url('app/calendar') }}" class="stretched-link">Calendar</a>
              <small>Appointments</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-food-menu icon-26px text-heading"></i>
              </span>
              <a href="{{ url('app/invoice/list') }}" class="stretched-link">Invoice App</a>
              <small>Manage Accounts</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-user icon-26px text-heading"></i>
              </span>
              <a href="{{ url('app/user/list') }}" class="stretched-link">User App</a>
              <small>Manage Users</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-check-shield icon-26px text-heading"></i>
              </span>
              <a href="{{ url('app/access-roles') }}" class="stretched-link">Role Management</a>
              <small>Permission</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-pie-chart-alt-2 icon-26px text-heading"></i>
              </span>
              <a href="{{ url('/') }}" class="stretched-link">Dashboard</a>
              <small>User Dashboard</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-cog icon-26px text-heading"></i>
              </span>
              <a href="{{ url('pages/account-settings-account') }}" class="stretched-link">Setting</a>
              <small>Account Settings</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-help-circle icon-26px text-heading"></i>
              </span>
              <a href="{{ url('pages/faq') }}" class="stretched-link">FAQs</a>
              <small>FAQs & Articles</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base bx bx-window-open icon-26px text-heading"></i>
              </span>
              <a href="{{ url('modal-examples') }}" class="stretched-link">Modals</a>
              <small>Useful Popups</small>
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
            <h6 class="mb-0 me-auto">Notification</h6>
            <div class="d-flex align-items-center h6 mb-0">
              <span class="badge bg-label-primary me-2">{{ auth()->check() ? auth()->user()->unreadNotifications->count() : 0 }} New</span>
              <form action="{{ route('app-notifications-mark-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read">
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
                    <h6 class="small mb-0">{{ $notification->data['title'] ?? 'New Alert' }}</h6>
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
              <li class="list-group-item text-center py-4 text-muted small">No new notifications</li>
              @endforelse
            @else
              <li class="list-group-item text-center py-4 text-muted small">Please login to see notifications</li>
            @endif
          </ul>
        </li>
        <li class="border-top">
          <div class="d-grid p-4">
            <a class="btn btn-primary btn-sm d-flex" href="{{ route('app-notifications') }}">
              <small class="align-middle">View all notifications</small>
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
          <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}" alt
            class="rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item"
            href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                    alt class="w-px-40 h-auto rounded-circle" />
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
                <small class="text-body-secondary">Admin</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <a class="dropdown-item"
            href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
            <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
          </a>
        </li>
        @if (Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures())
        <li>
          <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
            <i class="icon-base bx bx-key icon-md me-3"></i><span>API Tokens</span>
          </a>
        </li>
        @endif
        <li>
          <a class="dropdown-item" href="{{ url('pages/account-settings-billing') }}">
            <span class="d-flex align-items-center align-middle">
              <i class="flex-shrink-0 icon-base bx bx-credit-card icon-md me-3"></i>
              <span class="flex-grow-1 align-middle">Billing Plan</span>
              <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
            </span>
          </a>
        </li>
        @if (Auth::user() && Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <h6 class="dropdown-header">Manage Team</h6>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <a class="dropdown-item"
            href="{{ (Auth::user() && Auth::user()->currentTeam) ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
            <i class="icon-base bx bx-cog icon-md me-3"></i><span>Team Settings</span>
          </a>
        </li>
        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
        <li>
          <a class="dropdown-item" href="{{ route('teams.create') }}">
            <i class="icon-base bx bx-user icon-md me-3"></i><span>Create New Team</span>
          </a>
        </li>
        @endcan
        @if (Auth::user() && Auth::user()->allTeams()->count() > 1)
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <h6 class="dropdown-header">Switch Teams</h6>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        @endif
        @if (Auth::user())
        @foreach (Auth::user()->allTeams() as $team)
        {{-- Below commented code read by artisan command while installing jetstream. !! Do not remove if you want to use jetstream. --}}

        <x-switchable-team :team="$team" />
        @endforeach
        @endif
        @endif
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        @if (Auth::check())
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Logout</span>
          </a>
        </li>
        <form method="POST" id="logout-form" action="{{ route('logout') }}">
          @csrf
        </form>
        @else
        <li>
          <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
            <i class="icon-base bx bx-log-in icon-md me-3"></i><span>Login</span>
          </a>
        </li>
        @endif
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>
