# AK-Mart Global Language & Localization Architecture Audit

## 1. Overview & Supported Locales
AK-Mart features an enterprise-grade localization framework providing seamless, instantaneous language switching across 6 major languages:
- **English (`en`)**: Primary Global Language (LTR)
- **Malayalam (`ml`)**: Native Indic Script with full Unicode rendering
- **Hindi (`hi`)**: Devanagari script for national commerce operations
- **Arabic (`ar`)**: Full Right-to-Left (RTL) directional layout and typography adaptation
- **French (`fr`)**: European / Internationalized French
- **German (`de`)**: High-precision German terminology

---

## 2. Architecture & Persistence Engine
1. **Locale Resolution Hierarchy**:
   - `session('locale')` → User preference stored in session upon switching.
   - `auth()->user()->preferred_locale` → User database profile preference.
   - `StoreSetting::get('default_locale')` → Global store default configured in `/settings/localization`.
   - Fallback: `config('app.fallback_locale', 'en')`.
2. **Translation Dictionaries**:
   - Maintained as UTF-8 formatted JSON dictionaries in `/lang/{locale}.json`.
   - Over 300+ shared translation keys covering navigation, forms, validation messages, action buttons, alert dialogues, and empty states.
3. **RTL Directional Engine**:
   - When locale is `ar`, layout stylesheets dynamically apply `dir="rtl"` and load Sneat RTL-compiled CSS assets.
   - Flexbox orientations, margin/padding utilities (`ms-*`, `me-*`), and icons flip appropriately.

---

## 3. Module-by-Module Translation Coverage Audit

| Module / Component | Translation Key Implementation | RTL Verified | Malayalam Unicode Verified | Status |
| :--- | :---: | :---: | :---: | :---: |
| **Global Navigation & Topbar** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **User Profile & Security** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **SaaS Billing & Invoices** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **Dashboard & Metric Cards** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **Products & Inventory** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **Orders & Invoicing** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **Settings Hub (30 Sections)** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **Email & SMTP Hub** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **WhatsApp Hub** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **AI Copilot & Assistant** | `{{ __('Key') }}` | ✓ | ✓ | Complete |
| **SweetAlert2 Notifications** | Dynamic JS translation | ✓ | ✓ | Complete |
