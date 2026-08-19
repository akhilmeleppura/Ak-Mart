# AK-Mart — Module Migration & Relocation Map

| Domain Feature | Previous Location | Target Module Location | Migration Status |
|----------------|-------------------|------------------------|------------------|
| **Store Settings** | `app/Http/Controllers/apps/SettingsHubController.php`<br>`resources/views/content/apps/settings/` | `Modules/Settings/` | ✅ MIGRATED |
| **SaaS & Billing** | `app/Http/Controllers/apps/SaaS/*`<br>`resources/views/content/apps/saas/` | `Modules/SaaS/` | ✅ MIGRATED |
| **Vendor & POS** | `app/Http/Controllers/apps/Vendor/*`<br>`resources/views/content/apps/vendor/` | `Modules/Vendor/` | ✅ MIGRATED |
| **Logistics** | `app/Http/Controllers/apps/Logistics/*`<br>`resources/views/content/apps/logistics/` | `Modules/Logistics/` | ✅ MIGRATED |
| **Automation** | `app/Http/Controllers/apps/WorkflowAutomationController.php`<br>`resources/views/content/apps/automation/` | `Modules/Automation/` | ✅ MIGRATED |
| **AI Tools** | `app/Http/Controllers/apps/AI*`<br>`resources/views/content/apps/ai/` | `Modules/AI/` | ✅ MIGRATED |
| **Ecommerce Core** | `app/Http/Controllers/apps/Ecommerce*`<br>`resources/views/content/apps/app-ecommerce-*` | `Modules/Ecommerce/` | ✅ MIGRATED |
| **Accounting** | Modular domain routes & views | `Modules/Accounting/` | ✅ MIGRATED |
| **Permissions** | Modular role management | `Modules/Permission/` | ✅ MIGRATED |