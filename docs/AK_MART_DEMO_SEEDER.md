# AK-Mart Comprehensive Realistic Demo Seeder

## 1. Overview

AK-Mart comes with an enterprise-grade database seeding suite designed to populate a fully functional, realistic retail and e-commerce environment.

### Command Execution:
```bash
php artisan migrate:fresh --seed
```

---

## 2. Seeded Entity Summary

| Entity | Quantity | Details |
| :--- | :--- | :--- |
| **Branches** | 4 | Flagship NY, London City, Dubai Mall, Kochi Hub |
| **Warehouses** | 3 | NYC Central, Thames Cold Storage, JAFZA Terminal |
| **Categories** | 12 | Groceries, Beverages, Dairy, Bakery, Produce, Electronics, etc. |
| **Products** | 60+ | Realistic grocery/retail items with SKUs, barcodes, variants, and pricing |
| **Stock Levels** | 60+ | Includes high-volume stock, low stock warnings (<=5 units), and out-of-stock (0 units) |
| **Customers** | 36 | Realistic profiles with phones, addresses, and customer tiers |
| **Suppliers** | 10 | Nestle, Unilever, P&G, Golden Grain, Organic Fresh, etc. |
| **Purchase Orders** | 10 | POs spanning Received, Partial, Draft, and Sent states |
| **Orders & Items** | 45+ | Real-world orders across 30 days, statuses (Delivered, Processing, Shipped, Refunded) |
| **Coupons** | 5 | `WELCOME10`, `SAVE20`, `FESTIVE15`, `FREESHIP`, `SUPERMART` |
| **Reviews** | 30+ | Authentic multi-star customer reviews |
| **Notifications** | 20+ | Stock alerts, purchase orders, sales milestones |

---

## 3. Seeder Execution Hierarchy

1. `RolesPermissionsSeeder` - Installs Spatie roles and granular permissions.
2. `BranchSeeder` - Creates multi-location store branches.
3. `WarehouseSeeder` - Configures fulfillment and cold storage facilities.
4. `SuperAdminSeeder` - Registers Supreme Admin & Staff credentials.
5. `CustomerSeeder` - Creates diverse retail and wholesale customers.
6. `SupplierAndPurchaseSeeder` - Sets up vendor relationships and purchase orders.
7. `EcommerceSeeder` - Ingests 12 categories, 60+ products, variants, 45+ orders, and reviews.
8. `SubscriptionPlanSeeder` - Initializes multi-tenant SaaS tiers.
9. `PaymentOptionSeeder` - Configures payment gateways (Stripe, UPI, COD, Card).
10. `NotificationSeeder` - Populates system alert logs.
11. `AdvancedCommerceSeeder` - Creates stock movements, expenses, and workflow rules.
12. `NextGenCommerceSeeder` - Generates B2B quotes, gift cards, store credits, and POS sessions.
