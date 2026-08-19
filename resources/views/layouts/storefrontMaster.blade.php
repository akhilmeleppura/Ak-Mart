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
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Core Theme Styles -->
    @vite(['resources/assets/vendor/scss/core.scss', 'resources/assets/css/demo.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/ak-notifications.css') }}" />

    <style>
        :root {
            --ak-primary: #2563EB;
            --ak-primary-dark: #1D4ED8;
            --ak-accent: #14B8A6;
            --ak-bg: #F8FAFC;
            --ak-card-bg: #FFFFFF;
        }
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--ak-bg);
            color: #0F172A;
        }
        .store-header {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .top-bar {
            background: #0F172A;
            color: #94A3B8;
            font-size: 12.5px;
            padding: 6px 0;
        }
        .product-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.25s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.08);
            border-color: #CBD5E1;
        }
        .product-img-wrap {
            position: relative;
            background: #F1F5F9;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img-wrap img {
            max-height: 85%;
            max-width: 85%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img-wrap img {
            transform: scale(1.06);
        }
        .store-footer {
            background: #0F172A;
            color: #94A3B8;
            padding: 48px 0 24px;
            margin-top: 64px;
        }
        .badge-stock {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="bx bx-map-pin text-primary me-1"></i> {{ __('Store Location') }}: <strong>Main Branch Central</strong> | <i class="bx bx-phone ms-2 me-1"></i> +1 (800) 555-AKMART
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('storefront.track') }}" class="text-white text-decoration-none small"><i class="bx bx-package me-1"></i>{{ __('Track Order') }}</a>
                @if(Auth::check())
                    <a href="{{ route('customer.dashboard') }}" class="text-white text-decoration-none small"><i class="bx bx-user me-1"></i>{{ Auth::user()->name }}</a>
                @else
                    <a href="{{ route('login') }}" class="text-white text-decoration-none small"><i class="bx bx-lock-alt me-1"></i>{{ __('Sign In') }}</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="store-header py-3 shadow-xs">
        <div class="container d-flex justify-content-between align-items-center gap-3">
            <!-- Brand Logo -->
            <a href="{{ route('storefront.home') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart" height="42" onerror="this.src='{{ asset('assets/img/favicon/favicon.ico') }}'">
                <span class="fs-4 fw-bolder text-primary">AK-Mart</span>
            </a>

            <!-- Search Bar -->
            <form action="{{ route('storefront.shop') }}" method="GET" class="flex-grow-1 mx-4 d-none d-md-block" style="max-width: 550px;">
                <div class="input-group">
                    <input type="text" name="q" class="form-control rounded-start-pill ps-3" placeholder="{{ __('Search 5,000+ groceries, essentials, and snacks...') }}" value="{{ request('q') }}">
                    <button class="btn btn-primary rounded-end-pill px-4" type="submit"><i class="bx bx-search fs-5"></i></button>
                </div>
            </form>

            <!-- Action Buttons: Wishlist & Cart -->
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('customer.wishlist') }}" class="btn btn-light rounded-pill position-relative px-3">
                    <i class="bx bx-heart fs-5 align-middle"></i>
                </a>
                <a href="{{ route('storefront.cart') }}" class="btn btn-primary rounded-pill position-relative px-3 d-flex align-items-center gap-2">
                    <i class="bx bx-cart fs-5"></i>
                    <span class="d-none d-sm-inline fw-semibold">{{ __('Cart') }}</span>
                    @php $cartCount = array_sum(array_column(session('cart', []), 'qty')); @endphp
                    <span class="badge bg-white text-primary rounded-pill" id="cartBadge">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar: Categories -->
    <nav class="bg-white border-bottom py-2 shadow-xs d-none d-md-block">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex gap-4">
                <a href="{{ route('storefront.home') }}" class="text-dark fw-semibold text-decoration-none">{{ __('Home') }}</a>
                <a href="{{ route('storefront.shop') }}" class="text-dark fw-semibold text-decoration-none">{{ __('All Products') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 1]) }}" class="text-muted text-decoration-none">{{ __('Groceries & Staples') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 2]) }}" class="text-muted text-decoration-none">{{ __('Beverages & Juices') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 3]) }}" class="text-muted text-decoration-none">{{ __('Dairy & Eggs') }}</a>
                <a href="{{ route('storefront.shop', ['category' => 4]) }}" class="text-muted text-decoration-none">{{ __('Fresh Bakery') }}</a>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="badge bg-label-primary text-decoration-none px-3 py-1.5"><i class="bx bx-grid-alt me-1"></i>{{ __('Admin Portal') }}</a>
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
    </script>
    @yield('scripts')
</body>
</html>
