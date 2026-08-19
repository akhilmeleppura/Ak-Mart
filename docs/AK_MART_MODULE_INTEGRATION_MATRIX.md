# AK-Mart — Module Integration Matrix (38 Functional Modules)

| Module | Primary Blade | Route Name | Controller | Model | Database Tables | Integration Status |
|--------|---------------|------------|------------|-------|-----------------|--------------------|
| **Authentication & Security** | `auth/login-basic` | `auth-login-basic` | `LoginBasic` | `User` | `users, roles` | ✅ Complete |
| **Store Dashboard** | `content/apps/app-ecommerce-dashboard` | `app-ecommerce-dashboard` | `EcommerceDashboard` | `Order, Product` | `orders, products` | ✅ Complete |
| **Products & Catalog** | `content/apps/app-ecommerce-product-list` | `app-ecommerce-product-list` | `EcommerceProductList` | `Product, Category` | `products, categories` | ✅ Complete |
| **Inventory & Warehouses** | `content/apps/vendor/inventory` | `app-ecommerce-inventory` | `InventoryController` | `Warehouse, StockCount` | `warehouses, stock_counts` | ✅ Complete |
| **Sales & Orders** | `content/apps/app-ecommerce-order-list` | `app-ecommerce-order-list` | `EcommerceOrderList` | `Order, OrderItem` | `orders, order_items` | ✅ Complete |
| **POS Terminal** | `content/apps/vendor/pos` | `app-vendor-pos` | `PosController` | `Product, Order` | `orders, order_items` | ✅ Complete |
| **Customers & CRM** | `content/apps/app-ecommerce-customer-all` | `app-ecommerce-customer-all` | `EcommerceCustomerAll` | `User` | `users` | ✅ Complete |
| **SaaS Billing & Subscriptions** | `content/apps/saas/billing` | `billing.index` | `SubscriptionController` | `TenantSubscription` | `tenant_subscriptions` | ✅ Complete |
| **Settings Management Hub (28 Sections)** | `content/apps/settings/*` | `settings.section` | `SettingsHubController` | `StoreSetting` | `store_settings` | ✅ Complete |
| **Logistics & Shipping** | `content/apps/logistics/shipping-methods` | `app-logistics-shipping` | `ShippingMethodController` | `ShippingMethod` | `shipping_methods` | ✅ Complete |
| **Vendor Support & Helpdesk** | `content/apps/vendor/support-tickets` | `app-vendor-support` | `SupportTicketController` | `SupportTicket` | `support_tickets` | ✅ Complete |
| **Vendor KYC & Verification** | `content/apps/saas/kyc-admin` | `app-saas-kyc-admin` | `KycAdminController` | `VendorKyc` | `vendor_kycs` | ✅ Complete |
| **Workflow Automation Engine** | `content/apps/automation/index` | `app-automation-rules` | `WorkflowAutomationController` | `WorkflowRule` | `workflow_rules` | ✅ Complete |
| **AI Tools & Copilot** | `content/apps/settings/ai` | `app-ecommerce-settings-ai` | `AISettingsController` | `StoreSetting` | `store_settings` | ✅ Complete |
| **Accounting & Ledgers** | `Modules/Accounting/resources/views/*` | `accounting.*` | `AccountingController` | `Ledger, Journal` | `accounting_*` | ✅ Complete |
| **Role & Access Management** | `content/apps/app-access-roles` | `app-access-roles` | `RoleController` | `Role, Permission` | `roles, permissions` | ✅ Complete |
