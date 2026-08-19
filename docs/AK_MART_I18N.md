# AK-MART Internationalization (i18n) Architecture

## 1. Supported Languages & Locales

| Locale Code | Language | Native Name | Direction | Font / Unicode |
| :---: | :---: | :---: | :---: | :---: |
| **`en`** | English | English | `LTR` | Standard UTF-8 |
| **`ml`** | Malayalam | മലയാളം | `LTR` | Full Unicode Indic |
| **`hi`** | Hindi | हिन्दी | `LTR` | Devanagari Unicode |
| **`ar`** | Arabic | العربية | `RTL` | Arabic Script / RTL |
| **`fr`** | French | Français | `LTR` | Latin Accented UTF-8 |
| **`de`** | German | Deutsch | `LTR` | Latin Umlauts UTF-8 |

---

## 2. Multi-Layer Locale Resolution Hierarchy

When a request arrives at AK-Mart, `App\Http\Middleware\LocaleMiddleware` resolves the active locale using the following priority order:

1. **Active Session Preference** (`session('locale')`) — Reflects immediate clicks on the navbar language switcher.
2. **Authenticated User Profile** (`$user->locale`) — Stored in the database, automatically retrieved when logging in from any new browser/device.
3. **Persistent Cookie** (`cookie('akmart_locale')`) — Ensures guest users retain their chosen language across page reloads and tab closures.
4. **Browser Accept-Language** (`$request->getPreferredLanguage(...)`) — Detects the visitor's operating system / browser language.
5. **System Fallback** (`config('app.locale', 'en')`) — Defaults to English.

---

## 3. Database Integrity & Zero Data Loss

- Technical database keys, machine statuses (`pending`, `processing`, `delivered`, `paid`, `refunded`), SKUs, and monetary calculations remain completely independent of the presentation language.
- The language engine only translates visual UI text, dashboard widgets, menu headers, alerts, modals, emails, notifications, and AI responses.

---

## 4. Frontend JavaScript i18n Bridge

Global translations are exposed to JavaScript via `window.AK_I18N` and `window.__('Key')`:

```javascript
// Accessing active locale & direction
const locale = window.AK_I18N.locale; // 'ml', 'hi', 'ar', etc.
const dir = window.AK_I18N.dir;       // 'ltr' or 'rtl'

// Translating text inside scripts, SweetAlerts, or charts
Swal.fire({
    title: window.__('Are you sure?'),
    text: window.__('This action cannot be undone.'),
    icon: 'warning'
});
```

---

## 5. Automated Translation Parity Audit

Check translation completeness across all 6 supported locales at any time:

```bash
php artisan akmart:translation-audit
php artisan akmart:translation-audit --detail
```
