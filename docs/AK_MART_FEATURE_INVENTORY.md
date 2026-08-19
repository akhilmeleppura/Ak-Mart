# AK-Mart (MiniMart) Feature Inventory

Comprehensive feature inventory compiled directly from the application routes, controllers, and Blade view layer.

| ID | Module | Feature | Route | Backend Controller | Frontend View | Permission | Branch Aware | Language Aware | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :---: | :---: | :---: |
| F-001 | Auth | SaaS Animated Login | `/auth/login-basic` | `LoginBasic@index` | `auth-login-basic.blade.php` | Public | Yes | Yes | PASSED |
| F-002 | Auth | Session Login | `/auth/login-basic` [POST] | `LoginBasic@store` | Redirect Dashboard | Public | Yes | Yes | PASSED |
| F-003 | Auth | Logout | `/logout` | `LoginBasic@logout` | Redirect Login | Auth | Yes | Yes | PASSED |
| F-004 | Dashboard | Admin Analytics Dashboard | `/app/ecommerce/dashboard` | `EcommerceDashboard@index` | `app-ecommerce-dashboard.blade.php` | Auth | Yes | Yes | PASSED |
| F-005 | POS | POS Quick Sale Terminal | `/app/vendor/pos` | `PosController@index` | `pos.blade.php` | Auth | Yes | Yes | PASSED |
| F-006 | POS | Terminal Checkout & Stock Sync | `/app/vendor/pos/checkout` | `PosController@checkout` | Receipt Modal | Auth | Yes | Yes | PASSED |
| F-007 | Products | Product List | `/app/ecommerce/product/list` | `EcommerceProductList@index` | `app-ecommerce-product-list.blade.php` | Auth | Yes | Yes | PASSED |
| F-008 | Products | Add Product Form | `/app/ecommerce/product/add` | `EcommerceProductAdd@index` | `app-ecommerce-product-add.blade.php` | Auth | Yes | Yes | PASSED |
| F-009 | Products | Store Product & Variants | `/app/ecommerce/product/add` [POST] | `EcommerceProductAdd@store` | Redirect List | Auth | Yes | Yes | PASSED |
| F-010 | Products | Edit Product & Variants | `/app/ecommerce/product/edit/{id}` | `EcommerceProductAdd@edit` | `app-ecommerce-product-add.blade.php` | Auth | Yes | Yes | PASSED |
| F-011 | Products | Update Product | `/app/ecommerce/product/edit/{id}` [PUT] | `EcommerceProductAdd@update` | Redirect List | Auth | Yes | Yes | PASSED |
| F-012 | Categories | Category List | `/app/ecommerce/product/category` | `EcommerceProductCategory@index` | `app-ecommerce-product-category.blade.php` | Auth | Yes | Yes | PASSED |
| F-013 | Categories | Store Category | `/app/ecommerce/product/category` [POST] | `EcommerceProductCategory@store` | JSON Response | Auth | Yes | Yes | PASSED |
| F-014 | Orders | Order List | `/app/ecommerce/order/list` | `EcommerceOrderList@index` | `app-ecommerce-order-list.blade.php` | Auth | Yes | Yes | PASSED |
| F-015 | Orders | Order Details | `/app/ecommerce/order/details/{id}` | `EcommerceOrderDetails@index` | `app-ecommerce-order-details.blade.php` | Auth | Yes | Yes | PASSED |
| F-016 | Customers | Customer Directory | `/app/ecommerce/customer/all` | `EcommerceCustomerAll@index` | `app-ecommerce-customer-all.blade.php` | Auth | Yes | Yes | PASSED |
| F-017 | Customers | Customer Details | `/app/ecommerce/customer/details/overview/{id}` | `EcommerceCustomerDetailsOverview@index` | `app-ecommerce-customer-details-overview.blade.php` | Auth | Yes | Yes | PASSED |
| F-018 | Suppliers | Supplier Management | `/app/suppliers` | `SupplierController@index` | `suppliers/index.blade.php` | Auth | Yes | Yes | PASSED |
| F-019 | Suppliers | Store Supplier | `/app/suppliers` [POST] | `SupplierController@store` | Redirect Suppliers | Auth | Yes | Yes | PASSED |
| F-020 | Purchases | Purchase Orders | `/app/purchases` | `PurchaseOrderController@index` | `purchases/index.blade.php` | Auth | Yes | Yes | PASSED |
| F-021 | Purchases | Store Purchase Order | `/app/purchases` [POST] | `PurchaseOrderController@store` | Redirect Purchases | Auth | Yes | Yes | PASSED |
| F-022 | Reports | Reports Suite | `/app/reports` | `ReportController@index` | `reports/index.blade.php` | Auth | Yes | Yes | PASSED |
| F-023 | Reports | Export CSV Report | `/app/reports/export-csv` | `ReportController@exportCsv` | Stream CSV | Auth | Yes | Yes | PASSED |
| F-024 | Coupons | Coupon List | `/app/ecommerce/coupons` | `EcommerceCouponController@index` | `app-ecommerce-coupon-list.blade.php` | Auth | Yes | Yes | PASSED |
| F-025 | Coupons | Create Coupon | `/app/ecommerce/coupons` [POST] | `EcommerceCouponController@store` | JSON Response | Auth | Yes | Yes | PASSED |
| F-026 | Search | Global Search Engine | `/app/global-search` | `GlobalSearchController@search` | JSON Modal Partial | Auth | Yes | Yes | PASSED |
| F-027 | RBAC | Spatie Roles & Permissions | `/app/access-hub` | `RoleController@index` | `app-access-roles.blade.php` | Auth | Yes | Yes | PASSED |
| F-028 | Branch | Branch Switcher | `/branch/{id}` | `BranchController@swap` | Redirect Back | Auth | Yes | Yes | PASSED |
| F-029 | Locale | Language Switcher | `/lang/{locale}` | `LanguageController@swap` | Redirect Back | Public | Yes | Yes | PASSED |
