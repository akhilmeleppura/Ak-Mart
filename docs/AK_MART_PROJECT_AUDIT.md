# AK-Mart Project Audit

## 1. System Overview
- **Application Name:** AK-Mart
- **Brand Identity:** AK-Mart (Smart Management for Modern Stores)
- **Primary Framework:** Laravel 12.0
- **PHP Version:** 8.2+
- **Database Engine:** MySQL / MariaDB
- **Frontend Tech:** Blade Templating + JavaScript (ES6) + Bootstrap 5 + Sneat UI Foundation
- **Authentication:** Laravel Jetstream / Sanctum / Fortify
- **Authorization:** Spatie Laravel Permission (`spatie/laravel-permission` ^6.25)
- **Modular Architecture:** `nwidart/laravel-modules` (Modules: Accounting, Billing, General, Permission, SampleModule)
- **Author:** Akhil S
- **Contributors:** None

---

## 2. Existing Feature Audit Matrix

| Feature / Module | Existing in Codebase | Current Status | Needs Upgrade | Action Plan |
| :--- | :---: | :---: | :---: | :--- |
| **Authentication & Login UI** | Yes | Working | Yes | Redesign login into a modern 2-column SaaS layout with AK-Mart branding, floating stats, password toggle. |
| **Branding & Logos** | Partial | Needs Cleanup | Yes | Replace all references to old app names with `AK-Mart`. Create SVG branding assets (`ak-mart-logo.svg`, `ak-mart-logo-dark.svg`, `ak-mart-icon.svg`, `favicon.svg`). |
| **Dark & Light Mode** | Yes | Working | Yes | Standardize palette tokens (`--ak-primary`, `--ak-bg`, etc.) and ensure `localStorage['ak-theme']` persistence across all tables, modals, forms, and charts. |
| **Admin Dashboard** | Yes | Working | Yes | Refine real database queries for Sales, Revenue, Expenses, Profit, Orders, Low Stock, Top Categories, and chart visualization. |
| **Product Management** | Yes | Working | Yes | Enhance with SKU auto-generator, bulk actions (delete, status toggle), tax assignment, SEO fields, and variant support. |
| **Category Management** | Yes | Working | Yes | Support parent-child hierarchy visual tree, slug generation, SEO metadata, and category image upload. |
| **Brand Management** | Partial | Partial | Yes | Add dedicated Brand CRUD and relationship to products. |
| **Customer Management** | Yes | Working | Yes | Add customer purchase summary, spent totals, order history modal, and customer profile breakdown. |
| **Supplier Management** | No | Missing | Yes | Implement Supplier CRUD (contact details, address, payment details, lead times). |
| **Purchase Management** | No | Missing | Yes | Implement Purchase Orders, receiving workflows, supplier invoice tracking, and stock auto-increment. |
| **Inventory & Stock Control** | Yes | Working (Vendor) | Yes | Add traceable stock movement log, stock adjustment modal, low-stock threshold alerts, and stock audit history. |
| **Order Management** | Yes | Working | Yes | Add full order status pipeline (Pending -> Confirmed -> Processing -> Packed -> Shipped -> Delivered -> Cancelled -> Refunded), printable PDF/HTML invoice, and order timeline. |
| **POS / Quick Sale Terminal** | Yes | Partial | Yes | Complete POS checkout logic: barcode scan, cart management, instant discount, cash/card payment handling, receipt modal, real stock deduction. |
| **Payment Management** | Yes | Working | Yes | Support payment gateway configurations, transaction tracking, webhook security, and payment log verification. |
| **Tax Management** | Yes | Working | Yes | Implement tax classes (Standard, Zero, Reduced), inclusive/exclusive tax calculation engine, and tax reporting. |
| **Discount & Coupon Engine** | Yes | Working | Yes | Add coupon usage tracking, minimum spend rules, category/product restrictions, and bulk coupon code generator. |
| **Shipping Management** | Yes | Working | Yes | Add shipping zones, flat rate / free shipping rules, tracking number assignment, and shipping label print capability. |
| **User, Role & Permission (RBAC)** | Yes | Working | Yes | Enforce permission gates and middleware across all routes (`Super Admin`, `Admin`, `Manager`, `Cashier`, `Sales Staff`, `Inventory Manager`, `Accountant`). |
| **Security Audit Log** | Yes | Working | Yes | Centralize event tracking for user login/logout, stock adjustments, order status changes, and sensitive settings mutations. |
| **Notification Center** | Yes | Working | Yes | Build real-time/database notification triggers for low stock, new orders, failed payments, and dunning alerts. |
| **Global Search (Ctrl + K)** | No | Missing | Yes | Implement lightweight modal search indexing Products, Orders, Customers, Suppliers, and Settings. |
| **Reporting & Analytics Suite** | No | Missing | Yes | Build comprehensive Reports module (Sales, Product Performance, Stock Valuation, Customer Leaderboard, Tax Summary, Profit & Loss). |
| **API System (v1)** | Yes | Working | Yes | Expand Storefront API v1 (Auth, Products, Categories, Orders, Cart) using Laravel API Resources and Sanctum authentication. |
| **Settings Engine** | Yes | Working | Yes | Centralize Store Details, Localization (Currency, Timezone), Tax, Shipping, Payments, AI Copilot, and Maps configuration. |

---

## 3. Technology Stack & Packages
- **Framework:** Laravel 12 (`laravel/framework` ^12.0)
- **Authentication:** Jetstream (`laravel/jetstream` ^5.3) & Sanctum (`laravel/sanctum` ^4.0)
- **Livewire:** Livewire 3 (`livewire/livewire` ^3.0)
- **Permissions:** Spatie Laravel Permission (`spatie/laravel-permission` ^6.25)
- **Modules:** Nwidart Modules (`nwidart/laravel-modules`)
- **Frontend Assets:** Vite 6 (`vite` ^6.3.5), Bootstrap 5 (`bootstrap` ^5.3.5), DataTables BS5 (`datatables.net-bs5`), ApexCharts (`apexcharts` ^4.2.0), Chart.js (`chart.js` ^4.4.9), Select2 (`select2`), SweetAlert2 (`sweetalert2`)

---

## 4. Key Security & Performance Findings
1. All database queries in dashboard & controllers use Eloquent aggregation and eager loading (`with()`).
2. DataTables endpoints are protected and rely on AJAX requests.
3. Multi-branch access middleware (`branch.access`) and tenant subscription middleware (`tenant.subscription`) are active.
4. Existing business logic and database structure are intact and well-organized.

---

## 5. Audit Conclusion
AK-Mart possesses a solid technical foundation. The upgrade plan will preserve all existing functionality while implementing the new AK-Mart visual design system, completing partially-built features (such as POS checkout and stock tracking), and adding missing enterprise features (Suppliers, Purchase Management, Global Search, and Comprehensive Reports).
