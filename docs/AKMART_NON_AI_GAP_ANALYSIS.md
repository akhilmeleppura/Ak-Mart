# 🔍 AKMART — COMPLETE NON-AI E-COMMERCE AUDIT & GAP ANALYSIS

**Document ID**: AKMART-DOC-GAP-NONAI-001  
**Lead Architect**: Principal E-Commerce Architect & Senior Laravel Engineer  
**Scope**: Full Non-AI Omnichannel E-Commerce, POS, ERP, Inventory, OMS, CRM, B2B, Marketing, Finance & Logistics Subsystems  
**Date**: August 2026  

---

## 1. EXECUTIVE SUMMARY & STRICT NON-AI SCOPE

In accordance with strict operational directives:
1. **Zero System Duplication**: Existing tables, models, controllers, services, and Blade views are preserved and extended rather than recreated.
2. **AI Boundary Containment**: Retained **AKMart AI Chat / Copilot** as the sole conversational assistant for answering questions about sales, inventory, orders, customers, products, profit, reports, and store policies. All autonomous decision-making agents, automated pricing bots, and complex predictive engines are decoupled from critical financial paths.
3. **Deterministic Financial & Inventory Invariants**: All catalog, inventory, order, coupon, tax, loyalty, and wallet operations are enforced authoritatively by backend Laravel services with atomic database transactions.

---

## 2. COMPREHENSIVE NON-AI FEATURE AUDIT & CLASSIFICATION

| Domain / Subsystem | AKMart Status | Existing Implementation | Genuine Gaps Identified | Planned Architectural Action |
| :--- | :--- | :--- | :--- | :--- |
| **1. Product Management** | 🟡 **PARTIAL** | Single products, multi-variants, categories, attributes, importers, duplicate scanner, catalog health. | Draft status, scheduled publishing dates, bulk action controllers (price/stock/status/category), product archive/restore. | Add bulk update endpoints in `EcommerceProductList`, draft/scheduled scope, and archive/restore methods. |
| **2. Inventory Engine** | ✅ **COMPLETE** | Immutable `stock_movements` ledger, multi-branch isolation, warehouse transfers, cycle counting, ABC analysis, min/max stock. | Overstock & Dead-stock report calculation. | Add overstock and dead-stock aggregation in `ReportController`. |
| **3. Order Management (OMS)** | 🟡 **PARTIAL** | Order state machine, atomic stock debit, POS orders, split fulfillment, public tracking, customer returns. | Admin Order Item editing before fulfillment (Add/Remove item with atomic stock debit/credit), order internal notes. | Implement order item modifier in `EcommerceOrderDetails` and internal notes field. |
| **4. Shipping & Logistics** | 🟡 **PARTIAL** | Driver dispatch portal, shipping methods, delivery slots, tracking timeline. | Dedicated pluggable carrier adapter layer (`ShippingProviderInterface` with Shiprocket & Delhivery adapters). | Create `App\Services\Shipping\ShippingProviderInterface` and carrier adapters. |
| **5. Returns / RMA** | ✅ **COMPLETE** | Self-service return portal, admin inspection condition grading, restocking decision, wallet refund. | None. | Preserved and verified. |
| **6. Payments & Reconciliation** | ✅ **COMPLETE** | `OrderTransaction` ledger, Razorpay / Stripe / Cashfree / COD, webhooks, refund tracking. | Gateway fee calculation schedule per payment type. | Integrated gateway fee deduction in profit calculations. |
| **7. Marketing & Campaigns** | ✅ **COMPLETE** | Dynamic coupons, usage limits, referral reward codes, abandoned cart recovery, dynamic email & WhatsApp templates. | Buy X Get Y promotion rules. | Add BOGO rule calculation in `PricingEngine`. |
| **8. Back-in-Stock & Price Alerts**| ✅ **COMPLETE** | `StockNotification` and `PriceAlert` models with modal triggers on storefront and dispatch hooks. | None. | Preserved and verified. |
| **9. Customer 360 CRM** | ✅ **COMPLETE** | Customer 360 overview, spending history, wishlist, saved carts, support tickets, wallet balance, loyalty points. | Customer groups & tags. | Add customer tagging and group filtering. |
| **10. Loyalty & Gift Cards** | ✅ **COMPLETE** | `StoreCredit` double-entry ledger, `LoyaltyTransaction` points accrual/redemption, `GiftCard` balance tracking. | Tier levels (Silver, Gold, Platinum, VIP). | Add loyalty tier badges based on lifetime spending. |
| **11. B2B Wholesale & Procurement** | ✅ **COMPLETE** | `B2bCompany`, `B2bTierPrice`, `B2bQuote`, `Supplier`, `PurchaseOrder` with goods receiving and auto-inventory increment. | None. | Preserved and verified. |
| **12. POS Terminal** | 🟡 **PARTIAL** | Barcode search, register shift open/close, split payment, thermal receipt view, central stock movement sync. | Hold Cart & Resume Cart session in POS. | Implement Hold/Resume cart state in `PosRegisterController`. |
| **13. Advanced Reporting** | ✅ **COMPLETE** | Sales, inventory, expense, and tax reports with CSV export center. | Custom report column selection. | Add flexible column selection in `AccountingExportController`. |
| **14. Multi-Channel & APIs** | ✅ **COMPLETE** | Standardized `/api/v1/` RESTful JSON platform, Google Shopping XML feed, Meta catalog feeds. | None. | Preserved and verified. |
| **15. Multilingual (6 Langs)** | ✅ **COMPLETE** | English (EN), Malayalam (ML), Hindi (HI), Arabic (AR RTL), French (FR), German (DE). | None. | Preserved and verified. |
| **16. Security & RBAC** | ✅ **COMPLETE** | Password + OTP, Jetstream 2FA, Spatie RBAC with Supreme Admin bypass, anti-SSRF, IDOR protection, CSRF. | None. | Preserved and verified. |
| **17. AI Chat / Copilot** | ✅ **COMPLETE** | Conversational assistant answering sales, inventory, orders, and store policies in 6 languages. | None. | Preserved as sole AI component. |

---

## 3. SUMMARY OF GENUINE GAPS TO IMPLEMENT

1. **Product Management Enhancements**:
   - Bulk Product Updates: Price, Stock, Category, and Status bulk modifier in `EcommerceProductList`.
   - Product Lifecycle: Drafts, Scheduled publishing, Archive and Restore.
2. **Order Management Enhancements**:
   - Order Item Modification before fulfillment (Add/Remove item, Adjust Quantity) with atomic `StockMovement` synchronization.
   - Admin internal order notes and timeline entries.
3. **Pluggable Carrier Abstraction Layer**:
   - `ShippingProviderInterface` with `ShiprocketAdapter`, `DelhiveryAdapter`, and default `LocalFleetAdapter`.
4. **POS Hold & Resume Cart**:
   - Ability for cashier to park an active cart on hold and resume it later.
5. **Marketing BOGO (Buy X Get Y) Calculation**:
   - Integrated into `PricingEngine` and storefront checkout.

---

## 4. ARCHITECTURAL INVARIANTS

1. **Zero Duplicate Tables**: All features extend existing schema (`products`, `orders`, `order_items`, `stock_movements`, `coupons`, `users`, `b2b_companies`, `shipping_methods`).
2. **Strict Test Coverage**: Every enhancement is accompanied by automated feature and unit tests with 100% pass rate.
