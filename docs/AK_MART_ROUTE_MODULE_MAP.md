# AK-Mart — Route to Module Mapping

| Module | Route File | Canonical Route Names | Total Endpoints |
|--------|------------|-----------------------|-----------------|
| **Ecommerce** | `Modules/Ecommerce/routes/web.php` | `app-ecommerce-dashboard`, `app-ecommerce-product-*`, `app-ecommerce-order-*`, `app-ecommerce-customer-*` | 42 Routes |
| **Settings** | `Modules/Settings/routes/web.php` | `app-ecommerce-settings-details`, `settings.section`, `settings.section.save`, `settings.test-smtp` | 18 Routes |
| **SaaS** | `Modules/SaaS/routes/web.php` | `app-saas-analytics`, `app-saas-commissions`, `app-saas-kyc-*`, `app-saas-dunning` | 15 Routes |
| **Vendor** | `Modules/Vendor/routes/web.php` | `app-vendor-pos`, `app-vendor-kyc`, `app-vendor-wallet`, `app-vendor-support-*`, `app-vendor-inventory` | 18 Routes |
| **Logistics** | `Modules/Logistics/routes/web.php` | `app-logistics-shipping`, `app-logistics-shipping-store`, `app-logistics-shipping-destroy` | 4 Routes |
| **Automation** | `Modules/Automation/routes/web.php` | `app-automation-rules`, `app-automation-rules-store`, `app-automation-rules-toggle` | 4 Routes |
| **Accounting** | `Modules/Accounting/routes/web.php` | `accounting.ledger.*`, `accounting.trial-balance.*`, `accounting.journal.*` | 24 Routes |
| **Permission** | `Modules/Permission/routes/web.php` | `roles.*`, `permissions.*`, `role.view`, `permissions.index` | 12 Routes |