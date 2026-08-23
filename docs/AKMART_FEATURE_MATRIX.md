# 📊 AKMART — MASTER FEATURE BENCHMARK & GAP MATRIX

**Document ID**: AKMART-DOC-MATRIX-002  
**Benchmarks**: Amazon Seller Central, Flipkart Seller Hub, Shopify Plus, Meesho, Nykaa, Modern AI Commerce  
**Date**: August 2026  

---

## 1. STRATEGIC BENCHMARK MATRIX

| Module / Domain | AKMart Current Status | Benchmark Capability (Amazon / Shopify / Nykaa) | Gap Analysis | Planned Upgrade Action | Priority |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Catalog & Products** | ✅ **COMPLETE** (Single & Variants, Categories, Importers) | Amazon / Shopify: Multi-variant matrix, dynamic bundles, custom attributes. | Support for scheduled publishing, dynamic smart collections, and side-by-side spec comparison. | Implement Smart Collections, Brand Pages, and Product Comparison Drawer. | **P1** |
| **Inventory Engine** | ✅ **COMPLETE** (Multi-Branch, Warehouse, Stock Movement Ledger) | Amazon FBA / Flipkart: Reserved, Damaged, In-Transit stock, Reorder Points. | In-transit stock tracking across purchase receiving and branch transfers. | Add In-Transit, Damaged, and Returned stock segregation in ledger. | **P0** |
| **Storefront UX** | ✅ **COMPLETE** (Hero CMS, Live Search, Responsive, Cart, Checkout) | Shopify / Nykaa: Fast 1-page checkout, Saved For Later, Wishlist, Swatches. | Color swatch selector on catalog and persistent guest-to-user cart sync. | Enhance variant swatch pills and persistent guest cart migration. | **P1** |
| **OMS (Order Management)** | ✅ **COMPLETE** (Atomic stock debit, status pipeline, public tracking) | Amazon / Shopify: Split orders, partial fulfillment, multi-warehouse routing. | Split shipment allocation when items originate from separate branches. | Extend `FulfillmentOrder` to support multi-shipment dispatch. | **P1** |
| **Shipping & Logistics** | ✅ **OPERATIONAL** (Driver portal, shipping methods, delivery slots) | Shiprocket / Delhivery: Real-time pincode serviceability, automated AWB generation. | Carrier abstraction adapter layer for plug-and-play Indian/Global couriers. | Implement `ShippingProviderInterface` with Shiprocket & Delhivery adapters. | **P1** |
| **Returns / RMA** | ✅ **COMPLETE** (Customer self-service returns, admin approval, restocking) | Amazon RMA: Reverse pickup generation, inspection condition grading. | Return reason classification and automated store credit refund toggle. | Add reverse pickup tracking and automated store credit issuance. | **P1** |
| **CRM & Customer 360** | ✅ **COMPLETE** (Customer profile, loyalty points, store credit, wallet) | Shopify / Nykaa: RFM segmentation, Customer Lifetime Value (CLV), Churn risk. | Dynamic segmentation builder (e.g. VIP, Inactive, Cart Abandoners). | Add RFM scoring and natural language customer segment filters. | **P2** |
| **POS Terminal** | ✅ **COMPLETE** (Barcode scan, cash drawer, split payment, shift registers) | Square / Shopify POS: Atomic offline sync, unified customer rewards. | Offline IndexedDB buffer for zero-downtime offline checkout. | Enhance service worker cache and offline queue synchronization. | **P1** |
| **Marketing & Offers** | ✅ **COMPLETE** (Coupons, Flash deals, Bundle discounts, Referral codes) | Amazon Lightning Deals / Shopify Scripts: Tiered volume pricing, Buy X Get Y. | Buy X Get Y promotion rules and scheduled flash sales countdown timers. | Add BOGO rule builder and automated flash sale price schedules. | **P2** |
| **AI Commerce Core** | ✅ **OPERATIONAL** (AI Content generator, Multi-lingual copilot, Fallbacks) | 2026 AI Commerce: "Ask Your Store" Natural Language BI, Semantic Search. | Semantic catalog query parser ("budget phone with good camera under ₹15,000"). | Build AI Semantic Search query planner & Admin AI Copilot command center. | **P3** |
| **Predictive AI** | 🟡 **PLANNED** (Deterministic algorithms in place) | Amazon Supply Chain AI: Daily demand forecasting, stockout risk prediction. | Predictive reorder quantity suggestions based on velocity and lead time. | Implement `AIForecastingService` calculating stockout velocity & reorder alerts. | **P3** |
| **Marketplace / Multi-Vendor** | ✅ **OPERATIONAL** (Vendor KYC, vendor wallets, SaaS dunning, commissions) | Amazon Seller Central: Vendor self-service portal, automated payout payouts. | Vendor dashboard with commission settlement statements and dispute center. | Extend Vendor Portal with automated commission settlement ledger. | **P4** |
| **Finance & Profit** | ✅ **COMPLETE** (True Net Profit engine, GST compliance, Expenses, CSV Export)| Modern ERP: Order-level gross/net margin, gateway fee deductions. | Automatic gateway fee calculation per transaction type (UPI vs Credit Card). | Integrate gateway fee schedules into order profit reconciliation. | **P1** |
| **Communication** | ✅ **COMPLETE** (Dynamic email templates, WhatsApp Cloud API, Webhooks) | Klaviyo / Intercom: Event-triggered omnichannel notification workflows. | Automated WhatsApp order update dispatches and cart recovery links. | Connect WhatsApp trigger events on order status change. | **P1** |
| **Enterprise Hardening**| ✅ **COMPLETE** (RBAC, Anti-SSRF, Rate limiting, Audit logs, 81 Tests)| AWS Enterprise Standard: Redis caching, automated backup health testing. | Redis query cache tags for instant catalog and category reads. | Configure Redis caching layer for product listings and category trees. | **P0** |

---

## 2. FEATURE STATUS CLASSIFICATION SUMMARY

- **COMPLETE (18 Modules)**: Authentication, RBAC, Core Catalog, Multi-Branch Inventory, POS Terminal, Order Placement, Public Tracking, Verified Reviews, Dynamic Bundles, Store Credit/Wallet, Loyalty Points, Dynamic Mail Templates, GST Tax Engine, Expense Management, Smart Importers, Internationalization (6 Langs), RESTful API v1, Automated Testing Suite.
- **PARTIAL / NEEDS REFACTOR (6 Modules)**: Omnichannel Shipping Carrier Adapter, Customer 360 RFM Segmentation Builder, BOGO Promotional Rules, Vendor Settlement Statements, Guest Cart Migration, Split Order Shipments.
- **AI & PREDICTIVE UPGRADES (6 Modules)**: AI Daily Business Brief, AI Semantic Search Planner, AI Demand Forecasting, AI Anomaly Detection, AI Pricing Assistant, AI Review Sentiment Aggregator.
