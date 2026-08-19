# 🛒 AK-Mart — Complete Master Feature & Architecture Documentation

**Author & Lead Architect**: Akhil Meleppura  
**Platform**: Advanced E-Commerce ERP, Mini-Mart Management, POS, Multi-Branch Inventory & Automation Platform  
**Technology Stack**: Laravel 12.56.0 | PHP 8.2.12 | MySQL | Sneat Theme | Select2 4.0.13 | Boxicons | Modular Architecture (`Modules/*`)

---

## 1. Executive Summary & System Overview

**AK-Mart** is an enterprise-grade, multi-branch commercial platform engineered to unify online e-commerce with physical retail mini-mart operations. It operates across 14 dedicated business modules, providing end-to-end automation from product sourcing to inventory management, barcode POS checkout, multi-channel customer communication, and financial net profit analytics.

---

## 2. Core Functional Modules & Feature Breakdown

### 🔐 1. Authentication, OTP & Security Center
- **Dual-Mode Login System**:
  - **`🔑 Password Sign In`**: Instant email/username + password login without repeated OTP interruption.
  - **`📱 Mobile / OTP Sign In`**: Passwordless phone number or email sign-in via 6-digit cryptographic OTP.
- **Multi-Channel Account Recovery (Forgot Password)**:
  - **`📧 Reset via Email`**: Dispatches 6-digit verification code to registered email.
  - **`📱 Reset via Mobile`**: Dispatches 6-digit verification code to registered mobile number.
- **Cryptographic OTP Infrastructure**: Single-use invalidation, 5-minute expiration, Bcrypt storage, 60s resend cooldown, brute-force rate-limiting (`OtpService`).
- **Role-Based Access Control (RBAC)**: Supreme Admin (Gate::before bypass), Store Admin, Branch Manager, Inventory Staff, Cashier, Customer.
- **Live Security Center & Audit Trail**: Immutable logging of all sensitive actions (price edits, stock movements, logins, workflow triggers) in `AuditLog`.

---

### 📦 2. Catalog & Advanced Product Management
- **Product Hierarchy**: Simple products, variable products with variant matrices (Size, Color, Flavour), and product bundles/kits.
- **Batch & Expiry Tracking**: Per-batch tracking with manufacturing date, expiry date, and batch cost.
- **Inventory Thresholds**: Minimum stock, reorder point, safety stock, and maximum threshold levels.
- **Financial Fields**: Retail price, sale price, cost price (COGS), profit margins, and tax class.
- **Smart Product Importer & Scraper**:
  - **Universal Scraper & Amazon Extractor**: Extracts title, images, pricing, description, and specs from external URLs with built-in SSRF protection (`SsrfProtectionService`).
  - **Catalog Quality Scanner & Duplicate Auto-Fixer**: Detects duplicate SKUs, missing images, or invalid barcodes with 1-click remediation.

---

### 🏬 3. Multi-Branch & Warehouse Inventory Engine
- **Mathematical Stock Formula**:
  $$\text{Available Stock} = \text{Physical Stock} - \text{Active Reservations}$$
- **Traceable Stock Movement Ledger**: Records every quantity change with before/after counts, reason, user ID, and polymorphic reference.
- **Multi-Branch Stock Transfers**: Complete 4-stage transfer lifecycle (`Pending` $\rightarrow$ `In Transit` $\rightarrow$ `Received` $\rightarrow$ `Completed`).
- **Cycle Counting & Stock Audits**: Physical count reconciliation against expected system stock with discrepancy logging.
- **ABC Inventory Analysis**: Pareto classification (A: Top 80% revenue, B: 15%, C: 5%).
- **Dead Stock & Slow Mover Detection**: Automated identification of products with 0 sales over 60+ days.

---

### 🛒 4. Commercial POS & Barcode Scanner Engine
- **Hardware Scanner Ready**: Keyboard-wedge 1D/2D barcode scanning with instant audio feedback and item addition.
- **Shift & Register Lifecycle**:
  1. **Open Shift**: Set cashier opening cash float.
  2. **Sales Operations**: Quick add, barcode scan, customer selection, discounts, tax computation.
  3. **Split Payments**: Combination of Cash, Card, UPI, Wallet / Store Credit.
  4. **Close Shift & Cash Reconciliation**: Counted cash vs expected cash comparison with discrepancy tracking.
- **Thermal Printable Receipts**: Branded itemized POS receipt with cashier name, order number, and tax details.

---

