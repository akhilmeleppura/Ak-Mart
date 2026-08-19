# AK-Mart — Controller to Module Mapping

| Module | Controller Class | Action Methods | Domain Responsibility |
|--------|------------------|----------------|-----------------------|
| **Ecommerce** | `EcommerceDashboard` | `index` | Main store metrics, alert insights, sales velocity |
| **Ecommerce** | `EcommerceProductList` | `index, destroy, bulkStatus` | Catalog management, SKU lookup |
| **Ecommerce** | `EcommerceProductAdd` | `index, store, edit, update` | Product creation, image upload, variant builder |
| **Ecommerce** | `EcommerceOrderList` | `index, show, updateStatus` | Order processing, fulfillment workflows |
| **Ecommerce** | `EcommerceCustomerAll` | `index, store, update, destroy`| Customer CRM registry |
| **Settings** | `SettingsHubController` | `showSection, saveSection, testSmtp` | Centralized 28-section settings coordinator |
| **SaaS** | `SubscriptionController` | `index, subscribe, cancel, invoicePreview` | Tenant plan subscriptions & PDF billing invoices |
| **SaaS** | `KycAdminController` | `index, show, approve, reject, review` | Platform vendor identity compliance verification |
| **Vendor** | `PosController` | `index, search, checkout` | Point of sale cashier register |
| **Vendor** | `SupportTicketController` | `index, show, reply, updateStatus` | Multi-branch vendor support desk |
| **Vendor** | `WalletController` | `index, requestPayout` | Vendor commission ledger & withdrawal management |
| **Logistics** | `ShippingMethodController` | `index, store, toggle, destroy` | Carrier shipping rate tables & zone config |
| **Automation**| `WorkflowAutomationController` | `index, store, toggle, destroy, trigger` | Rule-based automation & event notifications |