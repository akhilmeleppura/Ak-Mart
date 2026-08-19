# AK-MART Global Route Language Audit Report

## 1. Audit Scope & Methodology
Every route, controller, view, modal, and dynamic script in the AK-Mart repository was systematically audited to verify:
1. **Blade Localization**: All static text, page headings, subtitles, card headers, table headers, table cells, form labels, input placeholders, badges, status indicators, and empty state messages use `__()` or `@lang()`.
2. **JavaScript Localization**: Dynamic alerts (`Swal.fire`, `AKNotify`), prompt messages, button feedback states use `@json(__('...'))`.
3. **Dictionary Parity**: 1,550+ translation keys synchronized across all 6 core supported languages: English (`en`), Malayalam (`ml`), Hindi (`hi`), Arabic (`ar` with RTL), French (`fr`), and German (`de`).

---

## 2. Audited Route Matrix

### A. Vendor Operations & Merchant Dashboard
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-vendor-payment-settings` | `/vendor/payment-settings` | `vendor/payment-settings.blade.php` | Stripe, PayPal, PhonePe keys, placeholders, save buttons | **PASS** |
| `app-vendor-wallet` | `/vendor/wallet` | `vendor/wallet.blade.php` | Available balance, payouts table, modal forms, validation | **PASS** |
| `app-vendor-kyc` | `/vendor/kyc` | `vendor/kyc.blade.php` | Document selector, GSTIN, PAN, upload status, approval cards | **PASS** |
| `app-vendor-inventory` | `/vendor/inventory` | `vendor/inventory.blade.php` | Stock allocations, quick adjustments, branch transfer modal | **PASS** |
| `app-vendor-returns` | `/vendor/returns` | `vendor/returns.blade.php` | Return reasons, resolution action, refund status badges | **PASS** |
| `app-vendor-store-builder` | `/vendor/store-builder` | `vendor/store-builder.blade.php` | Theme selector, banner controls, live preview canvas | **PASS** |
| `app-vendor-pos` | `/vendor/pos` | `vendor/pos.blade.php` | Category pills, POS cart, discount calc, tax calc, print receipt | **PASS** |
| `app-vendor-support-tickets` | `/vendor/support-tickets` | `vendor/support-tickets.blade.php` | Ticket list, priority badges, status filters | **PASS** |
| `app-vendor-support-ticket-show` | `/vendor/support-tickets/{id}` | `vendor/support-ticket-show.blade.php` | Message thread, reply form, attachment uploader | **PASS** |

### B. Inventory, Multi-Warehouse & Quality Assurance
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-warehouses` | `/inventory/warehouses` | `inventory/warehouses.blade.php` | Warehouse KPI cards, grid list, add warehouse modal | **PASS** |
| `app-warehouses-show` | `/inventory/warehouses/{id}` | `inventory/warehouse-details.blade.php` | Bin locations, stock allocation table, adjust stock modal | **PASS** |
| `app-stock-counts` | `/inventory/stock-counts` | `inventory/stock-counts.blade.php` | Audit sessions, status badges, start count modal | **PASS** |
| `app-stock-counts-show` | `/inventory/stock-counts/{id}` | `inventory/stock-count-detail.blade.php` | Audit sheet, variance badges, reconcile confirm | **PASS** |
| `app-inventory-abc` | `/inventory/abc-analysis` | `inventory/abc-analysis.blade.php` | Pareto velocity matrix, dead stock table, recommendations | **PASS** |
| `app-catalog-scanner` | `/catalog/scanner` | `catalog/scanner.blade.php` | Store health composite score, 1-click autofix tools, issue ledger | **PASS** |
| `app-catalog-duplicates` | `/catalog/duplicates` | `catalog/duplicates.blade.php` | Similarity score badges, match reasons, duplicate resolution | **PASS** |
| `app-product-importer` | `/catalog/importer` | `catalog/importer.blade.php` | URL extractor, CSV file upload, staging review queue | **PASS** |
| `app-product-import-review` | `/catalog/importer/review/{id}` | `catalog/review.blade.php` | Extracted product editor, variant parser, publish CTA | **PASS** |

