# AK-Mart — Complete Modular Architecture Design Document

## Executive Architecture Summary
AK-Mart is organized into a scalable, domain-driven **Modular Monolith** powered by `nwidart/laravel-modules`.
Each module encapsulates its respective domain logic, controllers, routes, views, models, and services while maintaining global Laravel kernel stability.

```
AK-MART (MODULAR MONOLITH)
│
├── App Core (Global / Non-Modular)
│   ├── Authentication (Login, Register, Password Reset)
│   ├── Core Identity (User model, Sanctum tokens, Spatie RBAC)
│   ├── Layouts & Shell (LayoutMaster, Header, Sidebar, Navigation)
│   └── Providers & Bootstrap
│
├── Modules (Domain Feature Enclaves)
│   ├── Modules/Ecommerce       (Products, Orders, Customers, Coupons, Reviews, Branches, Cart, Checkout)
│   ├── Modules/Settings        (Centralized 28-Section Store Settings Hub, SMTP, WhatsApp, Payments)
│   ├── Modules/SaaS            (Subscriptions, Billing Invoices, KYC Admin Review, Analytics, Commissions)
│   ├── Modules/Vendor          (Vendor Portal, POS Terminal, Store Builder, KYC, Support, Wallet)
│   ├── Modules/Inventory       (Warehouses, Stock Counts, Inventory Adjustments, Stock Transfers)
│   ├── Modules/Logistics       (Shipping Methods, Fleet, Tracking, Delivery Zones)
│   ├── Modules/Automation      (Workflow Automation Rules, Event Triggers, Notifications)
│   ├── Modules/AI              (AI Copilot, Product Content Optimizer, Attribute Extraction)
│   ├── Modules/Communication   (Email & WhatsApp Center, Templates, Campaigns)
│   ├── Modules/Accounting      (General Ledger, Customer Ledger, Journal Entries, Trial Balance)
│   ├── Modules/Permission      (Role Management, Permission Matrices)
│   └── Modules/Reports         (Sales, Profit & Loss, Tax & Stock Reporting)
```

## Module Directory Structure Standard
Each module follows a consistent internal topology:
```
Modules/{ModuleName}/
├── app/
│   ├── Http/Controllers/
│   ├── Http/Requests/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── Providers/
│   └── {ModuleName}ServiceProvider.php
├── resources/
│   └── views/
├── routes/
│   ├── web.php
│   └── api.php
└── module.json
```