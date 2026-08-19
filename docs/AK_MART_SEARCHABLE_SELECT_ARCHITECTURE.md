# AK-Mart — Searchable Select Architecture

## Overview
AK-Mart standardizes all searchable dropdowns on **Select2 4.0.13** (already bundled in `package.json`), providing a unified Blade component `<x-searchable-select>`, an automatic DOM initializer, and server-side AJAX endpoints.

---

## 1. Architecture Components

1. **Blade Component**: `resources/views/components/searchable-select.blade.php`
2. **JavaScript Initializer**: `resources/js/components/searchable-select.js`
3. **AJAX Search Controller**: `app/Http/Controllers/api/SelectSearchController.php`
4. **API Routes**: `routes/web.php` (`/api/select/*`)

---

## 2. Component Usage

### Static Options
```html
<x-searchable-select
    name="status"
    :options="['active' => 'Active', 'inactive' => 'Inactive', 'draft' => 'Draft']"
    selected="active"
    placeholder="Select status..."
/>
```

### AJAX Remote Search (Server-Side)
```html
<x-searchable-select
    name="product_id"
    id="product-selector"
    ajax-url="/api/select/products"
    placeholder="Search product by name, SKU, barcode..."
    min-length="2"
/>
```

### Multi-Select
```html
<x-searchable-select
    name="role_ids"
    ajax-url="/api/select/roles"
    :multiple="true"
    placeholder="Select one or more roles..."
/>
```

### Modal Integration
```html
<x-searchable-select
    name="customer_id"
    ajax-url="/api/select/customers"
    dropdown-parent="#addCustomerModal"
    placeholder="Search customer..."
/>
```

---

## 3. Server-Side AJAX Endpoints

All endpoints support `?q={query}&page={page}` and return Select2-compatible JSON:
```json
{
  "results": [
    { "id": 1, "text": "Item Name", "extra_field": "..." }
  ],
  "pagination": { "more": false }
}
```

| Route | Name | Search Criteria | Security Scoping |
|---|---|---|---|
| `/api/select/products` | `api.select.products` | `name`, `sku`, `barcode` | Active branch filter |
| `/api/select/customers` | `api.select.customers` | `name`, `email`, `phone` | Tenant isolation |
| `/api/select/branches` | `api.select.branches` | `name`, `code` | User authorized branches |
| `/api/select/suppliers` | `api.select.suppliers` | `name`, `company_name`, `email`, `phone` | Active suppliers |
| `/api/select/categories` | `api.select.categories` | `name` | Hierarchical |
| `/api/select/users` | `api.select.users` | `name`, `email` | Admin-only access |
| `/api/select/roles` | `api.select.roles` | `name` | Role permissions |

---

## 4. Security Enforcement
1. **Never Trust Submitted IDs**: Submitted IDs are validated against database foreign keys and authorized user scope on form submission.
2. **Branch Access Protection**: Non-admin users cannot query branches outside their authorized assignments.
3. **Pagination & Query Limits**: All endpoints are capped at 15 items per page to prevent memory exhaustion on large catalog databases (10,000+ items).
