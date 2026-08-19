# AK-Mart — Blade → Route → Controller Relationship Matrix

| Primary Blade File | Route Name | HTTP Method | Controller & Action | Primary Model | Status |
|--------------------|------------|-------------|---------------------|---------------|--------|
| `resources/views/content/apps/app-ecommerce-dashboard.blade.php` | `app-ecommerce-dashboard` | `GET` | `EcommerceDashboard@index` | `Order, Product` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-product-list.blade.php` | `app-ecommerce-product-list` | `GET` | `EcommerceProductList@index` | `Product, Category` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-product-add.blade.php` | `app-ecommerce-product-add` | `GET/POST` | `EcommerceProductAdd@index/store` | `Product` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-order-list.blade.php` | `app-ecommerce-order-list` | `GET` | `EcommerceOrderList@index` | `Order, OrderItem` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-order-details.blade.php` | `app-ecommerce-order-details` | `GET` | `EcommerceOrderDetails@index` | `Order, User` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-customer-all.blade.php` | `app-ecommerce-customer-all` | `GET` | `EcommerceCustomerAll@index` | `User` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-customer-details-overview.blade.php` | `app-ecommerce-customer-details-overview` | `GET` | `EcommerceCustomerDetailsOverview@index` | `User, Order` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-customer-details-security.blade.php` | `app-ecommerce-customer-details-security` | `GET` | `EcommerceCustomerDetailsSecurity@index` | `User` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-customer-details-billing.blade.php` | `app-ecommerce-customer-details-billing` | `GET` | `EcommerceCustomerDetailsBilling@index` | `User, Order` | ✅ OPERATIONAL |
| `resources/views/content/apps/app-ecommerce-customer-details-notifications.blade.php` | `app-ecommerce-customer-details-notifications` | `GET` | `EcommerceCustomerDetailsNotifications@index` | `User` | ✅ OPERATIONAL |
| `resources/views/content/apps/vendor/inventory.blade.php` | `app-ecommerce-inventory` | `GET` | `InventoryController@index` | `Product, Warehouse` | ✅ OPERATIONAL |
| `resources/views/content/apps/saas/billing.blade.php` | `billing.index` | `GET` | `SubscriptionController@index` | `TenantSubscription, SubscriptionPlan` | ✅ OPERATIONAL |
| `resources/views/content/pages/pages-account-settings-account.blade.php` | `pages-account-settings-account` | `GET` | `ProfileController@edit` | `User` | ✅ OPERATIONAL |
| `resources/views/content/pages/pages-profile-user.blade.php` | `profile.show` | `GET` | `ProfileController@show` | `User` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/store.blade.php` | `app-ecommerce-settings-details` | `GET/POST` | `SettingsHubController@showSection` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/email.blade.php` | `settings.section (email)` | `GET/POST` | `SettingsHubController@showSection` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/whatsapp.blade.php` | `settings.section (whatsapp)` | `GET/POST` | `SettingsHubController@showSection` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/payments.blade.php` | `app-ecommerce-settings-payments` | `GET/POST` | `SettingsHubController@showSection` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/shipping.blade.php` | `app-ecommerce-settings-shipping` | `GET/POST` | `SettingsHubController@showSection` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/ai.blade.php` | `app-ecommerce-settings-ai` | `GET/POST` | `AISettingsController@index/store` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/settings/branding.blade.php` | `app-ecommerce-settings-branding` | `GET/POST` | `EcommerceSettingsBranding@index` | `StoreSetting` | ✅ OPERATIONAL |
| `resources/views/content/apps/logistics/shipping-methods.blade.php` | `app-logistics-shipping` | `GET/POST/DEL` | `ShippingMethodController@index/store/destroy` | `ShippingMethod` | ✅ OPERATIONAL |
| `resources/views/content/apps/saas/kyc-admin.blade.php` | `app-saas-kyc-admin` | `GET/POST` | `KycAdminController@index/approve` | `VendorKyc` | ✅ OPERATIONAL |
| `resources/views/content/apps/saas/kyc-detail.blade.php` | `app-saas-kyc-show` | `GET` | `KycAdminController@show` | `VendorKyc` | ✅ OPERATIONAL |
| `resources/views/content/apps/vendor/support-tickets.blade.php` | `app-vendor-support` | `GET` | `SupportTicketController@index` | `SupportTicket` | ✅ OPERATIONAL |
| `resources/views/content/apps/vendor/support-ticket-show.blade.php` | `app-vendor-support-show` | `GET/POST` | `SupportTicketController@show/reply` | `SupportTicket, TicketMessage` | ✅ OPERATIONAL |
| `resources/views/content/apps/automation/index.blade.php` | `app-automation-rules` | `GET/POST` | `WorkflowAutomationController@index/store` | `WorkflowRule` | ✅ OPERATIONAL |
| `resources/views/content/apps/pos/index.blade.php` | `app-vendor-pos` | `GET/POST` | `PosController@index/checkout` | `Product, Order` | ✅ OPERATIONAL |
