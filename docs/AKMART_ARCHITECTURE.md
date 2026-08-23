# 🏗️ AKMART — ENTERPRISE SYSTEM & MODULAR ARCHITECTURE

**Document ID**: AKMART-DOC-ARCH-003  
**Architecture Style**: Clean Modular Monolith + Service Layer  
**Target Platform**: AKMart Omnichannel Operating System  
**Date**: August 2026  

---

## 1. HIGH-LEVEL SYSTEM ARCHITECTURE

```text
                                 ╔═════════════════════════════════════╗
                                 ║         CLIENT ACCESS CHANNELS      ║
                                 ╚═════════════════════════════════════╝
                                    │               │               │
                     ┌──────────────┴───────┐       │       ┌───────┴──────────────┐
                     ▼                      ▼       ▼       ▼                      ▼
              Customer Web             Mobile / PWA    POS Terminal        Admin & Vendor Portal
             (Blade / HTML5)          (Responsive)    (Sneat / JS)          (Sneat Jetstream)
                     │                      │               │                      │
                     └──────────────┬───────┴───────┬───────┴──────────────────────┘
                                    │               │
                                    ▼               ▼
                        ╔═══════════════════════════════════════╗
                        ║     SECURITY & GATEWAY MIDDLEWARE     ║
                        ╠═══════════════════════════════════════╣
                        ║ • CSRF / Sanctum API Authentication   ║
                        ║ • Spatie RBAC & Supreme Admin Bypass  ║
                        ║ • Multi-Branch Scoping Middleware     ║
                        ║ • 6-Language Locale & RTL Resolver    ║
                        ║ • Anti-SSRF & Rate Limiter Guards     ║
                        ╚═══════════════════════════════════════╝
                                            │
                                            ▼
                        ╔═══════════════════════════════════════╗
                        ║          COMMERCE CORE SERVICES       ║
                        ╠═══════════════════════════════════════╣
                        ║ • ProductService & PricingEngine      ║
                        ║ • InventoryService (Ledger & Locks)   ║
                        ║ • OrderService & FulfillmentService   ║
                        ║ • TaxEngineService (GST / HSN Rules)  ║
                        ║ • CommunicationService (Mail/WhatsApp)║
                        ║ • FinanceService (True Net Profit)    ║
                        ║ • SystemHealthService & BackupManager ║
                        ╚═══════════════════════════════════════╝
                                            │
                                            ▼
                        ╔═══════════════════════════════════════╗
                        ║       INTELLIGENCE & AUTOMATION       ║
                        ╠═══════════════════════════════════════╣
                        ║ • AI Copilot & Content Generator      ║
                        ║ • Deterministic Fallback Engine       ║
                        ║ • Workflow Automation Rules Matrix    ║
                        ║ • Omnichannel Feeds (Google/Meta)     ║
                        ║ • Webhook Dispatcher & Handlers       ║
                        ╚═══════════════════════════════════════╝
                                            │
                                            ▼
                        ╔═══════════════════════════════════════╗
                        ║        PERSISTENCE & DATA STORAGE     ║
                        ╠═══════════════════════════════════════╣
                        ║ • Relational DB (Transactions/Ledger) ║
                        ║ • Redis / Cache Storage               ║
                        ║ • Async Database Queues (Jobs/Events) ║
                        ║ • S3 / Local Media Storage Disk       ║
                        ╚═══════════════════════════════════════╝
```

---

## 2. MODULAR BOUNDARIES & DOMAIN MAP

AKMart leverages `nwidart/laravel-modules` for domain modularity:

1. **`Modules/Ecommerce`**: Product catalog, categories, attributes, reviews, questions, coupons, and orders.
2. **`Modules/Logistics`**: Warehouses, branches, shipping methods, delivery slots, and driver dispatching.
3. **`Modules/AI`**: AI Copilot controller, content generation tools, and multi-model abstraction adapter.
4. **`Modules/Accounting`**: Expenses, net profit calculation, GST compliance reports, and CSV export center.
5. **`Modules/Billing`**: POS terminal sessions, cash drawer management, split-payments, and receipts.
6. **`Modules/Communication`**: Email dynamic templates, WhatsApp Cloud API integration, and audit logs.
7. **`Modules/Automation`**: Trigger-Condition-Action automation builder and abandoned cart notifications.
8. **`Modules/Permission`**: Role hierarchies, granular permissions, and user assignment.
9. **`Modules/SaaS` & `Modules/Vendor`**: Multi-tenant vendor onboarding, KYC verification, vendor wallets, and dunning engine.
10. **`Modules/Settings`**: Store parameters, payment gateways, tax rules, and internationalization.

---

## 3. DATA INTEGRITY & FINANCIAL SAFETY PRINCIPLES

- **Immutable Stock Movement Ledger**: Stock is never directly updated without recording a corresponding `StockMovement` row with `quantity_change`, `previous_quantity`, `new_quantity`, `reason`, and `user_id`.
- **Concurrency & Race Condition Protection**: Checkout and POS deductions use database transactions with `lockForUpdate()` on product and inventory records.
- **Server-Side Financial Authority**: Client-side prices, discounts, taxes, and shipping rates are strictly treated as display hints. Final totals are calculated authoritatively by `PricingEngine`, `TaxEngineService`, and `OrderService`.
- **Deterministic AI Safety**: AI services cannot directly modify database records or execute financial transactions without going through deterministic application validation and admin approval.
