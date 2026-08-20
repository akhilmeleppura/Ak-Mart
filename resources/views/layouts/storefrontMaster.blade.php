<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'fa', 'ur', 'he']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'AK-Mart — Fresh Groceries, Supermarket & Smart POS')</title>
    <meta name="description" content="@yield('meta_description', 'AK-Mart is your premium neighborhood supermarket and online grocery destination.')" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Google Fonts & Boxicons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Core Theme Styles -->
    @vite(['resources/assets/vendor/scss/core.scss', 'resources/assets/css/demo.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/ak-notifications.css') }}" />

    <style>
        :root {
            --ak-primary: #4F46E5;
            --ak-primary-hover: #4338CA;
            --ak-primary-light: #EEF2FF;
            --ak-primary-gradient: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            --ak-emerald: #10B981;
            --ak-emerald-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
            --ak-coral: #FF5A5F;
            --ak-coral-gradient: linear-gradient(135deg, #FF5A5F 0%, #FF7E40 100%);
            --ak-amber: #F59E0B;
            --ak-amber-gradient: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            --ak-purple: #8B5CF6;
            --ak-purple-gradient: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
            --ak-bg: #F8FAFC;
            --ak-surface: #FFFFFF;
            --ak-text-main: #0F172A;
            --ak-text-muted: #64748B;
            --ak-border: #E2E8F0;
            --ak-shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
            --ak-shadow-hover: 0 20px 35px -10px rgba(79, 70, 229, 0.16), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
            --ak-shadow-glow-emerald: 0 4px 20px rgba(16, 185, 129, 0.3);
            --ak-shadow-glow-coral: 0 4px 20px rgba(255, 90, 95, 0.3);
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--ak-bg);
            color: var(--ak-text-main);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.015em;
        }

        /* Top Announcement Ticker */
        .top-announcement-bar {
            background: linear-gradient(90deg, #0F172A 0%, #1E1B4B 50%, #0F172A 100%);
            color: #E2E8F0;
            font-size: 12px;
            font-weight: 600;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .top-announcement-bar a {
            color: #CBD5E1;
            text-decoration: none;
            transition: color 0.15s;
        }
        .top-announcement-bar a:hover {
            color: #38BDF8;
        }

        /* Frosted Glass Header */
        .store-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
        }

        /* Highlighted Search Bar */
        .search-container-box {
            position: relative;
        }
        .search-pill-input {
            border: 2px solid #E2E8F0;
            background: #F8FAFC;
            border-radius: 35px;
            padding: 11px 22px 11px 48px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            color: #0F172A;
        }
        .search-pill-input:focus {
            background: #FFFFFF;
            border-color: #4F46E5;
            box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.15);
            outline: none;
        }
        .search-btn-highlight {
            background: var(--ak-primary-gradient);
            color: #FFFFFF;
            border: none;
            border-radius: 30px;
            padding: 7px 18px;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.2s;
        }
        .search-btn-highlight:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
            color: #FFFFFF;
        }

        /* Action Buttons & Badges */
        .header-icon-pill {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: #334155;
            text-decoration: none;
            position: relative;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .header-icon-pill:hover {
            background: #FFFFFF;
            border-color: #4F46E5;
            color: #4F46E5;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.15);
        }
        .cart-pill-highlight {
            background: var(--ak-primary-gradient);
            color: #FFFFFF;
            border-radius: 30px;
            padding: 9px 20px;
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 18px rgba(79, 70, 229, 0.3);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cart-pill-highlight:hover {
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.45);
        }

        /* Colorful Category Navigation Chips */
        .category-chip-link {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            padding: 7px 16px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
        }
        .category-chip-link:hover, .category-chip-link.active {
            background: #EEF2FF;
            color: #4F46E5;
            border-color: #C7D2FE;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
        }

        /* High-Converting Promo Ribbon */
        .store-promo-ribbon {
            background: linear-gradient(90deg, #4F46E5 0%, #7C3AED 50%, #EC4899 100%);
            color: #FFFFFF;
            font-size: 12.5px;
            font-weight: 700;
            padding: 8px 0;
            text-align: center;
            letter-spacing: 0.02em;
        }

        /* Footer */
        .store-footer-premium {
            background: #090E1A;
            color: #94A3B8;
            padding: 64px 0 32px;
            margin-top: 80px;
            border-top: 1px solid #1E293B;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Announcement Bar with Live Highlights -->
    <div class="top-announcement-bar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bx bxs-map-pin text-primary me-1"></i> {{ __('Store Location') }}: <strong class="text-white">Main Branch Central</strong></span>
                <span class="d-none d-md-inline text-muted">•</span>
                <span class="d-none d-md-inline"><i class="bx bxs-phone text-warning me-1"></i> +1 (800) 555-AKMART</span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4);">
                    <i class="bx bxs-zap me-1"></i> {{ __('30-Min Delivery Active') }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('storefront.track') }}" class="small"><i class="bx bx-package me-1 text-primary"></i>{{ __('Track Order') }}</a>
                @if(Auth::check())
                    <a href="{{ route('customer.dashboard') }}" class="small text-white fw-bold"><i class="bx bx-user-circle text-success me-1"></i>{{ Auth::user()->name }}</a>
                @else
                    <a href="{{ route('login') }}" class="small"><i class="bx bx-lock-alt me-1"></i>{{ __('Sign In') }}</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Free Delivery Promotion Ribbon -->
    <div class="store-promo-ribbon">
        <div class="container d-flex justify-content-center align-items-center gap-2">
            <span>🎉 <strong>{{ __('SUPER SAVINGS:') }}</strong> {{ __('Use code') }} <span class="badge bg-white text-dark rounded-pill px-2 py-0.5 font-monospace">WELCOME10</span> {{ __('for $5.00 OFF + Free Express Delivery on orders over $35!') }}</span>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="store-header py-3">
        <div class="container d-flex justify-content-between align-items-center gap-3">
            <!-- Brand Logo -->
            <a href="{{ route('storefront.home') }}" class="d-flex align-items-center gap-2.5 text-decoration-none">
                <div class="p-2 rounded-4 shadow-xs" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); border: 1px solid #C7D2FE;">
                    <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart" height="42" onerror="this.src='{{ asset('assets/img/favicon/favicon.ico') }}'">
                </div>
                <div class="d-flex flex-column">
                    <span class="fs-3 fw-bolder lh-1" style="background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">AK-Mart</span>
                    <span class="text-muted" style="font-size: 10px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase;">Smart Supermarket</span>
                </div>
            </a>

            <!-- Search Bar with Live Suggestions Dropdown -->
            <div class="position-relative flex-grow-1 mx-3 d-none d-md-block" style="max-width: 600px;">
                <form action="{{ route('storefront.shop') }}" method="GET">
                    <div class="search-container-box">
                        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-3.5 fs-5 text-primary"></i>
                        <input type="text" name="q" id="storeSearchInput" class="form-control search-pill-input pe-5" placeholder="{{ __('Search 5,000+ groceries, organic produce, dairy, snacks...') }}" value="{{ request('q') }}" autocomplete="off">
                        <button class="search-btn-highlight position-absolute top-50 end-0 translate-middle-y me-1.5" type="submit">
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
                <div id="searchSuggestionsBox" class="position-absolute start-0 w-100 bg-white border rounded-4 shadow-lg p-2 d-none" style="top: 115%; z-index: 1050; max-height: 380px; overflow-y: auto;"></div>
            </div>

            <!-- Action Buttons: Compare, Wishlist & Cart -->
            <div class="d-flex align-items-center gap-2.5">
                @php $compCount = count(session('compare_list', [])); @endphp
                <a href="{{ route('storefront.compare') }}" class="header-icon-pill" id="headerCompareBtn" title="{{ __('Product Comparison') }}">
                    <i class="bx bx-git-compare fs-5 align-middle text-primary"></i>
                    <span class="badge bg-primary rounded-pill position-absolute top-0 start-100 translate-middle" id="compareBadge" style="{{ $compCount > 0 ? '' : 'display:none;' }}">
                        {{ $compCount }}
                    </span>
                </a>

                @php $wishCount = count(session('wishlist', [])); @endphp
                <a href="{{ route('storefront.wishlist') }}" class="header-icon-pill" id="headerWishlistBtn" title="{{ __('My Wishlist') }}">
                    <i class="bx bx-heart fs-5 align-middle text-danger"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="wishlistBadge" style="{{ $wishCount > 0 ? '' : 'display:none;' }}">
                        {{ $wishCount }}
                    </span>
                </a>
                
                @php $cartCount = array_sum(array_column(session('cart', []), 'qty')); @endphp
                <a href="{{ route('storefront.cart') }}" class="cart-pill-highlight">
                    <i class="bx bx-shopping-bag fs-5"></i>
                    <span class="d-none d-sm-inline">{{ __('Cart') }}</span>
                    <span class="badge bg-white text-primary rounded-pill fw-bolder px-2 py-0.5" id="cartBadge">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar: Colorful Aisle Chips -->
    <nav class="bg-white border-bottom py-2.5 shadow-xs d-none d-md-block" style="background: rgba(255,255,255,0.98);">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('storefront.home') }}" class="category-chip-link {{ request()->routeIs('storefront.home') ? 'active' : '' }}">
                    <i class="bx bx-home-alt text-primary"></i> {{ __('Home') }}
                </a>
                <a href="{{ route('storefront.shop') }}" class="category-chip-link {{ request()->routeIs('storefront.shop') && !request('category') ? 'active' : '' }}">
                    <i class="bx bx-grid-alt text-primary"></i> {{ __('All Catalog') }}
                </a>
                <a href="{{ route('storefront.buy_again') }}" class="category-chip-link text-primary fw-bold" style="background: #EEF2FF; border-color: #C7D2FE;">
                    <i class="bx bx-repeat"></i> {{ __('Buy Again') }}
                </a>
                <a href="{{ route('storefront.referral') }}" class="category-chip-link text-success fw-bold" style="background: #ECFDF5; border-color: #A7F3D0;">
                    <i class="bx bx-gift"></i> {{ __('Refer & Earn $10') }}
                </a>
                <a href="{{ route('storefront.shop', ['category' => 1]) }}" class="category-chip-link {{ request('category') == 1 ? 'active' : '' }}">
                    🍎 {{ __('Groceries') }}
                </a>
                <a href="{{ route('storefront.shop', ['category' => 2]) }}" class="category-chip-link {{ request('category') == 2 ? 'active' : '' }}">
                    🥤 {{ __('Beverages') }}
                </a>
                <a href="{{ route('storefront.shop', ['category' => 3]) }}" class="category-chip-link {{ request('category') == 3 ? 'active' : '' }}">
                    🧀 {{ __('Dairy & Eggs') }}
                </a>
                <a href="{{ route('storefront.shop', ['category' => 4]) }}" class="category-chip-link {{ request('category') == 4 ? 'active' : '' }}">
                    🥐 {{ __('Fresh Bakery') }}
                </a>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="badge rounded-pill text-decoration-none px-3 py-2 fw-bold" style="background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;">
                    <i class="bx bx-slider me-1"></i>{{ __('Admin Portal') }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4">
        @if(session('success'))
            <div class="container mb-3">
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert" style="background: #ECFDF5; color: #065F46; border-left: 5px solid #10B981 !important;">
                    <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="container mb-3">
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert" style="background: #FEF2F2; color: #991B1B; border-left: 5px solid #EF4444 !important;">
                    <i class="bx bx-error-circle me-1 fs-5 align-middle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Store Footer -->
    <footer class="store-footer-premium">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="p-2 rounded-3 bg-primary bg-opacity-20 text-primary">
                            <i class="bx bx-store-alt fs-4"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-0">AK-Mart</h4>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Smart Management for Modern Stores. Automated mini-mart inventory, real-time POS checkouts, and express online delivery.') }}</p>
                    <p class="small text-muted mb-0">&copy; {{ date('Y') }} AK-Mart. {{ __('Built with pride by Akhil Meleppura.') }}</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">{{ __('Shop Categories') }}</h6>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-2">
                        <li><a href="{{ route('storefront.shop') }}" class="text-muted text-decoration-none">{{ __('All Products') }}</a></li>
                        <li><a href="{{ route('storefront.shop', ['category' => 1]) }}" class="text-muted text-decoration-none">{{ __('Fresh Produce') }}</a></li>
                        <li><a href="{{ route('storefront.shop', ['category' => 2]) }}" class="text-muted text-decoration-none">{{ __('Beverages') }}</a></li>
                        <li><a href="{{ route('storefront.shop', ['category' => 4]) }}" class="text-muted text-decoration-none">{{ __('Bakery') }}</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">{{ __('Customer Care') }}</h6>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-2">
                        <li><a href="{{ route('storefront.track') }}" class="text-muted text-decoration-none">{{ __('Track Order') }}</a></li>
                        <li><a href="{{ route('storefront.returns') }}" class="text-primary text-decoration-none fw-semibold"><i class="bx bx-revision me-1"></i>{{ __('Returns & Exchanges') }}</a></li>
                        <li><a href="{{ route('storefront.referral') }}" class="text-success text-decoration-none fw-semibold"><i class="bx bx-gift me-1"></i>{{ __('Refer & Earn $10') }}</a></li>
                        <li><a href="{{ route('customer.dashboard') }}" class="text-muted text-decoration-none">{{ __('My Account') }}</a></li>
                        <li><a href="{{ route('customer.wishlist') }}" class="text-muted text-decoration-none">{{ __('Wishlist') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-bold mb-3">{{ __('Newsletter & Deals') }}</h6>
                    <p class="small text-muted">{{ __('Subscribe to receive weekly store flyers, exclusive coupon codes, and restock alerts.') }}</p>
                    <form action="{{ route('storefront.newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="email" name="email" class="form-control rounded-pill bg-dark border-secondary text-white px-3" placeholder="{{ __('Your email address') }}" required>
                        <button class="btn btn-primary rounded-pill px-3 fw-bold" type="submit">{{ __('Subscribe') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function quickAddToCart(productId) {
            fetch('{{ route("storefront.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, qty: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.cartCount !== undefined) {
                    const badge = document.getElementById('cartBadge');
                    if (badge) {
                        badge.textContent = data.cartCount;
                        badge.classList.add('animate__animated', 'animate__rubberBand');
                    }
                }
                showNotification(data.message || 'Product added to cart!', 'success');
            })
            .catch(err => {
                console.error(err);
                showNotification('Could not add product to cart.', 'error');
            });
        }

        function quickToggleWishlist(productId, btn, event) {
            if (event) event.stopPropagation();
            fetch('{{ route("storefront.wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                const icon = btn.querySelector('i');
                if (data.status === 'added') {
                    icon.className = 'bx bxs-heart text-danger fs-5 align-middle';
                    showNotification('Saved to your Wishlist ❤️', 'success');
                } else {
                    icon.className = 'bx bx-heart text-muted fs-5 align-middle';
                    showNotification('Removed from Wishlist', 'info');
                }
                const badge = document.getElementById('wishlistBadge');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? '' : 'none';
                }
            })
            .catch(err => console.error(err));
        }

        function quickToggleCompare(productId, btn, event) {
            if (event) event.stopPropagation();
            fetch('{{ route("storefront.compare.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                const icon = btn.querySelector('i');
                if (data.status === 'added') {
                    icon.className = 'bx bx-git-compare text-primary fw-bold fs-5 align-middle';
                    showNotification(data.message, 'success');
                } else {
                    icon.className = 'bx bx-git-compare text-muted fs-5 align-middle';
                    showNotification(data.message, 'info');
                }
                const badge = document.getElementById('compareBadge');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? '' : 'none';
                }
            })
            .catch(err => console.error(err));
        }

        // Live Search Autocomplete Debounce
        let searchTimeout;
        const searchInput = document.getElementById('storeSearchInput');
        const suggestionsBox = document.getElementById('searchSuggestionsBox');

        if (searchInput && suggestionsBox) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                if (q.length < 2) {
                    suggestionsBox.classList.add('d-none');
                    suggestionsBox.innerHTML = '';
                    return;
                }
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('storefront.search_suggestions') }}?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.suggestions && data.suggestions.length > 0) {
                                let html = '<div class="list-group list-group-flush">';
                                data.suggestions.forEach(item => {
                                    html += `
                                        <a href="/store/product/${item.id}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-2 rounded-3 border-0">
                                            <img src="${item.image ? '/' + item.image : '/assets/img/illustrations/boy-with-rocket-light.png'}" width="40" height="40" class="rounded object-fit-contain bg-light p-1">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-dark fw-bold small">${item.name}</h6>
                                                <span class="text-primary fw-bold small">$${parseFloat(item.price).toFixed(2)}</span>
                                            </div>
                                            <i class="bx bx-chevron-right text-muted"></i>
                                        </a>
                                    `;
                                });
                                html += '</div>';
                                suggestionsBox.innerHTML = html;
                                suggestionsBox.classList.remove('d-none');
                            } else {
                                suggestionsBox.innerHTML = '<div class="p-3 text-center text-muted small">No items found matching your search.</div>';
                                suggestionsBox.classList.remove('d-none');
                            }
                        });
                }, 250);
            });

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.classList.add('d-none');
                }
            });
        }

        function showNotification(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : (type === 'info' ? 'info' : 'success')} position-fixed bottom-0 end-0 m-4 rounded-4 shadow-lg d-flex align-items-center gap-2 p-3`;
            toast.style.zIndex = 9999;
            toast.innerHTML = `<i class="bx ${type === 'error' ? 'bx-error-circle' : 'bx-check-circle'} fs-4"></i><span class="fw-bold">${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3200);
        }
    </script>
    @yield('scripts')
</body>
</html>