### 🚚 5. Order Fulfillment, Logistics & RMA Returns
- **12-Stage Order Lifecycle**: `Pending` $\rightarrow$ `Confirmed` $\rightarrow$ `Processing` $\rightarrow$ `Packed` $\rightarrow$ `Ready to Ship` $\rightarrow$ `Shipped` $\rightarrow$ `Out for Delivery` $\rightarrow$ `Delivered` $\rightarrow$ `Cancelled` $\rightarrow$ `Returned` $\rightarrow$ `Refunded` $\rightarrow$ `Failed`.
- **Logistics & Multi-Carrier Shipping**: Shipping zones, weight-based rates, tracking URLs, and branch click-and-collect fulfillment.
- **RMA Return & Refund Lifecycle**: Return request $\rightarrow$ quality inspection $\rightarrow$ refund / store credit issuance / product exchange.

---

### 👥 6. Customer CRM, Loyalty & Wallet Ledger
- **RFM Customer Segmentation**: Configurable automated segments (`New`, `Regular`, `VIP`, `High Value`, `At Risk`, `Inactive`).
- **Immutable Loyalty Point Ledger**: Points earned per purchase, redeemed at POS/checkout, with zero mutable balance overwriting (`LoyaltyTransaction`).
- **Store Credit Wallet**: Customer credit/debit transaction ledger for instant return refunds and balance checkout.
- **Gift Cards & Voucher Engine**: Unique code generation, partial redemption, expiry tracking, and balance lookups.

---

### ⚡ 7. Workflow Automation Engine
- **Trigger $\rightarrow$ Condition $\rightarrow$ Action Pipeline**:
  - **Triggers**: `order.created`, `order.paid`, `product.low_stock`, `cart.abandoned`, `customer.inactive`.
  - **Conditions**: Operators (`>=`, `<=`, `==`, `>`, `<`) matching against order value, branch, stock count, etc.
  - **Actions**: In-app notifications, automatic loyalty point grants, customer tagging, and audit trail logging.

---

### 📊 8. Finance, GST & True Net Profit Engine
- **True Net Profit Calculation**:
  $$\text{Net Profit} = \text{Gross Revenue} - \text{COGS} - \text{Discounts} - \text{Returns} - \text{Operating Expenses} - \text{Payment Fees}$$
- **Indian GST Engine**: Intra-state (CGST 9% + SGST 9%) vs Inter-state (IGST 18%) automatic tax separation.
- **Operating Expense Management**: Categorized expenses (Store Utilities, Rent, Salaries, Marketing) by branch.

---

### 📢 9. Communication Center & Marketing
- **Unified Channel Dispatcher**: Centralized email and WhatsApp messaging with live SMTP testing and status logs.
- **Abandoned Cart Auto-Recovery**: Detects uncompleted checkout sessions and triggers automated recovery reminders.
- **Omnichannel Product Feeds**: Live XML/CSV/JSON feeds for Google Shopping (`/feeds/google.xml`), Meta Catalog (`/feeds/meta.csv`), and TikTok Ads (`/feeds/tiktok.json`).

---

### 🌐 10. Developer Webhooks, System Health & SaaS
- **Developer Webhooks Hub**: Subscription management with payload signing (HMAC-SHA256), automatic retries, and test pinging.
- **Real-Time System Health Diagnostics**: Probes MySQL latency, Cache read/write latency, Local Storage disk I/O, and Queue worker heartbeats.
- **Database Backup Engine**: Automated and manual database snapshots.
- **SaaS Vendor & Multi-Tenancy**: Subscription billing, tiered commission rates, vendor KYC verification, vendor wallets, and payout requests.

---

### 🌍 11. Universal UI Components & Multi-Language
- **Universal Searchable Dropdown**: `<x-searchable-select>` component wrapping Select2 4.0.13 with AJAX debounce, remote pagination, and RTL alignment.
- **Global Search (`Ctrl + K`)**: Modal for searching products, customers, orders, suppliers, and settings across the entire store.
- **6 Supported Languages**: 100% localized across English (`en`), Arabic (`ar`), Hindi (`hi`), Malayalam (`ml`), German (`de`), and French (`fr`).

---

## 3. Complete Page & Route Directory

