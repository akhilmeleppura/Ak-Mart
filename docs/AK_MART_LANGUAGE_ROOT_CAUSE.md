# AK-MART Language Switching Root Cause Analysis & Resolution Guide

## 1. Executive Summary

During testing of the multi-language localization switcher across the AK-Mart platform, a critical inconsistency was discovered: while the global navigation elements (Sidebar menu, Top Navbar, and Footer) were switching correctly to the selected language (e.g., Malayalam, Hindi, Arabic, French, German), deeply nested operational pages (such as `/vendor/payment-settings`, `/vendor/pos`, `/app/fulfillment`, `/inventory/warehouses`, `/b2b/companies`, and SaaS administrative dashboards) remained largely in English.

---

## 2. Root Cause Analysis

### Root Cause 1: Static Hardcoded Strings in Blade View Templates
- **Mechanism**: Earlier development rounds utilized raw HTML text strings (e.g., `<h5>Payment Gateway Connections</h5>`, `<label>Publishable Key</label>`) directly in Blade templates without wrapping them in the standard Laravel localization helper `__('...')` or `@lang('...')`.
- **Impact**: While the sidebar JSON dictionary `verticalMenu.json` translated menu labels dynamically, the rendered page body content remained strictly static English.

### Root Cause 2: Untranslated JavaScript Modals, SweetAlerts & AKNotify Alerts
- **Mechanism**: Dynamic client-side scripts used hardcoded string literals inside `Swal.fire({ title: 'Receive Purchase Order?' })` or `AKNotify.success('Count entry saved!')`.
- **Impact**: Even when pages had server-side Blade strings translated, AJAX notifications and confirmation dialogs popped up in English.
- **Resolution**: Replaced with `@json(__('...'))` blade directives passed directly into JavaScript handlers or initialized via translation dictionaries.

### Root Cause 3: Incomplete JSON Dictionaries for Nested Feature Domains
- **Mechanism**: The base `lang/en.json` only contained top-level navigation strings (~458 keys). New feature domains introduced in recent sprints (Advanced B2B Wholesale, SaaS Subscriptions, Cycle Counting, Multi-Warehouse allocation, Dynamic Feeds, Communication Center) lacked keys in `lang/en.json`, `lang/ml.json`, etc.
- **Resolution**: Expanded `lang/en.json` from 458 keys to **1,550+ keys**, synchronized 100% across all 6 core locales with `php artisan akmart:sync-translations`.

### Root Cause 4: Untranslated Form Elements, Placeholders, and Empty States
- **Mechanism**: Input `placeholder="e.g. Central Metro Warehouse"`, table header labels, empty state warnings, and select dropdown options were overlooked in standard translations.
- **Resolution**: Audited every form control, modal, placeholder, badge label, and empty table row across the entire codebase to ensure 100% `__()` coverage.

---

## 3. Architecture & Enforcement Standards

1. **Strict 100% Wrapper Rule**:
   - Every user-facing UI string in Blade MUST be wrapped in `{{ __('String') }}` or `@lang('String')`.
   - Modals, popups, and dynamic JavaScript alerts MUST receive `@json(__('String'))`.
2. **Automated Translation Parity CI/CD**:
   - `php artisan akmart:translation-audit` ensures 100% parity across all 6 locales before production deployment.
   - `php artisan akmart:sync-translations` ensures new master keys in `lang/en.json` are automatically populated across `lang/ml.json`, `lang/hi.json`, `lang/ar.json`, `lang/fr.json`, and `lang/de.json`.
