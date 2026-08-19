# AK-Mart Changelog & Release Notes

## Version 5.0.0 — 2026 Next-Generation Enterprise Commerce Architecture

### 1. Multi-Warehouse & Inventory Subsystem
- Created `warehouses` and `warehouse_stocks` tables allowing multi-location stock allocations and bin location assignments (`AISLE-1-SHELF-B`).
- Created `stock_reservations` holding inventory for 30 minutes during active checkouts with automated release of expired reservations.
- Created `stock_counts` & `stock_count_items` cycle counting and barcode audit engine with difference calculations and atomic live reconciliation.
- Implemented ABC Inventory Analysis engine (`AbcAnalysisController`, `InventoryService`) classifying products into Class A (Top 80% revenue), Class B (Next 15%), and Class C (Bottom 5%), identifying dead stock (60+ days zero sales) and tied-up capital.

### 2. B2B & Wholesale Commerce
- Created `b2b_companies` for corporate accounts with tax IDs (GSTIN/VAT), credit limits, and net payment terms (`prepaid`, `net_15`, `net_30`, `net_60`).
- Created `b2b_buyers` assigning multiple buyers per corporate account with roles (`admin`, `buyer`, `approver`) and spending limits.
- Created `b2b_tier_prices` for account-specific and global volume quantity breaks (MOQ).
- Created `b2b_quotes` & `b2b_quote_items` for custom RFQ quote requests and multi-state approval workflows (`draft`, `submitted`, `approved`, `rejected`, `converted`).

### 3. Advanced Fulfillment & Split Shipping
- Created `fulfillment_orders` & `fulfillment_order_items` supporting partial and split order fulfillment from multiple warehouses.
- Created printable Pick Lists & Packing Slips (`fulfillment/pick-pack-list.blade.php`) with bin locations and verification checklists.
- Created `delivery_slots` for scheduled local delivery time windows (e.g. "Morning Priority 9 AM - 1 PM") with order capacity caps.
- Added Store Pickup (Click & Collect) option in orders and checkout.

### 4. Customer Experience, Wishlist, Gift Cards & Store Credit
- Created unified Customer Account Portal (`customer/portal.blade.php`) with order history, return requests, and balance tracking.
- Created `wishlists` (1-click product favoriting) and `saved_carts` (multi-cart staging).
- Created `gift_cards` issuing 16-character alphanumeric digital vouchers with secure PINs and lookup APIs.
- Created `store_credits` & `store_credit_transactions` ledger for refunds and checkout deductions.

### 5. Finance, POS Register Shift & Indian GST Architecture
- Created `pos_register_sessions` for cashier shift tracking (opening float, cash sales, expected cash in drawer, closing count, and cash drop variance reconciliations).
- Implemented Indian GST Tax Breakdown Engine (`FinanceService`) with configurable HSN/SAC codes, CGST, SGST, IGST calculations.

### 6. Marketing, Cart Recovery & Omnichannel Feeds
- Created `abandoned_carts` engine tracking dropped checkouts with recovery token links and 1-click email campaign dispatchers.
- Created Google Shopping XML RSS 2.0 feed (`/feeds/google.xml`), Meta Commerce Catalog CSV (`/feeds/meta.csv`), and TikTok Product Sync JSON (`/feeds/tiktok.json`).

### 7. Developer Hub & Outbound Webhooks
- Created `webhook_subscriptions` & `webhook_logs` engine dispatching real-time events (`order.created`, `order.paid`, `order.shipped`, `product.updated`, `inventory.updated`, `customer.created`) with SHA-256 HMAC signatures and test pinging.

### 8. System Health, Security Center & Backup Manager
- Created `SystemHealthService` real-time diagnostic telemetry monitoring MySQL latency, cache write speed, queue status, disk I/O, and PHP/Laravel environment specs.
- Created `backups` manager for on-demand SQL snapshot creation with MD5/SHA-256 checksum verification.
- Created Security Center monitoring 2FA coverage, Supreme Admin privileges, and live audit logs.

### 9. Decoupled AI-Ready Domain Service Layer
- Created clean domain services: `ProductService`, `InventoryService`, `B2bService`, `FulfillmentService`, `FinanceService`, `MarketingService`, `ProductFeedService`, `WebhookDispatcher`, `SystemHealthService`, `OrderService`.
