# AK-Mart 2026 Database Architecture & Schema Reference

## Database Connection
- **Engine**: MySQL 8.x
- **Host**: 127.0.0.1
- **Port**: 3307
- **Database**: `demo`

---

## 1. Multi-Warehouse & Inventory Subsystem

### `warehouses`
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | PK, Auto Increment | Warehouse ID |
| `code` | `varchar(50)` | Unique | Unique identifier code (e.g. WH-LDN-01) |
| `name` | `varchar(255)` | Not Null | Warehouse facility title |
| `address` | `varchar(255)` | Nullable | Street address |
| `city` | `varchar(100)` | Nullable | City |
| `contact_person`| `varchar(255)` | Nullable | Facility manager name |
| `phone` | `varchar(50)` | Nullable | Contact phone number |
| `is_active` | `tinyint(1)` | Default 1 | Enabled status |

### `warehouse_stocks`
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | PK, Auto Increment | Primary key |
| `warehouse_id` | `bigint unsigned` | FK -> `warehouses.id` | Warehouse link |
| `product_id` | `bigint unsigned` | FK -> `products.id` | Product link |
| `product_variant_id`| `bigint unsigned`| Nullable | Variant link |
| `qty` | `int` | Default 0 | Physical stock on hand |
| `committed_qty`| `int` | Default 0 | Stock allocated to orders |
| `reserved_qty` | `int` | Default 0 | Stock held for active checkouts |
| `min_stock` | `int` | Default 5 | Low stock threshold |
| `max_stock` | `int` | Default 100 | Optimal stock target |
| `bin_location` | `varchar(100)` | Nullable | Aisle/Shelf/Bin code |

### `stock_reservations`
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | PK, Auto Increment | Primary key |
| `product_id` | `bigint unsigned` | FK -> `products.id` | Product link |
| `warehouse_id` | `unsignedBigInteger`| Nullable | Warehouse link |
| `order_id` | `unsignedBigInteger`| Nullable | Associated order |
| `session_id` | `varchar(255)` | Nullable | Cart session ID |
| `qty` | `int` | Default 1 | Quantity held |
| `status` | `varchar(50)` | Default 'active' | `active`, `released`, `fulfilled` |
| `expires_at` | `timestamp` | Nullable | Expiration timestamp |

### `product_batches`
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | PK, Auto Increment | Primary key |
| `product_id` | `bigint unsigned` | FK -> `products.id` | Product link |
| `batch_number` | `varchar(100)` | Not Null | Lot/Batch code |
| `mfg_date` | `date` | Nullable | Manufacturing date |
| `expiry_date` | `date` | Nullable | Expiration date |
| `cost_price` | `decimal(12,2)` | Default 0 | Procurement cost |
| `qty` | `int` | Default 0 | Units in batch |
| `is_active` | `tinyint(1)` | Default 1 | Status |

### `stock_counts` & `stock_count_items`
- `stock_counts`: `id, count_number, warehouse_id, branch_id, type (cycle, full, partial), status (draft, in_progress, completed, reconciled), notes, user_id, completed_at`.
- `stock_count_items`: `id, stock_count_id, product_id, expected_qty, counted_qty, difference, remarks`.

---

## 2. B2B & Wholesale Subsystem

### `b2b_companies`
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | PK, Auto Increment | Primary key |
| `name` | `varchar(255)` | Not Null | Registered company name |
| `company_code` | `varchar(50)` | Unique | Identifier (e.g. B2B-APEX-01) |
| `tax_id` | `varchar(100)` | Nullable | Tax ID / GSTIN / VAT |
| `contact_email`| `varchar(255)` | Not Null | Purchasing email |
| `contact_phone`| `varchar(50)` | Nullable | Contact phone |
| `billing_address`| `text` | Nullable | Official billing address |
| `credit_limit` | `decimal(12,2)` | Default 0 | Maximum approved credit |
| `current_balance`| `decimal(12,2)`| Default 0 | Invoiced balance due |
| `payment_terms`| `varchar(50)` | Default 'net_30' | `prepaid`, `net_15`, `net_30`, `net_60` |
| `status` | `varchar(50)` | Default 'active' | `pending`, `active`, `suspended` |

### `b2b_buyers`
- `id, b2b_company_id, user_id, role (admin, buyer, approver), spending_limit, can_approve_orders`.

### `b2b_tier_prices`
- `id, product_id, b2b_company_id, min_qty, unit_price`.

### `b2b_quotes` & `b2b_quote_items`
- `b2b_quotes`: `id, quote_number, b2b_company_id, user_id, subtotal, discount, total, status (draft, submitted, approved, rejected, converted), valid_until, notes`.
- `b2b_quote_items`: `id, b2b_quote_id, product_id, qty, requested_price, approved_price, subtotal`.

---

## 3. Fulfillment Subsystem

### `fulfillment_orders` & `fulfillment_order_items`
- `fulfillment_orders`: `id, fulfillment_number, order_id, warehouse_id, branch_id, status (unfulfilled, picking, packed, shipped, delivered, cancelled), shipping_carrier, tracking_number, tracking_url, shipped_at, delivered_at, notes`.
- `fulfillment_order_items`: `id, fulfillment_order_id, order_item_id, qty`.

### `delivery_slots`
- `id, name, start_time, end_time, max_orders, days_available (json), is_active`.

---

## 4. Customer Experience Subsystem

### `wishlists` & `saved_carts`
- `wishlists`: `id, user_id, product_id, created_at, updated_at`.
- `saved_carts`: `id, user_id, name, cart_data (json), created_at, updated_at`.

### `gift_cards`
- `id, code (unique), initial_balance, current_balance, currency, recipient_email, pin, expiry_date, is_active, created_by`.

### `store_credits` & `store_credit_transactions`
- `store_credits`: `id, user_id (unique), balance, currency`.
- `store_credit_transactions`: `id, store_credit_id, amount, type (credit, debit), reference_type, reference_id, notes`.

---

## 5. Financial Shift Reconciliation

### `pos_register_sessions`
- `id, branch_id, user_id, opening_amount, closing_amount, expected_cash, cash_sales, card_sales, upi_sales, difference, status (open, closed), notes, opened_at, closed_at`.

---

## 6. Marketing & Omnichannel

### `abandoned_carts`
- `id, user_id, email, phone, cart_data (json), total_amount, recovery_token (unique), recovery_emails_sent, recovered_at`.

---

## 7. Developer Webhooks Hub

### `webhook_subscriptions` & `webhook_logs`
- `webhook_subscriptions`: `id, name, target_url, secret, events (json), is_active`.
- `webhook_logs`: `id, webhook_subscription_id, event, payload (json), response_status, response_body, attempts, status (delivered, failed)`.

### `backups`
- `id, file_name, file_size, type (database, files, full), status (completed, failed), checksum, user_id`.
