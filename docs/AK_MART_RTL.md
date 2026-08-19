# AK-MART Arabic Right-to-Left (RTL) Implementation

## 1. Dynamic Direction Detection
The root template `resources/views/layouts/commonMaster.blade.php` calculates the text direction on every request:

```php
$currentLocale = app()->getLocale() ?: session()->get('locale', 'en');
$textDirection = in_array($currentLocale, ['ar', 'fa', 'ur', 'he']) ? 'rtl' : 'ltr';
```

Injected into the DOM:
```html
<html lang="ar" dir="rtl" ...>
```

---

## 2. Layout & Component Adaptations

- **Sidebar & Menus**: Flexbox alignment mirrors to right-aligned navigation.
- **DataTables & Metrics**: Numerical metrics maintain Western/Arabic numerals while headers and descriptions align right.
- **AI Copilot Floating Launcher**: Repositioned or aligned to prevent overlapping with Arabic right-side drawers.
- **SweetAlert2 Dialogs**: Text align right with standard RTL button ordering.
