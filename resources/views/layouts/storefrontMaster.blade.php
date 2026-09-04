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
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid #E2E8F0;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 4px 20px -5px rgba(15, 23, 42, 0.05);
            transition: all 0.25s ease;
        }

        /* Brand Logo */
        .store-brand-link {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .store-brand-link:hover {
            transform: translateY(-1px);
        }
        .store-logo-wrapper {
            width: 46px;
            height: 46px;
            min-width: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #F0FDF4 0%, #EFF6FF 100%);
            border: 1.5px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .store-brand-link:hover .store-logo-wrapper {
            border-color: #6366F1;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.18);
            transform: scale(1.04);
        }
        .store-logo-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .store-brand-title {
            font-size: 24px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.6px;
            color: #0F172A;
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .store-brand-badge {
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            color: #FFFFFF;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }
        .store-brand-tagline {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #64748B;
            margin-top: 3px;
            line-height: 1;
        }

        /* Highlighted Search Bar */
        .search-container-box {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .search-icon-wrapper {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366F1;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 3;
        }
        .search-pill-input {
            width: 100%;
            height: 46px;
            border: 1.5px solid #E2E8F0;
            background: #F8FAFC;
            border-radius: 9999px;
            padding: 0 115px 0 46px;
            font-size: 13.5px;
            font-weight: 500;
            color: #0F172A;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }
        .search-pill-input::placeholder {
            color: #94A3B8;
            font-weight: 400;
            font-size: 13.5px;
        }
        .search-pill-input:focus {
            background: #FFFFFF;
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12), 0 8px 20px -4px rgba(79, 70, 229, 0.08);
            outline: none;
        }
        .search-btn-highlight {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            height: 36px;
            padding: 0 18px;
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.25);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 3;
        }
        .search-btn-highlight:hover {
            transform: translateY(-50%) scale(1.02);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            color: #FFFFFF;
        }
        .search-btn-highlight:active {
            transform: translateY(-50%) scale(0.98);
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

        /* Universal Storefront Product Cards */
        .product-grid-card, .product-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 22px;
            padding: 16px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }
        .product-grid-card:hover, .product-card:hover {
            transform: translateY(-6px);
            border-color: rgba(79, 70, 229, 0.35);
            box-shadow: 0 20px 30px -10px rgba(79, 70, 229, 0.12), 0 8px 12px -4px rgba(0, 0, 0, 0.04);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        .product-img-canvas, .product-img-wrap {
            height: 185px;
            max-height: 185px;
            width: 100%;
            background: #F8FAFC;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .product-img-canvas a, .product-img-wrap a {
            display: block;
            width: 100%;
            height: 100%;
        }
        .product-img-canvas img, .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
            display: block;
        }
        .product-grid-card:hover .product-img-canvas img,
        .product-card:hover .product-img-wrap img {
            transform: scale(1.08);
        }

        /* Glass Badges & Round Action Icons */
        .glass-badge-deal {
            background: rgba(239, 68, 68, 0.9);
            backdrop-filter: blur(8px);
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            position: absolute;
            bottom: 10px;
            left: 10px;
            z-index: 5;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }
        .card-action-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            z-index: 6;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .card-action-btn:hover {
            background: #FFFFFF;
            transform: scale(1.14);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .btn-add-cart {
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 30px;
            font-size: 13.5px;
            font-weight: 700;
            padding: 9px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.2);
        }
        .btn-add-cart:hover {
            background: linear-gradient(135deg, #4338CA 0%, #2563EB 100%);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
        }
        .btn-add-cart:disabled {
            background: #E2E8F0;
            color: #94A3B8;
            box-shadow: none;
            cursor: not-allowed;
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

        /* Option Menu & Flyout Sub-menus */
        .category-dropdown-btn {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            padding: 7px 14px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .category-dropdown-btn:hover, .category-dropdown-btn.active, .category-dropdown-btn[aria-expanded="true"] {
            background: #EEF2FF;
            color: #4F46E5;
            border-color: #C7D2FE;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
        }
        .dropdown-submenu-item {
            position: relative;
        }
        .dropdown-submenu-item:hover > .dropdown-menu-sub,
        .dropdown-submenu-item.show > .dropdown-menu-sub {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }
        .dropdown-menu-sub {
            display: none;
            position: absolute !important;
            left: 100% !important;
            top: 0 !important;
            min-width: 260px !important;
            background: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 16px !important;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.18) !important;
            padding: 8px !important;
            z-index: 1090 !important;
            margin-left: 2px !important;
        }
        .dropdown-menu-sub::before {
            content: '';
            position: absolute;
            top: 0;
            left: -12px;
            width: 14px;
            height: 100%;
        }
        .mega-aisle-dropdown {
            min-width: 290px;
            border-radius: 18px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            padding: 10px;
            background: #FFFFFF;
            z-index: 1080 !important;
        }
        .management-dropdown-menu {
            min-width: 320px;
            border-radius: 18px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            padding: 12px;
            background: #FFFFFF;
            z-index: 1080 !important;
        }
        .mgmt-section-header {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94A3B8;
            padding: 6px 12px 4px;
            margin-top: 4px;
        }
        .mgmt-menu-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: #1E293B;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.18s ease;
        }
        .mgmt-menu-link:hover {
            background: #F1F5F9;
            color: #4F46E5;
            transform: translateX(3px);
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

        /* Storefront AI Chatbot Floating Widget */
        .store-ai-widget {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1060;
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .store-ai-widget.pos-left {
            right: auto;
            left: 24px;
        }
        .store-ai-toggle-btn {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            color: #FFFFFF;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
            cursor: pointer;
            position: relative;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .store-ai-toggle-btn:hover {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.5);
            color: #FFFFFF;
        }
        .store-ai-pulse-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 13px;
            height: 13px;
            background: #10B981;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            animation: aiPulse 2s infinite;
        }
        @keyframes aiPulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .store-ai-window {
            position: absolute;
            bottom: 68px;
            right: 0;
            width: 380px;
            max-width: calc(100vw - 32px);
            height: 520px;
            max-height: calc(100vh - 120px);
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1061;
        }
        .store-ai-widget.pos-left .store-ai-window {
            right: auto;
            left: 0;
        }
        .store-ai-window.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .store-ai-header {
            background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #1E40AF 100%);
            color: #FFFFFF;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .store-ai-body {
            flex-grow: 1;
            padding: 16px;
            overflow-y: auto;
            background: #F8FAFC;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .store-ai-msg {
            max-width: 86%;
            padding: 10px 14px;
            border-radius: 16px;
            font-size: 13.5px;
            line-height: 1.45;
            word-break: break-word;
        }
        .store-ai-msg.bot {
            align-self: flex-start;
            background: #FFFFFF;
            color: #0F172A;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            border-bottom-left-radius: 4px;
        }
        .store-ai-msg.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
            color: #FFFFFF;
            border-bottom-right-radius: 4px;
        }
        .store-ai-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }
        .store-ai-chip-btn {
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
            border-radius: 20px;
            padding: 5px 11px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .store-ai-chip-btn:hover {
            background: #2563EB;
            color: #FFFFFF;
            border-color: #2563EB;
        }
        .store-ai-footer {
            padding: 12px;
            background: #FFFFFF;
            border-top: 1px solid #E2E8F0;
        }
        .store-ai-input-wrap {
            display: flex;
            align-items: center;
            background: #F1F5F9;
            border: 1.5px solid #E2E8F0;
            border-radius: 30px;
            padding: 4px 6px 4px 14px;
            transition: all 0.2s;
        }
        .store-ai-input-wrap:focus-within {
            border-color: #4F46E5;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }
        .store-ai-input {
            border: none;
            background: transparent;
            font-size: 13px;
            flex-grow: 1;
            outline: none;
            color: #0F172A;
        }
        .store-ai-send-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #4F46E5;
            color: #FFFFFF;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .store-ai-send-btn:hover {
            background: #3B82F6;
            transform: scale(1.05);
        }
        .store-ai-msg table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 12px;
        }
        .store-ai-msg th, .store-ai-msg td {
            border: 1px solid #E2E8F0;
            padding: 4px 8px;
            text-align: left;
        }
        .store-ai-msg th {
            background: #F1F5F9;
            font-weight: 700;
        }
        .store-ai-msg a {
            color: #4F46E5;
            font-weight: 600;
            text-decoration: underline;
        }

        /* RTL (Right-to-Left) Localization Styles */
        [dir="rtl"] {
            text-align: right;
        }
        [dir="rtl"] .search-icon-wrapper {
            left: auto;
            right: 16px;
        }
        [dir="rtl"] .search-pill-input {
            padding: 0 46px 0 115px;
            text-align: right;
        }
        [dir="rtl"] .search-btn-highlight {
            right: auto;
            left: 5px;
        }
        [dir="rtl"] .store-ai-widget {
            right: auto;
            left: 24px;
        }
        [dir="rtl"] .store-ai-window {
            right: auto;
            left: 0;
        }
        [dir="rtl"] .store-ai-widget.pos-left {
            left: auto;
            right: 24px;
        }
        [dir="rtl"] .store-ai-widget.pos-left .store-ai-window {
            left: auto;
            right: 0;
        }
        [dir="rtl"] .dropdown-menu-end {
            --bs-position: start;
            left: 0 !important;
            right: auto !important;
        }
        [dir="rtl"] .store-ai-msg th, [dir="rtl"] .store-ai-msg td {
            text-align: right;
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
                <!-- Multilingual Language Switcher -->
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm text-white dropdown-toggle p-0 d-flex align-items-center gap-1 border-0 shadow-none bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 12px; font-weight: 600;">
                        <i class="bx bx-globe text-info"></i>
                        <span>{{ ['en' => 'English', 'ml' => 'മലയാളം', 'hi' => 'हिन्दी', 'ar' => 'العربية', 'fr' => 'Français', 'de' => 'Deutsch', 'ta' => 'தமிழ்', 'kn' => 'ಕನ್ನಡ', 'it' => 'Italiano'][app()->getLocale()] ?? 'English' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-1 py-1" style="min-width: 175px; z-index: 1050; font-size: 13px;">
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'en' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/en') }}"><span>🇬🇧 English</span><span class="small opacity-75">EN</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'ml' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/ml') }}"><span>🇮🇳 മലയാളം</span><span class="small opacity-75">ML</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'hi' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/hi') }}"><span>🇮🇳 हिन्दी</span><span class="small opacity-75">HI</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'ar' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/ar') }}"><span>🇦🇪 العربية</span><span class="small opacity-75">AR</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'fr' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/fr') }}"><span>🇫🇷 Français</span><span class="small opacity-75">FR</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'de' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/de') }}"><span>🇩🇪 Deutsch</span><span class="small opacity-75">DE</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'ta' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/ta') }}"><span>🇮🇳 தமிழ்</span><span class="small opacity-75">TA</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'kn' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/kn') }}"><span>🇮🇳 ಕನ್ನಡ</span><span class="small opacity-75">KN</span></a></li>
                        <li><a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ app()->getLocale() === 'it' ? 'active bg-primary text-white' : '' }}" href="{{ url('lang/it') }}"><span>🇮🇹 Italiano</span><span class="small opacity-75">IT</span></a></li>
                    </ul>
                </div>
                <span class="text-muted">•</span>
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
            <a href="{{ route('storefront.home') }}" class="store-brand-link d-flex align-items-center gap-2.5 text-decoration-none">
                <div class="store-logo-wrapper">
                    <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart" class="store-logo-img" onerror="this.src='{{ asset('images/brand/ak-mart-logo.svg') }}'">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="store-brand-title">AK<span class="text-primary">-Mart</span></span>
                        <span class="store-brand-badge">{{ __('SMART') }}</span>
                    </div>
                    <span class="store-brand-tagline">{{ __('ONLINE SUPERMARKET') }}</span>
                </div>
            </a>

            <!-- Search Bar with Live Suggestions Dropdown -->
            <div class="position-relative flex-grow-1 mx-3 d-none d-md-block" style="max-width: 620px;">
                <form action="{{ route('storefront.shop') }}" method="GET" class="m-0">
                    <div class="search-container-box">
                        <span class="search-icon-wrapper">
                            <i class="bx bx-search fs-5"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               id="storeSearchInput" 
                               class="form-control search-pill-input" 
                               placeholder="{{ __('Search 5,000+ fresh groceries, produce, dairy & snacks...') }}" 
                               value="{{ request('q') }}" 
                               autocomplete="off">
                        <button class="search-btn-highlight" type="submit">
                            <span>{{ __('Search') }}</span>
                            <i class="bx bx-right-arrow-alt fs-5"></i>
                        </button>
                    </div>
                </form>
                <div id="searchSuggestionsBox" class="position-absolute start-0 w-100 bg-white border rounded-4 shadow-xl p-2 d-none" style="top: calc(100% + 8px); z-index: 1050; max-height: 380px; overflow-y: auto; border: 1px solid #E2E8F0 !important;"></div>
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

    <!-- Sub-Navbar: Option Menus with Sub-menus & Management Hub -->
    <nav class="bg-white border-bottom py-1.5 shadow-xs d-none d-md-block" style="background: rgba(255,255,255,0.98); position: relative; z-index: 1060;">
        <div class="container d-flex align-items-center justify-content-between gap-2">
            
            <!-- 1. Left: All Departments Dropdown Menu with Sub-menus -->
            <div class="flex-shrink-0 dropdown">
                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs dropdown-toggle text-nowrap" type="button" id="allDepartmentsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%); border: none;">
                    <i class="bx bx-grid-alt fs-6"></i>
                    <span>{{ __('All Departments') }}</span>
                </button>
                <ul class="dropdown-menu mega-aisle-dropdown shadow-lg border-0 py-2 mt-2" aria-labelledby="allDepartmentsDropdown">
                    <li class="px-3 py-1 mb-1 text-muted small fw-bolder text-uppercase letter-spacing-1">
                        {{ __('Explore Aisles & Sub-Categories') }}
                    </li>
                    @php
                        $majorCats = $navCategories ?? collect();
                        $catIconMap = [
                            'Groceries & Staples'       => '🍎',
                            'Beverages & Juices'        => '🥤',
                            'Dairy & Eggs'              => '🧀',
                            'Bakery & Bread'            => '🥐',
                            'Snacks & Confectionery'    => '🍿',
                            'Personal Care & Beauty'    => '🧴',
                            'Household & Cleaning'      => '🧹',
                            'Fresh Fruits & Vegetables' => '🥦',
                            'Electronics & Accessories' => '🎧',
                            'Health & Wellness'         => '💊',
                            'Baby & Child Care'         => '👶',
                            'Pet Supplies'              => '🐾',
                        ];
                    @endphp
                    @foreach($majorCats as $cat)
                        @php $catEmoji = $catIconMap[$cat->name] ?? '🛍️'; @endphp
                        <li class="dropdown-submenu-item position-relative">
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 rounded-3 fw-semibold {{ request('category') == $cat->id ? 'bg-light text-primary' : '' }}" href="{{ route('storefront.shop', ['category' => $cat->id]) }}">
                                <span class="d-flex align-items-center gap-2">
                                    <span>{{ $catEmoji }}</span>
                                    <span>{{ $cat->name }}</span>
                                </span>
                                @if($cat->children && $cat->children->count() > 0)
                                    <i class="bx bx-chevron-right text-muted small"></i>
                                @else
                                    <span class="badge bg-light text-muted rounded-pill px-2" style="font-size: 11px;">{{ $cat->products_count ?? 0 }}</span>
                                @endif
                            </a>
                            @if($cat->children && $cat->children->count() > 0)
                                <ul class="dropdown-menu dropdown-menu-sub shadow-lg border-0 py-2">
                                    <li class="px-3 py-1 mb-1 fw-bold text-primary small border-bottom pb-1.5">
                                        <a href="{{ route('storefront.shop', ['category' => $cat->id]) }}" class="text-primary text-decoration-none d-flex align-items-center justify-content-between">
                                            <span>{{ __('All') }} {{ $cat->name }}</span>
                                            <i class="bx bx-arrow-to-right"></i>
                                        </a>
                                    </li>
                                    @foreach($cat->children as $sub)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-3 rounded-2 small fw-medium {{ request('category') == $sub->id ? 'active' : '' }}" href="{{ route('storefront.shop', ['category' => $sub->id]) }}">
                                                <span>{{ $sub->name }}</span>
                                                <span class="badge bg-light text-muted rounded-pill ms-2" style="font-size: 10px;">{{ $sub->products_count ?? 0 }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                    <li class="border-top mt-2 pt-2">
                        <a class="dropdown-item text-center fw-bold text-primary small py-1.5" href="{{ route('storefront.shop') }}">
                            {{ __('View Complete Grocery Catalog →') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- 2. Middle: Clean unclipped category chips strip with full dropdown visibility -->
            <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-visible py-0.5 mx-1">
                <!-- Shortcuts: Home, Buy Again, Refer -->
                <a href="{{ route('storefront.home') }}" class="category-chip-link flex-shrink-0 text-nowrap {{ request()->routeIs('storefront.home') ? 'active' : '' }}">
                    <i class="bx bx-home-alt text-primary"></i> {{ __('Home') }}
                </a>
                <a href="{{ route('storefront.buy_again') }}" class="category-chip-link flex-shrink-0 text-primary fw-bold text-nowrap" style="background: #EEF2FF; border-color: #C7D2FE;">
                    <i class="bx bx-repeat"></i> {{ __('Buy Again') }}
                </a>
                <a href="{{ route('storefront.referral') }}" class="category-chip-link flex-shrink-0 text-success fw-bold text-nowrap" style="background: #ECFDF5; border-color: #A7F3D0;">
                    <i class="bx bx-gift"></i> {{ __('Refer & Earn $10') }}
                </a>

                <!-- Curated Category Chips (Clean, concise labels) -->
                @php
                    $chipCategories = $majorCats->take(5);
                    $shortCatNames = [
                        'Groceries & Staples'       => 'Groceries',
                        'Beverages & Juices'        => 'Beverages',
                        'Dairy & Eggs'              => 'Dairy & Eggs',
                        'Bakery & Bread'            => 'Bakery',
                        'Snacks & Confectionery'    => 'Snacks',
                        'Fresh Fruits & Vegetables' => 'Produce',
                    ];
                @endphp
                @foreach($chipCategories as $c)
                    @php
                        $emoji = $catIconMap[$c->name] ?? '🛍️';
                        $displayName = $shortCatNames[$c->name] ?? $c->name;
                        $hasSub = $c->children && $c->children->count() > 0;
                    @endphp
                    @if($hasSub)
                        <div class="dropdown flex-shrink-0">
                            <button class="category-dropdown-btn dropdown-toggle text-nowrap {{ request('category') == $c->id ? 'active' : '' }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>{{ $emoji }} {{ $displayName }}</span>
                            </button>
                            <ul class="dropdown-menu shadow-lg border-0 py-2 mt-1" style="min-width: 230px; border-radius: 14px;">
                                <li>
                                    <a class="dropdown-item fw-bold text-primary py-2 px-3 small border-bottom mb-1" href="{{ route('storefront.shop', ['category' => $c->id]) }}">
                                        {{ __('All') }} {{ $c->name }} <i class="bx bx-right-arrow-alt float-end fs-6"></i>
                                    </a>
                                </li>
                                @foreach($c->children as $subCat)
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 small d-flex justify-content-between align-items-center {{ request('category') == $subCat->id ? 'bg-light text-primary fw-bold' : '' }}" href="{{ route('storefront.shop', ['category' => $subCat->id]) }}">
                                            <span>{{ $subCat->name }}</span>
                                            <span class="badge bg-light text-muted rounded-pill" style="font-size: 10px;">{{ $subCat->products_count ?? 0 }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('storefront.shop', ['category' => $c->id]) }}" class="category-chip-link flex-shrink-0 text-nowrap {{ request('category') == $c->id ? 'active' : '' }}">
                            {{ $emoji }} {{ $displayName }}
                        </a>
                    @endif
                @endforeach
            </div>

            <!-- 3. Right: Management Option Menu (Always anchored on the right, separated cleanly) -->
            <div class="flex-shrink-0 ms-3 ps-2 border-start dropdown" style="border-color: #E2E8F0 !important;">
                <button class="btn btn-sm rounded-pill px-3.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 transition-all dropdown-toggle text-nowrap" type="button" id="managementMenuBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background: #F8FAFC; color: #1E293B; border: 1.5px solid #CBD5E1; font-size: 12.5px;">
                    <i class="bx bx-slider fs-6 text-primary"></i>
                    <span>{{ __('Management') }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end management-dropdown-menu shadow-lg border-0 mt-2" aria-labelledby="managementMenuBtn">
                    <div class="d-flex align-items-center gap-2 p-2 mb-2 bg-light rounded-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                            <i class="bx bx-slider-alt fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold fs-7 text-dark">{{ __('Store Management Hub') }}</h6>
                            <span class="text-muted small" style="font-size: 11px;">{{ __('Commerce, Logistics & Security') }}</span>
                        </div>
                    </div>

                    <!-- Operations & POS -->
                    <div class="mgmt-section-header">{{ __('Operations & POS') }}</div>
                    <a href="{{ route('dashboard') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-chart text-primary fs-6"></i> {{ __('Executive Analytics') }}</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">LIVE</span>
                    </a>
                    <a href="{{ route('app-pos-register') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-credit-card-front text-success fs-6"></i> {{ __('POS Register & Shift') }}</span>
                    </a>
                    <a href="{{ route('app-ecommerce-order-list') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-package text-warning fs-6"></i> {{ __('Order Management & FSM') }}</span>
                    </a>

                    <!-- Inventory & Warehouse -->
                    <div class="mgmt-section-header">{{ __('Inventory & Logistics') }}</div>
                    <a href="{{ route('app-warehouses') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-buildings text-info fs-6"></i> {{ __('Multi-Warehouse Stock') }}</span>
                    </a>
                    <a href="{{ url('/inventory/batches') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-calendar-event text-danger fs-6"></i> {{ __('FEFO Batches & Expiry') }}</span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill">FEFO</span>
                    </a>
                    <a href="{{ url('/warehouse/picking') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-barcode-reader text-primary fs-6"></i> {{ __('Warehouse Pick & Pack') }}</span>
                    </a>
                    <a href="{{ url('/admin/returns') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-undo text-warning fs-6"></i> {{ __('Customer Returns (RMA)') }}</span>
                    </a>

                    <!-- Security & Backups -->
                    <div class="mgmt-section-header">{{ __('System & Disaster Recovery') }}</div>
                    <a href="{{ route('app-security-center') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-shield-quarter text-success fs-6"></i> {{ __('Security Center & Audit') }}</span>
                    </a>
                    <a href="{{ route('app-backups') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-data text-primary fs-6"></i> {{ __('Database Snapshots') }}</span>
                    </a>
                    <a href="{{ route('app-communication-center') }}" class="mgmt-menu-link">
                        <span class="d-flex align-items-center gap-2"><i class="bx bx-message-rounded-dots text-info fs-6"></i> {{ __('Email & WhatsApp Hub') }}</span>
                    </a>
                </div>
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
                    <div class="d-flex align-items-center gap-2.5 mb-3">
                        <div class="store-logo-wrapper" style="background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.15); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                            <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart" class="store-logo-img" onerror="this.src='{{ asset('images/brand/ak-mart-logo.svg') }}'">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="store-brand-title text-white">AK<span class="text-primary">-Mart</span></span>
                                <span class="store-brand-badge">{{ __('SMART') }}</span>
                            </div>
                            <span class="store-brand-tagline text-muted">{{ __('ONLINE SUPERMARKET') }}</span>
                        </div>
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, qty: 1 })
            })
            .then(res => res.json())
            .then(data => {
                const count = data.cartCount !== undefined ? data.cartCount : (data.totalItems !== undefined ? data.totalItems : null);
                if (count !== null) {
                    const badge = document.getElementById('cartBadge');
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = '';
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
                    fetch(`{{ route('storefront.search.suggestions') }}?q=${encodeURIComponent(q)}`)
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

    @php
        $storeAiEnabled = \App\Models\StoreSetting::get('store_ai_chatbot_enabled', '1');
        $storeAiName = \App\Models\StoreSetting::get('store_ai_chatbot_name', 'AK-Mart Assistant');
        $storeAiGreeting = \App\Models\StoreSetting::get('store_ai_chatbot_greeting', "👋 Hi! I am your AK-Mart Shopping Assistant. How can I help you find groceries, track an order, or find discount coupons today?");
        $storeAiPromptsStr = \App\Models\StoreSetting::get('store_ai_chatbot_quick_prompts', 'Track Order, Available Coupons, Trending Products, Delivery Pincode');
        $storeAiPrompts = array_filter(array_map('trim', explode(',', $storeAiPromptsStr)));
        $storeAiPos = \App\Models\StoreSetting::get('store_ai_chatbot_position', 'bottom-right');
    @endphp

    @if($storeAiEnabled == '1')
    <!-- Storefront AI Shopping Assistant Floating Widget -->
    <div class="store-ai-widget {{ $storeAiPos === 'bottom-left' ? 'pos-left' : '' }}" id="storeAiWidget">
        <!-- Floating Trigger Button -->
        <button class="store-ai-toggle-btn" id="storeAiToggleBtn" title="Chat with {{ $storeAiName }}" aria-label="Open AI Shopping Assistant">
            <i class="bx bx-bot fs-3 text-white" id="storeAiToggleIcon"></i>
            <span class="store-ai-pulse-dot"></span>
        </button>

        <!-- Expandable Chat Window -->
        <div class="store-ai-window" id="storeAiWindow">
            <!-- Header -->
            <div class="store-ai-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-xs" style="width: 34px; height: 34px;">
                            <i class="bx bx-bot fs-5"></i>
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 10px; height: 10px;"></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white fw-bold fs-6 lh-1">{{ $storeAiName }}</h6>
                        <small class="text-white-50" style="font-size: 11px;">{{ __('Shopping Assistant') }} &bull; <span class="text-success">{{ __('Online') }}</span></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm text-white p-1 opacity-75" id="storeAiClearBtn" title="{{ __('Clear Chat') }}"><i class="bx bx-trash small"></i></button>
                    <button type="button" class="btn btn-sm text-white p-1 opacity-75" id="storeAiCloseBtn" title="{{ __('Close') }}"><i class="bx bx-x fs-5"></i></button>
                </div>
            </div>

            <!-- Chat Message Thread -->
            <div class="store-ai-body" id="storeAiMessages">
                <!-- Welcome Bot Message -->
                <div class="store-ai-msg bot">
                    {!! nl2br(e($storeAiGreeting)) !!}
                    @if(!empty($storeAiPrompts))
                        <div class="store-ai-chips mt-2">
                            @foreach($storeAiPrompts as $prompt)
                                <button type="button" class="store-ai-chip-btn" onclick="sendAiPrompt('{{ addslashes($prompt) }}')">{{ $prompt }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Input -->
            <div class="store-ai-footer">
                <form id="storeAiChatForm" onsubmit="event.preventDefault(); handleAiChatSubmit();" class="m-0">
                    <div class="store-ai-input-wrap">
                        <input type="text" id="storeAiInput" class="store-ai-input" placeholder="{{ __('Ask about groceries, coupons, orders...') }}" autocomplete="off">
                        <button type="submit" class="store-ai-send-btn" id="storeAiSendBtn" title="Send Message">
                            <i class="bx bx-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const widget = document.getElementById('storeAiWidget');
            const toggleBtn = document.getElementById('storeAiToggleBtn');
            const windowEl = document.getElementById('storeAiWindow');
            const closeBtn = document.getElementById('storeAiCloseBtn');
            const clearBtn = document.getElementById('storeAiClearBtn');
            const messagesEl = document.getElementById('storeAiMessages');
            const inputEl = document.getElementById('storeAiInput');
            const toggleIcon = document.getElementById('storeAiToggleIcon');

            if (!widget || !toggleBtn || !windowEl) return;

            function toggleChat() {
                const isOpen = windowEl.classList.toggle('open');
                if (isOpen) {
                    toggleIcon.className = 'bx bx-x fs-3 text-white';
                    inputEl.focus();
                } else {
                    toggleIcon.className = 'bx bx-bot fs-3 text-white';
                }
            }

            toggleBtn.addEventListener('click', toggleChat);
            if (closeBtn) closeBtn.addEventListener('click', toggleChat);

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    messagesEl.innerHTML = `
                        <div class="store-ai-msg bot">
                            {!! addslashes(nl2br(e($storeAiGreeting))) !!}
                            @if(!empty($storeAiPrompts))
                                <div class="store-ai-chips mt-2">
                                    @foreach($storeAiPrompts as $prompt)
                                        <button type="button" class="store-ai-chip-btn" onclick="sendAiPrompt('{{ addslashes($prompt) }}')">{{ $prompt }}</button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    `;
                });
            }

            window.sendAiPrompt = function(promptText) {
                inputEl.value = promptText;
                handleAiChatSubmit();
            };

            window.handleAiChatSubmit = function() {
                const text = inputEl.value.trim();
                if (!text) return;

                // Append user message
                const userMsg = document.createElement('div');
                userMsg.className = 'store-ai-msg user';
                userMsg.textContent = text;
                messagesEl.appendChild(userMsg);
                inputEl.value = '';
                messagesEl.scrollTop = messagesEl.scrollHeight;

                // Append typing indicator
                const typingMsg = document.createElement('div');
                typingMsg.className = 'store-ai-msg bot';
                typingMsg.id = 'storeAiTyping';
                typingMsg.innerHTML = '<span class="spinner-grow spinner-grow-sm text-primary me-1"></span> <small class="text-muted">{{ __("Thinking...") }}</small>';
                messagesEl.appendChild(typingMsg);
                messagesEl.scrollTop = messagesEl.scrollHeight;

                fetch('{{ route("storefront.ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(res => res.json())
                .then(data => {
                    const typing = document.getElementById('storeAiTyping');
                    if (typing) typing.remove();

                    const botMsg = document.createElement('div');
                    botMsg.className = 'store-ai-msg bot';
                    
                    let replyHtml = data.reply || data.response || '{{ __("I didn\'t understand that. Could you try asking in a different way?") }}';
                    
                    // Simple Markdown Parsing
                    replyHtml = replyHtml
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" class="text-primary fw-bold">$1</a>')
                        .replace(/\n/g, '<br>');

                    botMsg.innerHTML = replyHtml;
                    messagesEl.appendChild(botMsg);
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                })
                .catch(err => {
                    const typing = document.getElementById('storeAiTyping');
                    if (typing) typing.remove();

                    const botMsg = document.createElement('div');
                    botMsg.className = 'store-ai-msg bot text-danger';
                    botMsg.textContent = '⚠️ Sorry, I could not process your message right now. Please try again.';
                    messagesEl.appendChild(botMsg);
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                });
            };
        });
    </script>
    @endif

    <!-- Interactive Navbar Dropdowns & Submenu Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.matchMedia('(min-width: 768px)').matches) {
                const navDropdowns = document.querySelectorAll('nav .dropdown');
                navDropdowns.forEach(function (dropdown) {
                    const btn = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (!btn || !menu) return;

                    let timeout;
                    dropdown.addEventListener('mouseenter', function () {
                        clearTimeout(timeout);
                        navDropdowns.forEach(function (other) {
                            if (other !== dropdown) {
                                const otherMenu = other.querySelector('.dropdown-menu');
                                const otherBtn = other.querySelector('[data-bs-toggle="dropdown"]');
                                if (otherMenu) otherMenu.classList.remove('show');
                                if (otherBtn) {
                                    otherBtn.classList.remove('show');
                                    otherBtn.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });
                        menu.classList.add('show');
                        btn.classList.add('show');
                        btn.setAttribute('aria-expanded', 'true');
                    });

                    dropdown.addEventListener('mouseleave', function () {
                        timeout = setTimeout(function () {
                            menu.classList.remove('show');
                            btn.classList.remove('show');
                            btn.setAttribute('aria-expanded', 'false');
                        }, 120);
                    });
                });

                // Flyout submenus inside All Departments
                const subItems = document.querySelectorAll('.dropdown-submenu-item');
                subItems.forEach(function (item) {
                    const subMenu = item.querySelector('.dropdown-menu-sub');
                    if (!subMenu) return;

                    item.addEventListener('mouseenter', function () {
                        subItems.forEach(function (sibling) {
                            if (sibling !== item) {
                                const sibSub = sibling.querySelector('.dropdown-menu-sub');
                                if (sibSub) sibSub.style.display = 'none';
                            }
                        });
                        subMenu.style.display = 'block';
                    });

                    item.addEventListener('mouseleave', function () {
                        subMenu.style.display = 'none';
                    });
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
