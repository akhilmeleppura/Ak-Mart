# AK-Mart 2026 Enterprise Implemented Features Master Directory

Comprehensive catalog of all implemented subsystems, domain services, database schemas, and administrative interfaces across the AK-Mart platform.

---

## 1. Multi-Warehouse & Inventory Engine
- **Multi-Warehouse Management** (`warehouses`, `warehouse_stocks`): Manage regional distribution hubs, allocate SKU balances by warehouse, assign bin location codes (`AISLE-1-SHELF-B`), and track On-Hand vs Committed vs Reserved vs Available stock.
- **Stock Reservation Engine** (`stock_reservations`): Holds inventory for active checkout sessions and pending orders for 30 minutes with automated release of expired reservations to prevent overselling.
- **Cycle Counting & Barcode Stock Audits** (`stock_counts`, `stock_count_items`): Physical count sessions with discrepancy calculations (+/- variance) and 1-click atomic inventory reconciliation logging `StockMovement` records.
- **ABC Inventory Classification & Dead Stock** (`AbcAnalysisController`, `InventoryService`): Evaluates sales revenue velocity to classify products into Class A (Top 80%), Class B (Next 15%), and Class C (Bottom 5%), identifying stagnant SKUs with 60+ days zero sales and calculating tied-up capital.
- **Traceable Stock Movements** (`stock_movements`): Immutable audit ledger capturing 9 movement types (`stock_in`, `stock_out`, `adjustment`, `transfer_in`, `transfer_out`, `damaged`, `expired`, `purchase`, `sale`, `return`) with before/after quantity tracking.

---

## 2. B2B & Wholesale Commerce
- **Corporate Company Accounts** (`b2b_companies`): Account registration with company codes, tax IDs (GSTIN/VAT), credit limits, and customizable net payment terms (`prepaid`, `net_15`, `net_30`, `net_60`).
- **Authorized Corporate Buyers** (`b2b_buyers`): Assign multiple buyers per corporate account with roles (`admin`, `buyer`, `approver`), individual spending limits, and order approval permissions.
- **Contracted Wholesale Tier Pricing** (`b2b_tier_prices`): Account-specific and global volume quantity breaks (MOQ thresholds) with negotiated unit prices.
- **B2B Custom Quotes & RFQs** (`b2b_quotes`, `b2b_quote_items`): Request for Quote generator with tiered bulk discounts, validity periods, and multi-state approval workflow (`draft`, `submitted`, `approved`, `rejected`, `converted`).

---

## 3. Advanced Fulfillment & Split Shipping
- **Split & Multi-Warehouse Fulfillment** (`fulfillment_orders`, `fulfillment_order_items`): Partial and split order fulfillment routing items to distinct warehouses with dedicated tracking numbers and carriers.
- **Pick Lists & Packing Slips** (`fulfillment/pick-pack-list.blade.php`): Printable thermal/A4 pick lists and packing slips with bin locations, SKU codes, and packer signatures.
- **Delivery Slots & Scheduling** (`delivery_slots`): Configurable time windows (e.g. "Morning Priority 9 AM - 1 PM") with day-of-week restrictions and max order capacity limits.
- **Store Pickup / Click & Collect**: Seamless in-store collection option at checkout with branch routing.

---

## 4. Customer Experience, Loyalty & Gift Cards
- **Customer Portal** (`customer/portal.blade.php`): Unified customer dashboard for real-time order tracking, self-service return requests, and store credit balance management.
- **Wishlist & Save for Later** (`wishlists`): 1-click product favoriting with AJAX state sync.
- **Saved Carts & Fast Re-Order** (`saved_carts`): Multi-cart staging for recurring purchases.
- **Digital Gift Cards & Vouchers** (`gift_cards`): Digital card generation, 16-character alphanumeric codes, secure PINs, expiration dates, and lookup API.
- **Store Credit Ledger** (`store_credits`, `store_credit_transactions`): Customer balance ledger supporting refunds, gift grants, and automatic checkout deductions.
- **Customer Intelligence & RFM Segmentation**: Automated categorization into `VIP`, `High Value`, `Regular`, `New`, `At Risk`, and `Inactive` with loyalty points accumulation.

---

## 5. Finance, POS Register Shift & Indian GST Architecture
- **POS Cash Drawer Shift Sessions** (`pos_register_sessions`): Cashier shift management tracking opening float, cash sales, digital payments, expected cash in drawer, actual closing count, and cash drop variance reconciliations.
- **Indian GST Tax Breakdown Engine** (`FinanceService`): Configurable HSN/SAC codes, CGST, SGST, IGST calculations, and intra-state vs inter-state tax rate splitting.
- **Store Profit & Loss Accounting**: Multi-period P&L statement (Gross Revenue - Wholesale COGS - Store Operational Expenses = Net Operating Profit).

---

## 6. Marketing, Cart Recovery & Omnichannel Feeds
- **Abandoned Cart Recovery Engine** (`abandoned_carts`): Automatic tracking of dropped checkouts with recovery token links and 1-click email campaign dispatchers.
- **Google Shopping XML Feed** (`/feeds/google.xml`): RFC-compliant Google Merchant Center RSS 2.0 feed with GTIN/Barcode, MPN, condition, and availability.
- **Meta Commerce Catalog CSV** (`/feeds/meta.csv`): Dynamic Product Ads catalog feed for Facebook and Instagram Shop tagging.
- **TikTok Product Sync JSON** (`/feeds/tiktok.json`): Real-time JSON feed formatted for TikTok Shop Catalog integration.

---

## 7. Developer Hub & Outbound Webhooks
- **Outbound Webhook Engine** (`webhook_subscriptions`, `webhook_logs`): Real-time event notifications (`order.created`, `order.paid`, `order.shipped`, `product.updated`, `inventory.updated`, `customer.created`) with SHA-256 HMAC signature verification, test pinging, and delivery logs.
- **RESTful Storefront API v1** (`/api/v1/*`): Complete public and authenticated endpoints for catalog, categories, cart, order placement, and coupon verification with Laravel Sanctum tokens.

---

## 8. System Health, Security Center & Backups
- **System Health Diagnostics** (`SystemHealthService`): Real-time telemetry monitoring MySQL latency, cache write speed, queue worker status, disk I/O, and PHP/Laravel environment specs.
- **Automated Backup Manager** (`backups`): On-demand SQL and file backup snapshots with SHA-256 checksum verification and restore documentation.
- **Security Center Dashboard** (`SecurityCenterController`): Centralized monitor for 2FA coverage, Supreme Admin privileges, CSRF protection, and model audit logs.
- **👑 Supreme Admin Universal Bypass**: Unrestricted access across all gates, policies, middleware, and branch scopes.