| URL Route | Route Name | Controller Action | Description |
|---|---|---|---|
| `/` | `root` | `LoginBasic@index` | Public entry point / dashboard redirect |
| `/login` | `login` | `LoginBasic@index` | Unified Dual-Mode Password / Mobile OTP Login |
| `/auth/login-basic` | `auth-login-basic` | `LoginBasic@index` | Dual-mode login view |
| `/auth/otp` | `auth.otp.show` | `OtpController@show` | 6-digit segmented OTP verification screen |
| `/auth/forgot-password/otp` | `auth.forgot-password-otp.request` | `ForgotPasswordOtpController@showRequestForm` | 2-Option Account Recovery (Email / Mobile) |
| `/dashboard` | `dashboard` | `EcommerceDashboard@index` | Main operational analytics dashboard |
| `/pos` | `pos-direct` | `PosController@index` | Barcode Quick Sale POS Terminal |
| `/finance/pos-register` | `app-pos-register` | `PosRegisterController@index` | Cashier register shift management & reconciliation |
| `/products` | `app-ecommerce-product-list` | `EcommerceProductList@index` | Master product catalog list with bulk actions |
| `/products/create` | `app-ecommerce-product-add` | `EcommerceProductAdd@index` | Product creation with variants and attributes |
| `/products/categories` | `app-ecommerce-product-category` | `EcommerceProductCategory@index` | Category hierarchy & management |
| `/inventory` | `app-ecommerce-inventory` | `InventoryController@index` | Branch inventory tracking & stock adjustment |
| `/inventory/warehouses` | `app-warehouses` | `WarehouseController@index` | Multi-warehouse stock management |
| `/inventory/stock-counts` | `app-stock-counts` | `StockCountController@index` | Cycle counting and physical stock reconciliation |
| `/inventory/abc-analysis` | `app-inventory-abc` | `AbcAnalysisController@index` | ABC classification & dead stock detection |
| `/catalog/importer` | `app-product-importer` | `ProductImportController@index` | Smart product URL scraper & import review |
| `/catalog/scanner` | `app-catalog-scanner` | `CatalogScannerController@index` | Duplicate SKU and barcode health scanner |
| `/orders` | `app-ecommerce-order-list` | `EcommerceOrderList@index` | Orders list with live status filter |
| `/orders/{id}` | `app-ecommerce-order-details` | `EcommerceOrderDetails@index` | Order timeline, invoice, items, and shipping info |
| `/purchases` | `app-purchases` | `PurchaseOrderController@index` | Supplier purchase orders and receiving |
| `/suppliers` | `app-suppliers` | `SupplierController@index` | Supplier profiles, catalogs, and lead times |
| `/customers` | `app-ecommerce-customer-all` | `EcommerceCustomerAll@index` | Customer directory, LTV, orders, and balances |
| `/coupons` | `app-ecommerce-coupon-list` | `EcommerceCouponController@index` | Discount promotions and coupon bulk generator |
| `/gift-cards` | `app-gift-cards` | `GiftCardController@index` | Gift card balance lookup and creation |
| `/expenses` | `app-expenses` | `ExpenseController@index` | Branch expense tracking and categories |
| `/automation` | `app-automation` | `WorkflowAutomationController@index` | Trigger-Condition-Action automation rules |
| `/communication` | `app-communication-center` | `CommunicationCenterController@index` | Email & WhatsApp campaign and template manager |
| `/marketing/abandoned-carts` | `app-abandoned-carts` | `AbandonedCartController@index` | Abandoned cart tracking and email recovery |
| `/marketing/feeds` | `app-feeds` | `ProductFeedController@index` | Google Shopping, Meta, and TikTok product feeds |
| `/system/health` | `app-system-health` | `SystemHealthController@index` | MySQL, cache, queue, and storage diagnostics |
| `/system/backups` | `app-backups` | `BackupController@index` | Database snapshot creation and downloads |
| `/system/security-center` | `app-security-center` | `SecurityCenterController@index` | Universal 2FA tracking and immutable audit logs |
| `/settings` | `app-ecommerce-settings` | `SettingsHubController@showSection` | Centralized 12-section configuration hub |
| `/developer/webhooks` | `app-developer-webhooks` | `DeveloperWebhookController@index` | Webhook subscription management and test pings |

---

## 4. Key Database Models & Schema Relationships

- **`User`**: Central identity with OTP verifications, orders, roles, branch assignment, and audit logs.
- **`Product`**: SKU, barcode, price, cost price, stock, category, variants, batch tracking.
- **`StockMovement`**: Traceable immutable stock changes (`before_qty`, `after_qty`, `reason`, `type`).
- **`StockTransfer`**: Multi-branch stock transfers (`from_branch`, `to_branch`, `status`, `items`).
- **`PosRegisterSession`**: Cashier shift ledger (`opening_amount`, `cash_sales`, `closing_amount`, `difference`).
- **`Order` & `OrderItem`**: Full purchase data with status, payment method, branch ID, items, and tax.
- **`LoyaltyTransaction`**: Immutable customer loyalty points ledger.
- **`StoreCredit` & `StoreCreditTransaction`**: Customer digital wallet with transaction history.
- **`WorkflowRule`**: Dynamic automation rules with JSON triggers, conditions, and actions.
- **`AuditLog`**: Immutable security audit records for regulatory compliance.

---

*Documentation prepared for AK-Mart by Lead Architect Akhil Meleppura.*
