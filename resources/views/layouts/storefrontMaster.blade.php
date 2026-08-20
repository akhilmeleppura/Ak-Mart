<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'fa', 'ur', 'he']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'AK-Mart — Smart Mini-Mart & Online Store')</title>
    <meta name="description" content="@yield('meta_description', 'AK-Mart is your premium mini-mart and online grocery destination.')" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Google Fonts & Boxicons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Core Theme Styles -->
    @vite(['resources/assets/vendor/scss/core.scss', 'resources/assets/css/demo.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/ak-notifications.css') }}" />

    <style>
        :root {
            --ak-primary: #4F46E5;
            --ak-primary-hover: #4338CA;
            --ak-primary-light: #EEF2FF;
            --ak-emerald: #10B981;
            --ak-emerald-light: #ECFDF5;
            --ak-amber: #F59E0B;
            --ak-amber-light: #FFFBEB;
            --ak-rose: #F43F5E;
            --ak-bg: #F8FAFC;
            --ak-surface: #FFFFFF;
            --ak-text: #0F172A;
            --ak-text-muted: #64748B;
            --ak-border: #E2E8F0;
            --ak-radius: 16px;
            --ak-shadow-sm: 0 1px 3px 0 rgba(0,0,0,0.04), 0 1px 2px -1px rgba(0,0,0,0.04);
            --ak-shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            --ak-shadow-hover: 0 20px 30px -10px rgba(79, 70, 229, 0.12), 0 10px 15px -5px rgba(0, 0, 0, 0.04);
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--ak-bg);
            color: var(--ak-text);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
        }
        .store-header {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.2s ease;
        }
        .top-bar {
            background: #0F172A;
            color: #94A3B8;
            font-size: 12px;
            font-weight: 500;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .top-bar a {
            color: #CBD5E1;
            transition: color 0.15s;
        }
        .top-bar a:hover {
            color: #FFFFFF;
        }
        
        /* Modern Card Styling */
        .product-card {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            box-shadow: var(--ak-shadow-card);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--ak-shadow-hover);
            border-color: rgba(79, 70, 229, 0.3);
        }
        .product-img-wrap {
            position: relative;
            background: radial-gradient(circle at center, #F8FAFC 0%, #F1F5F9 100%);
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            overflow: hidden;
            transition: background 0.3s;
        }
        .product-img-wrap img {
            max-height: 80%;
            max-width: 80%;
            object-fit: contain;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .product-card:hover .product-img-wrap img {
            transform: scale(1.08);
        }
        .btn-action-round {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-action-round:hover {
            background: #FFFFFF;
            transform: scale(1.12);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }
        .btn-gradient-primary {
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            color: #FFFFFF;
            border: none;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
            transition: all 0.25s ease;
        }
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #4338CA 0%, #2563EB 100%);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
        }
        .badge-pill-soft {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .store-footer {
            background: #0B1120;
            color: #94A3B8;
            padding: 56px 0 28px;
            margin-top: 72px;
            border-top: 1px solid #1E293B;
        }
        .nav-category-pill {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            padding: 6px 14px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .nav-category-pill:hover, .nav-category-pill.active {
            background: #EEF2FF;
            color: #4F46E5;
        }
        .search-pill-input {
            border: 1px solid #E2E8F0;
            background: #F8FAFC;
            border-radius: 30px;
            padding: 10px 20px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .search-pill-input:focus {
            background: #FFFFFF;
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
            outline: none;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bx bxs-map-pin text-primary me-1"></i> {{ __('Store Location') }}: <strong class="text-white">Main Branch Central</strong></span>
                <span class="d-none d-md-inline text-muted">|</span>
                <span class="d-none d-md-inline"><i class="bx bxs-phone me-1"></i> +1 (800) 555-AKMART</span>
                <span class="d-none d-lg-inline badge bg-emerald-500 bg-opacity-20 text-success rounded-pill px-2 py-0.5" style="background: rgba(16,185,129,0.15); color: #10B981;"><i class="bx bx-check-circle me-1"></i>{{ __('Open for Instant Delivery') }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('storefront.track') }}" class="text-decoration-none small"><i class="bx bx-package me-1"></i>{{ __('Track Order') }}</a>
                @if(Auth::check())
                    <a href="{{ route('customer.dashboard') }}" class="text-decoration-none small"><i class="bx bx-user-circle me-1"></i>{{ Auth::user()->name }}</a>
                @else
                    <a href="{{ route('login') }}" class="text-decoration-none small"><i class="bx bx-lock-alt me-1"></i>{{ __('Sign In') }}</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="store-header py-2.5">
        <div class="container d-flex justify-content-between align-items-center gap-3">
            <!-- Brand Logo -->
            <a href="{{ route('storefront.home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="p-1.5 rounded-3" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                    <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart" height="38" onerror="this.src='{{ asset('assets/img/favicon/favicon.ico') }}'">
                </div>
                <div class="d-flex flex-column">
                    <span class="fs-4 fw-bolder lh-1" style="background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">AK-Mart</span>
                    <span class="text-muted" style="font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Supermarket &amp; POS</span>
                </div>
            </a>

            <!-- Search Bar with Live Suggestions Dropdown -->
            <div class="position-relative flex-grow-1 mx-3 d-none d-md-block" style="max-width: 580px;">
                <form action="{{ route('storefront.shop') }}" method="GET">
                    <div class="position-relative">
                        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-3 fs-5 text-muted"></i>
                        <input type="text" name="q" id="storeSearchInput" class="form-control search-pill-input ps-5 pe-5" placeholder="{{ __('Search 5,000+ groceries, organic produce, dairy, snacks...') }}" value="{{ request('q') }}" autocomplete="off">
                        <button class="btn btn-sm btn-gradient-primary rounded-pill position-absolute top-50 end-0 translate-middle-y me-1.5 px-3 py-1 fw-semibold" type="submit">
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
                <div id="searchSuggestionsBox" class="position-absolute start-0 w-100 bg-white border rounded-4 shadow-lg p-2 d-none" style="top: 110%; z-index: 1050; max-height: 360px; overflow-y: auto;"></div>
            </div>

            <!-- Action Buttons: Compare, Wishlist & Cart -->
            <div class="d-flex align-items-center gap-2">
                @php $compCount = count(session('compare_list', [])); @endphp
                <a href="{{ route('storefront.compare') }}" class="btn btn-light rounded-pill position-relative p-2 px-2.5 border" id="headerCompareBtn" title="{{ __('Product Comparison') }}" style="background: #F8FAFC;">
                    <i class="bx bx-git-compare fs-5 align-middle text-primary"></i>
                    <span class="badge bg-primary rounded-pill position-absolute top-0 start-100 translate-middle" id="compareBadge" style="{{ $compCount > 0 ? '' : 'display:none;' }}">
                        {{ $compCount }}
                    </span>
                </a>

                @php $wishCount = count(session('wishlist', [])); @endphp
                <a href="{{ route('storefront.wishlist') }}" class="btn btn-light rounded-pill position-relative p-2 px-2.5 border" id="headerWishlistBtn" title="{{ __('My Wishlist') }}" style="background: #F8FAFC;">
                    <i class="bx bx-heart fs-5 align-middle text-danger"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="wishlistBadge" style="{{ $wishCount > 0 ? '' : 'display:none;' }}">
                        {{ $wishCount }}
                    </span>
                </a>
                
                @php $cartCount = array_sum(array_column(session('cart', []), 'qty')); @endphp
                <a href="{{ route('storefront.cart') }}" class="btn btn-gradient-primary rounded-pill position-relative px-3.5 py-2 d-flex align-items-center gap-2">
                    <i class="bx bx-cart fs-5"></i>
                    <span class="d-none d-sm-inline fw-bold small">{{ __('Cart') }}</span>
                    <span class="badge bg-white text-primary rounded-pill fw-bolder px-2" id="cartBadge">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar: Aisles & Fast Navigation -->
    <nav class="bg-white border-bottom py-2 shadow-xs d-none d-md-block" style="background: rgba(255,255,255,0.98);">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <a href="{{ route('storefront.home') }}" class="nav-category-pill {{ request()->routeIs('storefront.home') ? 'active' : '' }}"><i class="bx bx-home-alt text-primary"></i> {{ __('Home') }}</a>
                <a href="{{ route('storefront.shop') }}" class="nav-category-pill {{ request()->routeIs('storefront.shop') && !request('category') ? 'active' : '' }}"><i class="bx bx-grid-alt text-primary"></i> {{ __('All Catalog') }}</a>
                <a href="{{ route('storefront.buy_again') }}" class="nav-category-pill text-primary fw-bold" style="background: #EEF2FF;"><i class="bx bx-repeat"></i> {{ __('Buy Again') }}</a>
                <a href="{{ route('storefront.referral') }}" class="nav-category-pill text-success fw-bold" style="background: #ECFDF5;"><i class="bx bx-gift"></i> {{ __('Refer & Earn $10') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 1]) }}" class="nav-category-pill {{ request('category') == 1 ? 'active' : '' }}">🍎 {{ __('Groceries') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 2]) }}" class="nav-category-pill {{ request('category') == 2 ? 'active' : '' }}">🥤 {{ __('Beverages') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 3]) }}" class="nav-category-pill {{ request('category') == 3 ? 'active' : '' }}">🧀 {{ __('Dairy & Eggs') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 4]) }}" class="nav-category-pill {{ request('category') == 4 ? 'active' : '' }}">🥐 {{ __('Fresh Bakery') }}</a>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="badge rounded-pill text-decoration-none px-3 py-1.5 fw-semibold" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;"><i class="bx bx-slider me-1"></i>{{ __('Admin Portal') }}</a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4">
        @if(session('success'))
            <div class="container mb-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="container mb-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Store Footer -->
    <footer class="store-footer">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <h4 class="text-white fw-bold mb-3">AK-Mart</h4>
                    <p class="text-muted small mb-3">{{ __('Smart Management for Modern Stores. Automated mini-mart inventory, real-time POS checkouts, and express online delivery.') }}</p>
                    <p class="small text-muted mb-0">&copy; {{ date('Y') }} AK-Mart. {{ __('Built with pride by Akhil Meleppura.') }}</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-semibold mb-3">{{ __('Shop Categories') }}</h6>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-2">
                        <li><a href="{{ route('storefront.shop') }}" class="text-muted text-decoration-none">{{ __('All Products') }}</a></li>
                        <li><a href="{{ route('storefront.shop') }}" class="text-muted text-decoration-none">{{ __('Fresh Produce') }}</a></li>
                        <li><a href="{{ route('storefront.shop') }}" class="text-muted text-decoration-none">{{ __('Beverages') }}</a></li>
                        <li><a href="{{ route('storefront.shop') }}" class="text-muted text-decoration-none">{{ __('Household Items') }}</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-semibold mb-3">{{ __('Customer Care') }}</h6>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-2">
                        <li><a href="{{ route('storefront.track') }}" class="text-muted text-decoration-none">{{ __('Track Order') }}</a></li>
                        <li><a href="{{ route('storefront.returns') }}" class="text-primary text-decoration-none fw-semibold"><i class="bx bx-revision me-1"></i>{{ __('Returns & Exchanges') }}</a></li>
                        <li><a href="{{ route('storefront.referral') }}" class="text-success text-decoration-none fw-semibold"><i class="bx bx-gift me-1"></i>{{ __('Refer & Earn $10') }}</a></li>
                        <li><a href="{{ route('customer.dashboard') }}" class="text-muted text-decoration-none">{{ __('My Account') }}</a></li>
                        <li><a href="{{ route('customer.wishlist') }}" class="text-muted text-decoration-none">{{ __('Wishlist') }}</a></li>
                        <li><a href="{{ route('customer.wallet') }}" class="text-muted text-decoration-none">{{ __('Wallet & Loyalty') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-semibold mb-3">{{ __('Newsletter & Deals') }}</h6>
                    <p class="small text-muted">{{ __('Subscribe to receive weekly store flyers, exclusive coupon codes, and restock alerts.') }}</p>
                    <form action="{{ route('storefront.newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="email" name="email" class="form-control rounded-pill" placeholder="{{ __('Your email address') }}" required>
                        <button class="btn btn-primary rounded-pill px-3" type="submit">{{ __('Subscribe') }}</button>
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
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('cartBadge');
                    if (badge) badge.textContent = data.totalItems;
                    alert(data.message);
                }
            });
        }

        // Live Search Autocomplete Debounce
        const searchInput = document.getElementById('storeSearchInput');
        const suggestionsBox = document.getElementById('searchSuggestionsBox');
        let searchTimeout = null;

        if (searchInput && suggestionsBox) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                if (q.length < 2) {
                    suggestionsBox.classList.add('d-none');
                    return;
                }
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('storefront.search.suggestions') }}?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.suggestions && data.suggestions.length > 0) {
                                suggestionsBox.innerHTML = data.suggestions.map(p => `
                                    <a href="/store/product/${p.id}" class="d-flex align-items-center gap-3 p-2 rounded text-decoration-none text-dark hover-bg">
                                        <img src="${p.image ? '/' + p.image : '/assets/img/illustrations/boy-with-rocket-light.png'}" width="38" height="38" class="rounded object-fit-contain bg-light p-1">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small text-truncate" style="max-width: 320px;">${p.name}</div>
                                            <small class="text-primary fw-bold">$${parseFloat(p.price).toFixed(2)}</small>
                                            <span class="badge ${p.qty > 0 ? 'bg-label-success' : 'bg-label-danger'} ms-2" style="font-size: 10px;">${p.qty > 0 ? 'In Stock' : 'Out'}</span>
                                        </div>
                                    </a>
                                `).join('');
                                suggestionsBox.classList.remove('d-none');
                            } else {
                                suggestionsBox.innerHTML = '<div class="p-3 text-center text-muted small">No products found.</div>';
                                suggestionsBox.classList.remove('d-none');
                            }
                        });
                }, 250);
            });
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.classList.add('d-none');
                }
            });
        }

        // Toast Notification Helper
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('storeToastContainer');
            if (!toastContainer) return;
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'primary'} shadow-lg rounded-pill px-4 py-2 mb-2 d-flex align-items-center gap-2 fade show`;
            toast.style.cssText = 'pointer-events: auto; min-width: 260px; transition: all 0.3s ease;';
            toast.innerHTML = `<i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-info-circle'} fs-5"></i> <span class="fw-semibold small">${message}</span>`;
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Quick Wishlist Toggle Function
        function quickToggleWishlist(productId, btn, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            fetch('{{ route("storefront.wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('wishlistBadge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            if (data.added) {
                                icon.className = 'bx bxs-heart text-danger fs-5 align-middle';
                            } else {
                                icon.className = 'bx bx-heart text-muted fs-5 align-middle';
                            }
                        }
                    }
                    showToast(data.message, data.added ? 'success' : 'info');
                }
            });
        }

        function quickToggleCompare(productId, btn, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            fetch('{{ route("storefront.compare.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(r => r.json().then(data => ({ status: r.status, body: data })))
            .then(({ status, body }) => {
                if (body.success) {
                    const badge = document.getElementById('compareBadge');
                    if (badge) {
                        badge.textContent = body.count;
                        badge.style.display = body.count > 0 ? 'inline-block' : 'none';
                    }
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            if (body.added) {
                                icon.className = 'bx bx-git-compare text-primary fw-bold fs-4 align-middle';
                            } else {
                                icon.className = 'bx bx-git-compare text-muted fs-4 align-middle';
                            }
                        }
                    }
                    showToast(body.message, body.added ? 'primary' : 'secondary');
                } else {
                    showToast(body.message || 'Comparison limit reached.', 'primary');
                }
            });
        }
    </script>

    <!-- Global Floating Toast Container -->
    <div id="storeToastContainer" class="position-fixed bottom-0 end-0 p-4" style="z-index: 1100; pointer-events: none;"></div>

    @yield('scripts')
</body>
</html>

