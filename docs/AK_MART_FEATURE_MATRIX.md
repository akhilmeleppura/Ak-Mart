# AK-Mart Master Feature Matrix

| Module | Feature | Status | Route | Database Table | Permission | Language Support | Test Status | Notes |
| :--- | :--- | :---: | :--- | :--- | :--- | :---: | :---: | :--- |
| **Dashboard** | Real Financial KPIs & Trends | Complete | `/` | `orders`, `order_items` | `view_dashboard` | 6 Languages | Passed | Aggregates actual DB transactions with date filters |
| **Dashboard** | Clickable Action Cards | Complete | `/` | `orders`, `products` | `view_dashboard` | 6 Languages | Passed | Links directly to pre-filtered lists |
| **Dashboard** | Action Required Insight Center | Complete | `/` | `products`, `orders`, `store_settings` | `view_dashboard` | 6 Languages | Passed | Live low-stock alerts, pending payment cues |
| **POS Terminal** | Cashier Register Session & Barcode | Complete | `/pos/register` | `pos_register_sessions`, `products` | `access_pos` | 6 Languages | Passed | Cash in/out, register open/close, instant receipt |
| **Products** | Product CRUD & Variants | Complete | `/app/ecommerce/product/list` | `products`, `product_variants` | `manage_products` | 6 Languages | Passed | Supports SKUs, barcodes, categories, tags |
| **Products** | Smart URL Importer | Complete | `/app/ecommerce/product/import-url` | `imported_products`, `products` | `manage_products` | 6 Languages | Passed | Multi-strategy JSON-LD, OpenGraph with preview & approval |
| **Inventory** | Stock Tracking & Safety Stock | Complete | `/app/ecommerce/inventory` | `stock_movements`, `products` | `manage_inventory` | 6 Languages | Passed | Audited stock adjustments, restock on cancel |
| **Inventory** | Warehouses & Stock Count | Complete | `/warehouses`, `/stock-count` | `warehouses`, `stock_counts` | `manage_inventory` | 6 Languages | Passed | Multi-warehouse isolation and variance tracking |
| **Branches** | Multi-Branch Management | Complete | `/branches` | `branches`, `companies` | `manage_branches` | 6 Languages | Passed | Header branch switcher scopes tenant queries |
| **Orders** | Full Order Lifecycle | Complete | `/app/ecommerce/order/list` | `orders`, `order_items` | `manage_orders` | 6 Languages | Passed | Pending → Processing → Shipped → Delivered |
| **Orders** | Invoice Generation & Print | Complete | `/invoices/{id}/preview` | `orders`, `subscription_invoices` | `manage_orders` | 6 Languages | Passed | Printable PDF layout with QR verification |
| **Returns** | Return Requests & Restocking | Complete | `/vendor/return-requests` | `return_requests`, `stock_movements` | `manage_returns` | 6 Languages | Passed | Automatic restocking into inventory upon approval |
| **Payments** | Payment Gateways (Stripe/PayPal/PhonePe) | Complete | `/settings/payments` | `store_settings` (encrypted) | `manage_settings` | 6 Languages | Passed | AES encrypted keys, test sandboxes, COD limits |
| **Finance** | Expenses & Cash Flow | Complete | `/expenses` | `expenses`, `expense_categories` | `manage_finance` | 6 Languages | Passed | Categorized expense logging with receipt attachments |
| **Shipping** | Shipping Zones & Methods | Complete | `/settings/shipping` | `shipping_methods`, `shipments` | `manage_shipping` | 6 Languages | Passed | Flat rate, express rate, and free shipping thresholds |
| **Customers** | Customer Profiles & Segmentation | Complete | `/app/ecommerce/customer/all` | `users`, `orders` | `manage_customers` | 6 Languages | Passed | Dynamic VIP, Returning, New, and Inactive segments |
| **Coupons** | Promo Codes & Restrictions | Complete | `/app/ecommerce/coupon/list` | `coupons` | `manage_coupons` | 6 Languages | Passed | Min amount, usage caps, category rules |
| **Reviews** | Ratings & Moderation | Complete | `/app/ecommerce/manage-reviews` | `reviews`, `products` | `manage_reviews` | 6 Languages | Passed | Customer review approval, reject, and admin reply |
| **Loyalty** | Loyalty Points & Rewards | Complete | `/settings/customers` | `loyalty_transactions` | `manage_customers` | 6 Languages | Passed | Configurable points per dollar & redemption rates |
| **B2B** | Wholesale Quotes & Companies | Complete | `/b2b/companies` | `b2b_companies`, `b2b_quotes` | `manage_b2b` | 6 Languages | Passed | Business buyer accounts with tiered price schedules |
| **Suppliers** | Purchase Orders & Receiving | Complete | `/purchases/orders` | `purchase_orders`, `suppliers` | `manage_purchasing` | 6 Languages | Passed | Stock receiving directly increments inventory count |
| **Analytics** | Sales & Inventory Reports | Complete | `/reports/sales` | `orders`, `products` | `view_reports` | 6 Languages | Passed | Transactional data aggregation with CSV exports |
| **AI Copilot** | Google Gemini E-Commerce AI | Complete | `/settings/ai`, `/api/ai/copilot-chat` | `ai_settings`, `store_settings` | `use_ai` | 6 Languages | Passed | Neural catalog tools, localized contextual insights |
| **Global Search** | Instant CTRL+K Search | Complete | `/api/global-search` | `products`, `orders`, `store_settings` | Authenticated | 6 Languages | Passed | Instant index of products, orders, settings, invoices |
| **Email Hub** | SMTP Server & Live Test | Complete | `/settings/email` | `store_settings` (encrypted) | `manage_settings` | 6 Languages | Passed | Real-time SMTP handshake tester with SweetAlert2 |
| **Email Templates** | Transactional Template Editor | Complete | `/settings/email-templates` | `communication_templates` | `manage_settings` | 6 Languages | Passed | Dynamic placeholders (`{{customer_name}}`, `{{order_number}}`) |
| **Email Reminders** | Automated Scheduler Engine | Complete | `/settings/email-reminders` | `store_settings` | `manage_settings` | 6 Languages | Passed | Unpaid orders & cart recovery with stop conditions |
| **WhatsApp Hub** | Meta Cloud API & Auto Alerts | Complete | `/settings/whatsapp` | `store_settings` (encrypted) | `manage_settings` | 6 Languages | Passed | Automated triggers for order created, shipped, delivered |
| **Notifications** | Omnichannel Matrix | Complete | `/settings/notifications` | `store_settings` | `manage_settings` | 6 Languages | Passed | Channel-level matrix (In-App, Email, WhatsApp) |
| **Automation** | Workflow Rules Engine | Complete | `/settings/automation` | `workflow_rules` | `manage_settings` | 6 Languages | Passed | WHEN/IF/THEN rule execution with audit tracking |
| **Settings Hub** | Centralized 30-Section Hub | Complete | `/settings/*` | `store_settings` | `manage_settings` | 6 Languages | Passed | Categorized sidebar with instant keyword search |
| **Security Center** | Brute Force & Session Security | Complete | `/settings/security` | `store_settings`, `audit_logs` | `manage_security` | 6 Languages | Passed | Lockout duration, password rules, session timeouts |
| **API & Webhooks** | REST API & Outbound Webhooks | Complete | `/settings/api-webhooks` | `webhook_subscriptions` | `manage_api` | 6 Languages | Passed | API keys, signature secrets, event logging |
| **Profile** | User Account & Security | Complete | `/profile`, `/account/settings` | `users`, `audit_logs` | Authenticated | 6 Languages | Passed | Avatar AJAX upload/remove, password update |
| **Billing** | Multi-Tenant SaaS Subscriptions | Complete | `/billing`, `/saas/billing` | `tenant_subscriptions`, `invoices` | Authenticated | 6 Languages | Passed | Plan usage limits, upgrade checkout, PDF invoices |
| **Localization** | Multi-Language & RTL Engine | Complete | Global | JSON language dictionaries | All | 6 Languages | Passed | Dynamic locale switching across EN, ML, HI, AR, FR, DE |
| **Seeders** | Master Database Seeder | Complete | `php artisan db:seed` | All tables | Artisan | 6 Languages | Passed | Unified `DatabaseSeeder` orchestrating all models |
| **Audit Logs** | Immutable Activity Trail | Complete | `/settings/audit-logs` | `audit_logs` | `view_audit` | 6 Languages | Passed | Logs profile changes, settings updates, and returns |
