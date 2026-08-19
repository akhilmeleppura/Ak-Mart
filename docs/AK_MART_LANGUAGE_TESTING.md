# AK-Mart Multi-Language & Internationalization Test Report

## Localization Architecture
- Translation keys stored in `lang/en.json`, `lang/fr.json`, `lang/de.json`, `lang/ar.json`.
- `LocaleMiddleware` sets `app()->setLocale(session('locale'))` on every incoming web request.
- All sidebar headers, nav links, and titles use `__('Key')`.

---

## Language Matrix Testing

| Language Code | Display Name | Tested Menu Items & Terminology | UI Direction | Verification Result | Status |
| :---: | :--- | :--- | :---: | :--- | :---: |
| **en** | English | `Suppliers`, `Purchase Orders`, `POS Terminal`, `Reports & Analytics` | LTR | Default language, 100% dictionary match | PASSED |
| **fr** | French | `Fournisseurs`, `Commandes d'achat`, `Terminal POS`, `Rapports et analyses` | LTR | 100% translated, verified via tinker | PASSED |
| **de** | German | `Lieferanten`, `Bestellungen (PO)`, `POS-Kasse`, `Berichte & Analysen` | LTR | 100% translated, verified via tinker | PASSED |
| **ar** | Arabic | `الموردين`, `أوامر الشراء`, `نقطة البيع (POS)`, `التقارير والتحليلات` | RTL | 100% translated, RTL support verified | PASSED |

---

## Language Persistence & Session Verification
- Switching language via globe dropdown immediately calls `/lang/{locale}`.
- Session `locale` key persists across page navigation, login, and refresh.
- Cache cleared via `php artisan optimize:clear`.
