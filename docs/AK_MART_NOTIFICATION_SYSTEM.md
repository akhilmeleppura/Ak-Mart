# AK-Mart Premium Notification System (`AKNotify`)

## 1. Overview & Architecture

AK-Mart features a custom, modern notification and toast messaging framework called **`AKNotify`**. It provides a non-intrusive, accessible notification surface designed with rounded geometry, smooth spring transitions, countdown progress timers, dark/light theme integration, and full RTL layout support (e.g., Arabic).

### Key Files:
- **Stylesheet**: `public/assets/css/ak-notifications.css`
- **Engine Script**: `public/assets/js/ak-notifications.js`
- **Blade Layout Integration**:
  - `resources/views/layouts/sections/styles.blade.php`
  - `resources/views/layouts/sections/scripts.blade.php`

---

## 2. API Reference & Usage

`AKNotify` is globally accessible on `window.AKNotify`.

### Toast Types & Signatures:

```javascript
// 1. Success Toast
AKNotify.success('Product updated successfully!', 'Catalog Updated', { duration: 4000 });

// 2. Error Toast
AKNotify.error('Failed to communicate with inventory database.', 'Database Error');

// 3. Warning Toast
AKNotify.warning('Low stock remaining (3 units).', 'Inventory Alert');

// 4. Info Toast
AKNotify.info('New orders have been synchronized.', 'Sync Complete');

// 5. Loading Toast with Dismiss Handle
const loaderId = AKNotify.loading('Generating AI product descriptions...');
// Later when complete:
AKNotify.dismiss(loaderId);
AKNotify.success('AI description generated!');

// 6. Asynchronous Confirmation Modal (Promise-based)
const confirmed = await AKNotify.confirm(
  'Are you sure you want to permanently delete this product?',
  'Confirm Product Deletion',
  {
    confirmText: 'Yes, Delete',
    cancelText: 'Cancel',
    type: 'warning'
  }
);
if (confirmed) {
  // Execute deletion...
}
```

---

## 3. Backward Compatibility Bridge

Existing code calling SweetAlert2 (`Swal.fire(...)` or `window.alert(...)`) is intercepted and automatically upgraded to `AKNotify` toast cards or modals with zero breaking changes.

### Livewire & Session Flash Support:
Session flashes dispatched from Laravel controllers (e.g. `return back()->with('success', 'Order created!');`) are automatically rendered as branded `AKNotify` toasts upon page load.
