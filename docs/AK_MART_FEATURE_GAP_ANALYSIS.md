# AK-Mart 2026 Deep Feature Gap Analysis

Compiled based on full inspection of the current AK-Mart codebase against a 2026 enterprise omni-channel, B2B, POS, and AI-ready commerce architecture.

## Evaluation Matrix (55 Feature Domains)

| ID | Feature Domain | Status | Current Codebase Implementation | Missing 2026 Capabilities | Implementation Action |
| :--- | :--- | :---: | :--- | :--- | :--- |
| 1 | **Commerce** | `COMPLETED` | Cart, orders, tax, checkout, coupons, currencies, multiple guards | Pre-orders, backorders, split checkout | Implement pre-order/backorder engine. |
| 2 | **Products** | `COMPLETED` | Single/Multi product CRUD, SKU, barcode, brand, min/max stock, categories | Product bundling, kit relationships | Build Product Bundles subsystem. |
| 3 | **Product Variants** | `COMPLETED` | Interactive multi-attribute variants (Color, Size, RAM, Storage, Weight) with SKU/Price/Stock | Matrix price rules, quantity breaks | Add quantity break pricing. |
| 4 | **Bundles** | `NOT IMPLEMENTED` | Products treated individually | Virtual bundles, fixed & custom kits, bundle stock deduction | Create `product_bundles` table & model. |
| 5 | **Pricing** | `PARTIALLY COMPLETED` | Base price, compare-at price, coupon discounts | Tier pricing, quantity breaks, B2B price lists, customer group pricing | Implement `TierPrice` & `B2bPrice` models. |
| 6 | **Inventory** | `COMPLETED` | `StockMovement` ledger (9 movement types), stock adjustments, valuation | Committed stock vs available stock, stock reservations | Add stock reservations & committed qty. |
| 7 | **Warehouses** | `NOT IMPLEMENTED` | Single branch allocation per product | Multi-warehouse routing, bin locations, warehouse transfers | Create `warehouses` & `warehouse_stock` schema. |
| 8 | **Branches** | `COMPLETED` | Multi-branch model, branch switcher, inter-branch stock transfers | Branch-specific fulfillment routing | Link branch inventory with local fulfillment. |
| 9 | **Purchases** | `COMPLETED` | Purchase orders, PO items breakdown, atomic receiving workflow | Automated reorder suggestions based on sales velocity | Add automated reorder suggestion calculator. |
| 10 | **Suppliers** | `COMPLETED` | Supplier directory, balance tracking, purchase history | Supplier lead times, supplier performance metrics | Add supplier lead times and analytics. |
| 11 | **POS** | `COMPLETED` | Barcode scanning, category filters, cart, cash/card/UPI, receipt modal | Offline cache sync, cash drawer register closing | Implement POS Cash Reconciliation / Register Closing. |
| 12 | **Offline POS** | `PARTIALLY COMPLETED` | Web-based terminal with barcode search | Service worker offline caching & IndexedDB sync | Add offline transaction queue structure. |
| 13 | **Orders** | `COMPLETED` | Order creation, status transitions, line items, transaction records | Split shipments, fulfillment order mapping | Create `fulfillment_orders` and tracking. |
| 14 | **Returns** | `COMPLETED` | Return requests lifecycle (Pending/Approved/Refunded), restock movement | Self-service customer return portal | Build Customer Return Portal view & endpoints. |
| 15 | **Refunds** | `COMPLETED` | Full & partial refund calculations, order status synchronization | Store credit refund option | Add Store Credit refund destination. |
| 16 | **Fulfillment** | `PARTIALLY COMPLETED` | Basic shipments table with tracking IDs | Multi-location split fulfillment, pick lists, pack lists | Build `FulfillmentService`, pick & pack sheets. |
| 17 | **Shipping** | `COMPLETED` | Shipping methods, rates, carrier tracking numbers | Real-time rate calculating API & zone matrix | Expand shipping method calculation service. |
| 18 | **Delivery** | `PARTIALLY COMPLETED` | Standard delivery addresses | Local scheduled delivery & time slots | Create Delivery Slots architecture. |
| 19 | **Pickup** | `NOT IMPLEMENTED` | Ship-to-address only | Click & Collect / In-store pickup scheduling | Add Store Pickup option in checkout & orders. |
| 20 | **Customers** | `COMPLETED` | Customer directory, full profiles, segmentation groups | Saved carts, Buy Again, Wishlist, Compare products | Implement Wishlist, Buy Again & Saved Carts. |
| 21 | **CRM** | `COMPLETED` | RFM segmentation (`VIP`, `High Value`, `Regular`, `At Risk`, `New`), AOV | Timeline history, customer communication logs | Add Customer Activity Timeline. |
| 22 | **Loyalty** | `COMPLETED` | `loyalty_transactions` ledger, earning rules (1 pt / $10), redemption | Tier-based reward multiplier perks | Add tier multipliers (Silver/Gold/Platinum). |
| 23 | **Gift Cards** | `NOT IMPLEMENTED` | Coupons only | Digital Gift Cards with code, balance, expiry, checkout apply | Create `gift_cards` table, model & checkout logic. |
| 24 | **Store Credit** | `NOT IMPLEMENTED` | None | Customer store credit balance, credit ledger, checkout deduction | Create `store_credits` table & model. |
| 25 | **Coupons** | `COMPLETED` | Fixed/percentage, min spend, usage limits, server validation API | Cart rule combinations, customer-exclusive coupons | Retain & expand validation logic. |
| 26 | **Subscriptions** | `COMPLETED` | SaaS subscription plans, tenant billing, dunning cycle | Recurring product subscriptions (Subscribe & Save) | Add Product Subscription frequency configuration. |
| 27 | **B2B** | `NOT IMPLEMENTED` | B2C retail users only | Company accounts, multiple buyers, credit limits, net terms | Create `b2b_companies`, `b2b_buyers` architecture. |
| 28 | **Wholesale** | `NOT IMPLEMENTED` | Retail pricing only | Wholesale MOQ, bulk CSV ordering, quantity tiers | Create Bulk / Quick SKU order screen. |
| 29 | **Customer Pricing**| `NOT IMPLEMENTED` | Global catalog price | Account-specific negotiated pricing | Create `customer_pricing` table & price resolver. |
| 30 | **Quotes** | `NOT IMPLEMENTED` | Direct checkout only | Request a Quote, quote estimation, quote approval workflow | Build B2B Quote Management system. |
| 31 | **Checkout** | `COMPLETED` | One-page / multi-step checkout with coupons, tax, addresses | Split payment, delivery slot selection, pickup selector | Upgrade checkout with pickup/slots/gift cards. |
| 32 | **Payments** | `COMPLETED` | Cash, Card, UPI, Stripe webhooks, PayPal | Gift Card & Store Credit split checkout | Add Gift card & store credit deduction. |
| 33 | **Reconciliation** | `PARTIALLY COMPLETED` | Order transactions log | Daily POS cash drawer closing, payment gateway reconciliation | Build Daily Cash & Payment Reconciliation screen. |
| 34 | **GST** | `PARTIALLY COMPLETED` | Configurable tax percentages | HSN/SAC codes, CGST, SGST, IGST calculation, GSTIN invoices | Build GST Invoice & HSN Tax Engine. |
| 35 | **Expenses** | `COMPLETED` | Expense categories, expense entries, receipt reference, P&L link | Recurring expense scheduler | Retain & integrate with accounting feeds. |
| 36 | **Accounting** | `COMPLETED` | Chart of accounts, journal entries, P&L reporting | Export to QuickBooks/Tally format | Add Tally/ERP accounting XML/CSV exporter. |
| 37 | **Analytics** | `COMPLETED` | Sales trends, revenue vs COGS vs expenses, top products, AOV | ABC inventory analysis, stock aging, dead stock detection | Implement ABC Inventory Analysis & Dead Stock. |
| 38 | **Forecasting** | `COMPLETED` | Deterministic 7-day and 30-day moving average sales forecasting | Reorder stock forecasting based on lead times | Add intelligent reorder quantity calculation. |
| 39 | **Marketing** | `PARTIALLY COMPLETED` | Referral system, customer reviews | Abandoned cart recovery campaigns, win-back rules | Build Abandoned Cart Tracker & Recovery Engine. |
| 40 | **Abandoned Carts** | `NOT IMPLEMENTED` | None | Session/cart tracking, recovery email triggers, discount offers | Create `abandoned_carts` table & automated recovery. |
| 41 | **Automation** | `COMPLETED` | `workflow_rules` engine (Triggers -> Conditions -> Actions) | Multi-step webhook and email dispatch | Expand rule triggers & automated jobs. |
| 42 | **Product Feeds** | `NOT IMPLEMENTED` | None | Google Shopping (XML/RSS), Meta Catalog (CSV), TikTok (JSON) | Build Omnichannel Product Feed Generator. |
| 43 | **Omnichannel** | `PARTIALLY COMPLETED` | Unified POS and Online store catalog & stock | Marketplace export endpoints & channel configurations | Implement Channel Registry & Feed Exporter. |
| 44 | **API** | `COMPLETED` | RESTful API v1 for products, categories, cart, orders, coupons | Webhook event notifications, rate limiting | Expand API endpoints and webhook subscribers. |
| 45 | **Webhooks** | `NOT IMPLEMENTED` | Incoming payment webhooks only | Outgoing webhooks (`order.created`, `order.shipped`, etc.) with logs | Create `webhook_subscriptions` & dispatcher. |
| 46 | **Developer Tools** | `PARTIALLY COMPLETED` | API routes, Sanctum tokens | Webhook logs, API request inspector, test payload sender | Build Developer API & Webhook Management UI. |
| 47 | **Security** | `COMPLETED` | Supreme Admin universal Gate, CSRF, RBAC, XSS prevention, 2FA | Security Center dashboard, login audit history | Build Security Center monitoring dashboard. |
| 48 | **Backup** | `NOT IMPLEMENTED` | Manual database dump | Admin Backup manager (Database & Storage), backup logs & verification | Build Automated Backup Management System. |
| 49 | **System Health** | `PARTIALLY COMPLETED` | Laravel health endpoint `/up` | Comprehensive Admin Diagnostics (DB, Cache, Queue, Mail, Storage) | Build AK-Mart System Health Diagnostic Dashboard. |
| 50 | **AI** | `COMPLETED` | AI Copilot, content generator, optimizer, attribute extractor | Agentic commerce service interfaces | Create Clean Service Architecture for AI access. |
| 51 | **AI Importer** | `COMPLETED` | URL structured data scraper (JSON-LD, Schema.org, OpenGraph) + review | Batch URL queue processing | Retain & optimize staging workflow. |
| 52 | **AI Optimization**| `COMPLETED` | Product Quality Score (0-100), diagnosis, actionable suggestions | Auto-repair execution approval | Retain & enhance. |
| 53 | **AI Recommendations**| `COMPLETED` | Deterministic cross-sell, up-sell, and category matching | Frequently bought together rule engine | Implement `RecommendationService`. |
| 54 | **AI Shopping Asst** | `COMPLETED` | AI Copilot chat widget with supreme access authorization | Catalog-grounded product discovery | Retain & connect to `SearchService`. |
| 55 | **Agentic Commerce**| `PARTIALLY COMPLETED` | REST API endpoints for agent operations | Clean Service layer (`ProductService`, `InventoryService`, `OrderService`) | Implement decoupled Domain Service Architecture. |
