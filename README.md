# 🛒 AK-MART — Enterprise Multi-Branch E-Commerce & Retail OS

<p align="center">
  <img src="public/assets/img/branding/ak-mart-logo.svg" alt="AK-Mart Logo" width="180" />
</p>

<p align="center">
  <strong>Production-Grade Multi-Branch E-Commerce + Retail POS + Real OTP Auth + Smart AI Importer + B2B Wholesale + Inventory 2.0 + WhatsApp & Email Communication Platform</strong>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" /></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" /></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3" /></a>
  <a href="https://www.mysql.com"><img src="https://img.shields.io/badge/MySQL-Supported-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Supported" /></a>
  <a href="https://www.postgresql.org"><img src="https://img.shields.io/badge/PostgreSQL-Ready-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL Ready" /></a>
  <img src="https://img.shields.io/badge/OTP%20Security-Bcrypt%20Hashed%20%7C%20Zero--Plaintext-success?style=for-the-badge&logo=auth0" alt="OTP Security" />
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License MIT" /></a>
</p>

---

## 🌟 Overview

**AK-Mart** is an enterprise-grade Omni-Channel Retail and E-Commerce operating system built with **Laravel 12**. Designed for multi-branch retail networks, department stores, wholesale suppliers, and digital brands, AK-Mart unites real-time POS checkouts, multi-warehouse stock logistics, smart AI catalog scrapers, B2B wholesale workflows, modular SaaS tenancy, cryptographic OTP authentication, and unified marketing communications into one seamless platform.

---

## 🚀 Key Modules & Capabilities

### 1. 🔐 Cryptographic OTP Authentication Layer
- **Zero Plaintext Storage**: All OTPs are generated cryptographically (`random_int`) and stored strictly as Bcrypt hashes via `Hash::make()`.
- **Purpose Isolation**: Strict segregation between `LOGIN`, `PASSWORD_RESET`, `EMAIL_VERIFICATION`, and `PHONE_VERIFICATION`. A Login OTP is cryptographically rejected for password resets.
- **Single-Use & Replay Protection**: Automatic invalidation upon verification or attempt exhaustion. Replay attacks are rejected.
- **Rate Limiting & Resend Cooldown**: 60-second cooldown timer, 3 maximum resends per flow, and 5 failed-attempt brute-force lockouts.
- **Segmented UI**: Interactive 6-digit input boxes with auto-focus, backspace jumping, clipboard paste parsing, dynamic countdown timer, and AJAX resend.
- **3-Step Password Recovery**: Email request → OTP verification → Short-lived authorization token (10 min) → Secure password reset.

### 2. 🔍 Universal Searchable Select System
- **Centralized Select2 Component**: Reusable `<x-searchable-select>` Blade component with server-side pagination, keyboard navigation, and debouncing (300ms).
- **Server-Side AJAX Endpoints (`/api/select/*`)**: High-performance querying for:
  - **Products**: Search by Name, SKU, Barcode with live price and stock badges.
  - **Customers**: Search by Name, Email, Phone.
  - **Branches**: Scoped dynamically to user's authorized branch permissions.
  - **Suppliers**: Search by Name, Company Name, Contact.
  - **Categories, Roles, and Staff Users**.
- **Zero-Trust Backend Security**: Client-submitted IDs are re-validated on the server against tenant scope and active permissions.

### 3. 🤖 Smart Product Engine 2.0 & AI Importer
- **Universal Multi-Platform Scraping**: Automated data, image, variant, and specification extraction from **Amazon (ASIN)**, **Flipkart**, **Meesho**, **Shopify**, and **Schema.org JSON-LD** pages.
- **Deterministic Ground-Truth Parsing**: Strict fallback mechanisms prevent hallucination of pricing, quantities, or product details.
- **Listing Quality Score (0–100%)**: Evaluates title completeness, description richness, SKU/barcode validity, high-resolution media, and SEO readiness.
- **Staging & Approval Queue**: Ingest drafts, review extracted specifications, refine pricing, and publish live with one click.
- **AI Content Generator & Copilot**: Automated generation of SEO titles, meta descriptions, selling points, and category suggestions.

### 4. 🏢 Multi-Branch & Multi-Warehouse Inventory 2.0
- **Multi-Location Fulfillment**: Independent branch and regional warehouse stock tracking.
- **Atomic Concurrency Locking**: Database row-locking and reservations prevent overselling during high-volume flash sales.
- **ABC Inventory Intelligence**: Automated catalog classification into Fast-Moving (Class A), Moderate (Class B), and Dead Stock (Class C).
- **Cycle Counting & Stock Transfers**: Physical audit reconciliation, variance adjustment, and inter-branch transfer tracking.

