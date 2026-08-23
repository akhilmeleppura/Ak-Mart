# 🗄️ AKMART — DATABASE SCHEMA, RELATIONSHIPS & INVARIANTS

**Document ID**: AKMART-DOC-DB-005  
**Database Engines**: MySQL 8.0+ / MariaDB / SQLite  
**Date**: August 2026  

---

## 1. CORE DOMAIN SCHEMAS & RELATIONSHIPS

```text
  ┌─────────────────┐       ┌──────────────────────┐       ┌──────────────────────┐
  │   categories    │       │       products       │       │   product_variants   │
  ├─────────────────┤       ├──────────────────────┤       ├──────────────────────┤
  │ id              │1     *│ id                   │1     *│ id                   │
  │ name            ├───────┤ category_id          ├───────┤ product_id           │
  │ slug            │       │ name, slug, sku      │       │ sku, barcode, price  │
  │ parent_id       │       │ price, cost_price    │       │ qty, attributes_json │
  │ is_active       │       │ qty, min_stock       │       │ is_active            │
  └─────────────────┘       └──────────┬───────────┘       └──────────────────────┘
                                       │
                 ┌─────────────────────┼─────────────────────┐
                 │1                    │1                    │1
                 ▼*                    ▼*                    ▼*
      ┌────────────────────┐ ┌────────────────────┐ ┌────────────────────┐
      │  stock_movements   │ │    order_items     │ │      reviews       │
      ├────────────────────┤ ├────────────────────┤ ├────────────────────┤
      │ id                 │ │ id                 │ │ id                 │
      │ product_id         │ │ order_id           │ │ product_id         │
      │ branch_id          │ │ product_id         │ │ user_id            │
      │ quantity_change    │ │ quantity, unit_price││ rating (1-5)        │
      │ reason, reference  │ │ subtotal, tax_rate │ │ comment, verified  │
      └────────────────────┘ └────────────────────┘ └────────────────────┘
                                       ▲
                                       │*
                                       │1
                            ┌─────────────────────┐
                            │       orders        │
                            ├─────────────────────┤
                            │ id, order_number    │
                            │ user_id, branch_id  │
                            │ status, total_amount│
                            │ payment_method/stat │
                            │ shipping_address    │
                            │ created_at          │
                            └─────────────────────┘
```

---

## 2. INVARIANT RULES & DATA INTEGRITY

1. **Atomic Stock Ledger Invariant**: Direct manipulation of `products.qty` without an accompanying `stock_movements` record is prohibited. Every inventory change (Sale, Return, Purchase Receiving, Manual Adjustment, Branch Transfer) creates a permanent movement audit trail.
2. **Double-Entry Store Credit Ledger**: `store_credits` and `store_credit_transactions` track balance before, transaction amount, balance after, reference type, and source. Negative balances are prevented at database schema level (`unsigned` or check constraint).
3. **Multi-Branch Isolation**: Branch-scoped entities utilize the `BelongsToBranch` trait. When a branch manager or cashier is logged in, all catalog, POS, and inventory queries are scoped to their authorized `branch_id`. Supreme Admin retains unrestricted global access.
4. **Order Status Transition State Machine**: Orders strictly transition through valid sequential states:
   `Pending` $\rightarrow$ `Confirmed` $\rightarrow$ `Payment Verified` $\rightarrow$ `Processing` $\rightarrow$ `Packed` $\rightarrow$ `Shipped` $\rightarrow$ `Out for Delivery` $\rightarrow$ `Delivered` (or `Cancelled` / `Returned` / `Refunded`).
