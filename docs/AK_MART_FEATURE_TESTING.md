# AK-Mart Feature & Regression Testing Matrix

| Feature | Input / Test Case | Expected Behavior | Actual Behavior | Result |
| :--- | :--- | :--- | :--- | :---: |
| **Login Auth** | `admin@ak-mart.com` + `password` | Authenticates session, sets `Auth::check() = true`, redirects to `/dashboard` | Login succeeds, session persists across pages | PASSED |
| **Dashboard KPIs** | Open `/app/ecommerce/dashboard` | Renders real DB sales sum ($57,972.53), orders count (200), ApexCharts | Metrics load cleanly from Eloquent queries | PASSED |
| **POS Terminal** | Search item by barcode/SKU & Checkout | Cart calculates total, deducts product stock, creates order (`ORD-POS-`) | Stock decremented, receipt modal displays | PASSED |
| **Product CRUD** | Edit Product #114 & save variants | Updates price, quantity, description, replaces variants without error | Product & variants updated successfully | PASSED |
| **Coupons** | Create coupon `TEST50` (fixed $50) | Validates code, saves to `coupons` table with active status | Saved to DB, listed on coupons table | PASSED |
| **Suppliers** | Create Supplier (Apex Wholesale) | Saves contact details & balance tracking to `suppliers` table | Saved and listed cleanly | PASSED |
| **Purchases** | Issue Purchase Order (`PO-2026-001`) | Creates PO, updates supplier relationship, supports 'Received' status | Saved and updated cleanly | PASSED |
| **Reports Suite** | Date filter & CSV Export | Aggregates financial KPIs and streams CSV download (`/app/reports/export-csv`) | Streamed CSV download completes | PASSED |
| **Global Search** | Press `Ctrl + K` or click search bar | Opens modal searching Products, Orders, Customers, Suppliers | Results returned with clickable links | PASSED |
| **Branch Switcher** | Switch to 'London Flagship' (`/branch/2`) | Updates `session('branch_id')`, filters dashboard & inventory scope | Branch context switched and persisted | PASSED |
| **Language Switcher** | Switch to French (`/lang/fr`) | Updates `session('locale')`, translates all menu titles & headers | Locales translated to French (`Fournisseurs`, etc.) | PASSED |
