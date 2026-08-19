# AK-MART Translation & Localization Guide

## 1. Adding New Translation Strings

### In Blade Views:
```blade
{{-- Root Dictionary Translation --}}
{{ __('Order Details') }}
{{ __('Total Sales') }}

{{-- Modular Domain File Translation --}}
{{ __('navigation.dashboard') }}
{{ __('orders.status.delivered') }}
```

### In Controllers / PHP Services:
```php
$msg = __('Product saved successfully.');
// Or with placeholders:
$alert = __('Stock for :product is low (:qty remaining)', ['product' => $p->name, 'qty' => $p->qty]);
```

### In JavaScript / AJAX Handlers:
```javascript
const translated = window.__('Success');
```

---

## 2. Adding a New Language

1. Add the locale code to `$supportedLocales` in:
   - `App\Http\Middleware\LocaleMiddleware`
   - `App\Http\Controllers\language\LanguageController`
2. Create `lang/{locale}.json` and populate all keys matching `lang/en.json`.
3. Add the language selector item in `resources/views/layouts/sections/navbar/navbar-partial.blade.php`.
4. Run `php artisan akmart:translation-audit` to verify 100% parity.