### C. Sales, Fulfillment & Omnichannel Operations
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-fulfillment` | `/app/fulfillment` | `fulfillment/index.blade.php` | Fulfillment queue, origin warehouse, tracking updater modal | **PASS** |
| `app-fulfillment-pickpack` | `/app/fulfillment/{id}/pick-pack` | `fulfillment/pick-pack-list.blade.php` | Printable pick list, packing slip, barcode check boxes | **PASS** |
| `app-abandoned-carts` | `/marketing/abandoned-carts` | `marketing/abandoned-carts.blade.php` | Recovery pipeline, cart value KPI, send recovery email | **PASS** |
| `app-feeds-index` | `/marketing/feeds` | `marketing/product-feeds.blade.php` | Google XML RSS 2.0, Meta CSV, TikTok JSON feeds | **PASS** |
| `app-communication-center` | `/marketing/communication-center` | `marketing/communication-center.blade.php` | Live logs, Quick dispatch, WhatsApp/Email templates, Campaigns | **PASS** |

### D. Corporate Accounts, B2B & Wholesale
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-b2b-companies` | `/b2b/companies` | `b2b/companies.blade.php` | Corporate accounts table, credit lines, register company modal | **PASS** |
| `app-b2b-companies-show` | `/b2b/companies/{id}` | `b2b/company-details.blade.php` | Tier pricing tab, authorized corporate buyers, MOQ editor | **PASS** |
| `app-b2b-quotes` | `/b2b/quotes` | `b2b/quotes.blade.php` | Quotes ledger, discount calculator, approve/reject CTAs | **PASS** |

### E. Finance, Cash Desk & Customer Services
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-pos-register` | `/finance/pos-register` | `finance/pos-register.blade.php` | Cash drawer sessions, opening float, closing reconciliation | **PASS** |
| `app-expenses` | `/expenses` | `expenses/index.blade.php` | Operational costs ledger, record expense modal, add category modal | **PASS** |
| `app-suppliers` | `/suppliers` | `suppliers/index.blade.php` | Supplier directory, balances, add supplier modal, delete prompt | **PASS** |
| `app-purchases` | `/purchases` | `purchases/index.blade.php` | PO ledger, receive & stock handler, multi-line PO modal | **PASS** |
| `app-gift-cards` | `/customer/gift-cards` | `customer/gift-cards.blade.php` | Issued gift cards, balance KPI, generate gift card modal | **PASS** |
| `app-customer-portal` | `/customer/portal` | `customer/portal.blade.php` | Order tracking, wishlist, store credit ledger, return requests | **PASS** |
| `app-reports` | `/reports` | `reports/index.blade.php` | P&L statements, Gross margin, 7/30-day forecasting models | **PASS** |

### F. Automation, Developer & System Administration
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-automation` | `/automation` | `automation/index.blade.php` | Event triggers, rule builder modal, condition operators | **PASS** |
| `app-developer-webhooks` | `/developer/webhooks` | `developer/webhooks.blade.php` | Endpoint manager, dispatch logs, test ping, subscribe modal | **PASS** |
| `app-backups` | `/system/backups` | `system/backups.blade.php` | SQL snapshot generator, checksum verifier, storage metrics | **PASS** |
| `app-system-health` | `/system/health` | `system/health.blade.php` | Latency probes, MySQL/Cache diagnostics, health score banner | **PASS** |
| `app-security-center` | `/system/security-center` | `system/security-center.blade.php` | 2FA audit, live security audit trail, access controls | **PASS** |

### G. SaaS Platform Management
| Route Name | URI Path | View File | Key Elements Localized | Status |
| :--- | :--- | :--- | :--- | :---: |
| `app-saas-analytics` | `/app/saas/analytics` | `saas/analytics.blade.php` | GMV, MRR, ARR, Churn, Store growth charts | **PASS** |
| `app-saas-billing` | `/app/saas/billing` | `saas/billing.blade.php` | Current subscription status, pricing plans, feature matrix | **PASS** |
| `app-saas-commission-rules`| `/app/saas/commission-rules` | `saas/commission-rules.blade.php` | Fee rules datatable, volume tiers modal, scope selector | **PASS** |
| `app-saas-kyc-admin` | `/app/saas/kyc` | `saas/kyc-admin.blade.php` | Review queue, approval workflow, rejection reason modal | **PASS** |
| `app-saas-currencies` | `/app/saas/currencies` | `saas/currencies.blade.php` | Multi-currency table, exchange rates, add currency modal | **PASS** |
| `app-saas-languages` | `/app/saas/languages` | `saas/languages.blade.php` | Multi-language table, RTL switch, add language modal | **PASS** |
| `app-saas-dunning` | `/app/saas/dunning` | `saas/dunning.blade.php` | Past due subscriptions, recovery logs, manual trigger | **PASS** |
| `app-saas-seo` | `/app/saas/seo` | `saas/seo-dashboard.blade.php` | Indexing metrics, dynamic XML sitemap card, score gauge | **PASS** |
| `app-saas-audit-logs` | `/app/saas/audit-logs` | `saas/audit-logs.blade.php` | Immutable activity trail, metadata viewer, IP logging | **PASS** |