### 5. 💳 POS Terminal 2.0 (Point of Sale)
- **High-Speed Barcode Scanning**: Rapid item lookup, hotkey accelerators, and variant selectors.
- **Register Shift Sessions**: Track opening floats, cash sales, paid-in/paid-out adjustments, closing counts, and discrepancy reports.
- **Multi-Tender Payments**: Cash, Card, UPI, Digital Gift Cards, and Split Payments.
- **Direct POS Route**: Accessible at `/pos` for dedicated checkout counter devices.

### 6. 💼 B2B Wholesale & Enterprise Accounts
- **Corporate Company Management**: Assigned account managers, credit limits, and net terms (Net 15 / Net 30 / Net 60).
- **Volume Tier Pricing**: Quantity-based pricing brackets (e.g., 50+ units @ ₹750 vs retail ₹1,000).
- **RFQ Quote Lifecycle**: Request submission, merchant pricing negotiations, customer approval, and auto-conversion to Purchase Orders.

### 7. 📱 Unified Communication Center (`/communication`)
- **Email Gateway**: Transactional mailer with template variable parsing (`{{customer_name}}`, `{{order_number}}`, `{{tracking_number}}`, `{{otp}}`).
- **WhatsApp Cloud API**: Automated order confirmations, delivery tracking, and OTP verification via Meta's Cloud API.
- **Targeted Broadcasts**: Filter broadcasts by customer segments (VIP, Inactive, Abandoned Carts).
- **Zero-Checkout-Rollback**: Communication delivery failures are isolated and never disrupt customer orders.

### 8. ⚙️ Centralized Settings Hub (`/settings`)
- Modular settings for **General Store Details**, **Payments & Gateways**, **Checkout Policies**, **Shipping & Logistics**, **Tax & GST Rates**, **Branding & Assets**, **AI API Keys**, **WhatsApp Cloud Config**, **Audit Logs**, and **Security Policies**.

### 9. 🌐 Global Multi-Language Localization
- Complete synchronization across 6 primary languages:
  - 🇺🇸 **English** (`en`)
  - 🇸🇦 **Arabic** (`ar` — Full RTL Support)
  - 🇮🇳 **Hindi** (`hi`)
  - 🌴 **Malayalam** (`ml`)
  - 🇩🇪 **German** (`de`)
  - 🇫🇷 **French** (`fr`)

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel 12.x (PHP 8.2+) |
| **Authentication** | Jetstream 5.x, Sanctum, Custom Cryptographic OTP Service |
| **RBAC & Permissions** | Spatie Laravel Permission 6.x |
| **Modular Architecture** | nwidart/laravel-modules (14 Active Modules) |
| **Reactive Frontend** | Livewire 3.x, Alpine.js, Blade Components |
| **UI Design System** | Sneat Bootstrap 5 Enterprise Theme |
| **Dropdown & DataTables** | Select2 4.0.13, DataTables.net BS5 |
| **Database Engines** | MySQL 8.x / PostgreSQL / SQLite |
| **Asset Bundler** | Vite 6.x |

---

## ⚡ Quick Start & Local Setup

### 1. Clone Repository
```bash
git clone git@github.com:akhilmeleppura/Ak-Mart.git
cd Ak-Mart
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup & Migrations
Configure your database credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ak_mart
DB_USERNAME=root
DB_PASSWORD=
```

Run all migrations including OTP verifications and advanced commerce tables:
```bash
php artisan migrate --seed
```

### 5. Start Development Servers
```bash
# Terminal 1: Build Assets
npm run dev

# Terminal 2: Start Laravel Server
php artisan serve
```

Access the application in your browser: **`http://127.0.0.1:8000`**

---

## 🔑 Default Demo Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Supreme Admin** | `supreme@ak-mart.com` | `supreme123` | Full Universal System Access |
| **Store Admin** | `admin@ak-mart.com` | `password` | Store Operations, Catalog & POS |
| **Manager** | `manager@ak-mart.com` | `password` | Branch Inventory & Sales |
| **Cashier** | `cashier@ak-mart.com` | `password` | POS Terminal Only |

---

## 🧪 Security & Quality Verification

AK-Mart features automated test suites verifying OTP generation, Bcrypt hash validation, cooldown enforcement, and server-side select endpoints:

```bash
# Run OTP & Select Unit Verification
php scratch/test_otp.php
php scratch/test_select_search.php

# Run Feature Test Suite
php artisan test
```

---

## 👨‍💻 Author & Maintainer

**Akhil Meleppura**
- **GitHub**: [@akhilmeleppura](https://github.com/akhilmeleppura)
- **Email**: [akhilmeleppura@gmail.com](mailto:akhilmeleppura@gmail.com)
- **Project Repository**: [https://github.com/akhilmeleppura/Ak-Mart](https://github.com/akhilmeleppura/Ak-Mart)

---

## 📝 License

This project is licensed under the [MIT License](LICENSE).
