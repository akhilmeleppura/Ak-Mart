# 📊 AK-Mart 2.0 — Complete Feature Matrix

| Feature Domain | Module / File | Database Model | Status | Verification Result |
|---|---|---|---|---|
| **Dual-Mode Auth** | `LoginBasic.php` | `User`, `OtpVerification` | `COMPLETED` | Direct Password & Mobile OTP verified |
| **Account Recovery** | `ForgotPasswordOtpController.php` | `OtpVerification` | `COMPLETED` | 2 Options (Email / Mobile) tested |
| **Storefront Homepage** | `StorefrontController.php` | `CmsBanner`, `Category` | `COMPLETED` | Live dynamic render verified |
| **Shop Catalog & Search** | `StorefrontController.php` | `Product`, `Category` | `COMPLETED` | Faceted filters & keyword search |
| **Product Detail** | `StorefrontController.php` | `Product`, `ProductVariant` | `COMPLETED` | Gallery, EAV specs, reviews |
| **Shopping Cart** | `StorefrontController.php` | Session / `Product` | `COMPLETED` | Dynamic quantity +/- & subtotal |
| **Checkout & Orders** | `StorefrontController.php` | `Order`, `OrderItem` | `COMPLETED` | Stock deduction & order creation |
| **Order Tracking** | `StorefrontController.php` | `Order` | `COMPLETED` | 4-stage visual delivery tracker |
| **Customer Portal** | `CustomerAccountController.php` | `User`, `Order`, `Wishlist` | `COMPLETED` | Dashboard, orders, wishlist, wallet |
| **EAV Attributes** | `ProductAttributeController.php` | `ProductAttribute` | `COMPLETED` | Dynamic types (select, color, text) |
| **POS Quick Sale** | `PosController.php` | `Order`, `StockMovement` | `COMPLETED` | Barcode scanning & receipt generation |
| **POS Shift Register** | `PosRegisterController.php` | `PosRegisterSession` | `COMPLETED` | Opening float $\rightarrow$ Close cash count |
| **Available Stock Formula**| `InventoryService.php` | `StockReservation` | `COMPLETED` | $\text{Physical} - \text{Reserved}$ invariant |
| **Stock Transfers** | `InventoryService.php` | `StockTransfer` | `COMPLETED` | Dispatch $\rightarrow$ Receipt lifecycle |
| **Restock Intelligence** | `InventoryService.php` | `Product`, `Supplier` | `COMPLETED` | Automated replenishment suggestions |
| **Procurement & PO** | `PurchaseOrderController.php` | `PurchaseOrder` | `COMPLETED` | Purchase orders & receiving |
| **Net Profit Engine** | `FinanceService.php` | `Order`, `Expense` | `COMPLETED` | Revenue - COGS - Expenses - Fees |
| **GST Tax Engine** | `FinanceService.php` | `Order` | `COMPLETED` | CGST+SGST vs IGST calculation |
| **Workflow Automation** | `WorkflowAutomationController.php`| `WorkflowRule` | `COMPLETED` | Trigger $\rightarrow$ Condition $\rightarrow$ Action |
| **Accounting Exports** | `AccountingExportController.php` | `Order`, `Expense` | `COMPLETED` | Sales, Expenses, GST CSV streams |
| **REST API v1** | `ApiController.php` | `Product`, `Category` | `COMPLETED` | JSON endpoints with pagination |
| **Newsletter** | `NewsletterController.php` | `NewsletterSubscriber` | `COMPLETED` | Storefront footer subscription |
| **System Diagnostics** | `SystemHealthController.php` | Telemetry Probes | `COMPLETED` | 100/100 Health Score verified |
| **Security Audit Logs** | `SecurityCenterController.php`| `AuditLog` | `COMPLETED` | Immutable audit trail recorded |
