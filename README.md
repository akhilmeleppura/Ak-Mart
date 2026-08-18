# 🛒 AK-MART — Enterprise Multi-Branch E-Commerce & Retail OS

<p align="center">
  <img src="public/assets/img/branding/ak-mart-logo.svg" alt="AK-Mart Logo" width="180" />
</p>

<p align="center">
  <strong>Production-Grade Multi-Branch E-Commerce + Retail POS + Smart AI Importer + B2B Wholesale + Inventory 2.0 + WhatsApp & Email Communication Platform</strong>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" /></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" /></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3" /></a>
  <a href="https://www.postgresql.org"><img src="https://img.shields.io/badge/PostgreSQL-Ready-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL Ready" /></a>
  <a href="https://www.mysql.com"><img src="https://img.shields.io/badge/MySQL-Supported-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Supported" /></a>
  <img src="https://img.shields.io/badge/Tests-75%20Passed%20%7C%20321%20Assertions-brightgreen?style=for-the-badge&logo=checkmarx" alt="Tests" />
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License MIT" /></a>
</p>

---

## 🌟 Overview

**AK-Mart** is a comprehensive, enterprise-ready E-Commerce and Omni-Channel Retail operating system. Designed for real-world mini-marts, multi-branch department stores, wholesale suppliers, and modern online brands, it unites catalog management, POS registers, warehouse stock logistics, B2B wholesale quoting, AI-assisted imports, and unified marketing communications into a unified, high-performance interface.

---

## 🚀 Key Modules & Architecture

### 1. 🤖 Smart Product Engine 2.0 & Importer
- **Universal Multi-Platform Scraping**: Automated data, image, variant, and specification extraction from **Amazon (ASIN)**, **Flipkart**, **Meesho**, **Shopify**, and standard **Schema.org JSON-LD** pages.
- **Strict Non-Hallucinatory Fallback**: Deterministic parsing architecture that extracts ground truth without fabricating prices or stock.
- **Product Listing Quality Scoring (0–100%)**: Real-time diagnostic engine evaluating title length, description richness, pricing, SKU/barcodes, image resolution, and SEO readiness with actionable suggestions.
- **Staging & Approval Queue**: Ingest drafts, preview formatted data, edit attributes, and publish to the live store with one click.

### 2. 🏢 Multi-Branch & Multi-Warehouse Inventory 2.0
- **Multi-Location Fulfillment**: Independent branch and regional warehouse stock tracking.
- **Atomic Stock Reservation & DB Row-Locking**: Concurrency-safe inventory reservations preventing overselling during flash sales.
- **Inventory Intelligence & ABC Analysis**: Automated product classification into Fast-Moving (Class A), Moderate (Class B), and Dead Stock (Class C) with aging analysis.
- **Cycle Counting & Stock Transfers**: Physical audit reconciliation and inter-branch inventory transfer workflows.

### 3. 💳 POS Terminal 2.0 (Point of Sale)
- **Barcode & Quick SKU Search**: Rapid item scanning with keyboard shortcuts and variant selectors.
- **Cash Register Shift Sessions**: Track opening floats, cash sales, paid-in/paid-out adjustments, closing counts, and cash variance.
- **Multi-Tender Checkout**: Cash, Card, UPI, and split payments.
- **Offline Buffer**: Resilient transaction queue maintaining operations during network interruptions.

### 4. 💼 B2B & Wholesale Commerce
- **Corporate Company Accounts**: Credit limits, dedicated sales reps, and net payment terms (Net 15 / Net 30 / Net 60).
- **Volume Tier Pricing**: Quantity-based pricing brackets (e.g., 50+ units @ ₹750 vs retail ₹1,000).
- **RFQ Quote Workflow**: B2B quote request submission, admin price adjustments, customer approval, and auto-conversion to Purchase Orders.

### 5. 💰 Zero-Trust Pricing & Checkout Engine
- **Server-Side Verification**: 100% server recalculation preventing frontend tampering of prices, quantities, or totals.
- **Split Tender Checkout**: Simultaneous redemption of Coupons, Digital Gift Cards, Store Credit / Wallet Balances, and Gateway payments.
- **Automated Tax Splitting**: Comprehensive GST calculation with automatic CGST / SGST / IGST breakdown.

### 6. 📱 Unified Communication Center (`/communication`)
- **Email Gateway**: Transactional mailer with template variable parsing (`{{customer_name}}`, `{{order_number}}`, `{{tracking_number}}`, `{{discount_code}}`).
- **WhatsApp Business Cloud API**: Direct order tracking, delivery notifications, and return confirmations via Meta's official Cloud API.
- **Campaign Broadcast Manager**: Broadcast targeted marketing promotions to custom customer segments (All, VIP, Inactive, Abandoned Carts).
- **Customer Opt-Out Governance**: Automatic compliance with customer marketing communication preferences while preserving critical transactional receipts.
- **Zero Order Rollback Guarantee**: Communication delivery failures never crash or interrupt successful customer checkout transactions.

### 7. 🛡️ Enterprise Security & Multi-Tenancy
- **Supreme Admin Universal Gate**: Transparent superuser bypass across all system boundaries.
- **IDOR Protection**: Strict customer ownership validation on orders, invoices, returns, and wallet records.
- **Interactive SweetAlert2 Dialogs**: Global confirmation modals replacing raw browser popups on all delete and state-changing actions.
- **2FA & Session Security**: Multi-factor authentication, rate limiting, and remote browser session invalidation.

---

## 🛠️ Technology Stack

| Component | Technology / Library |
| :--- | :--- |
| **Backend Framework** | Laravel 12.x (PHP 8.2+) |
| **Frontend Reactive Layer** | Livewire 3.x, Alpine.js, Blade |
| **Admin UI Theme** | Sneat Bootstrap 5 Enterprise Theme |
| **Database Engines** | **PostgreSQL** (Production on Render) & **MySQL** / SQLite |
| **Permissions & RBAC** | Spatie Laravel Permission 6.x |
| **Authentication** | Laravel Jetstream 5.x & Sanctum API Tokens |
| **Asset Bundler** | Vite 5.x |
| **Dialogs & UI Alerts** | SweetAlert2 & Animate.css |

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

### 4. Database Setup & Migration
Configure your database credentials in `.env` (MySQL or PostgreSQL):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ak_mart
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed the comprehensive demo catalog:
```bash
php artisan migrate --seed
```

### 5. Build Frontend Assets & Run
```bash
npm run build
php artisan serve
```

Access the store in your browser: `http://127.0.0.1:8000`

---

## ☁️ Free Cloud Deployment on Render (with PostgreSQL)

AK-Mart is optimized for 1-click cloud deployment on [Render.com](https://render.com) using free PostgreSQL:

1. Create a free **PostgreSQL Database** on Render (`akmart-db`).
2. Create a free **Web Service** on Render connected to `akhilmeleppura/Ak-Mart`.
3. Set the build & start commands:
   - **Build Command**:
     ```bash
     composer install --no-dev --optimize-autoloader && php artisan config:clear && php artisan migrate --force && php artisan db:seed --force
     ```
   - **Start Command**:
     ```bash
     php artisan serve --host=0.0.0.0 --port=$PORT
     ```
4. Set Environment Variables:
   - `DB_CONNECTION` = `pgsql`
   - `DATABASE_URL` = `<your-render-internal-database-url>`
   - `APP_ENV` = `production`
   - `APP_KEY` = `<your-256-bit-app-key>`
   - `SESSION_DRIVER` = `cookie`
   - `CACHE_STORE` = `file`

---

## 🔑 Default Demo Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Supreme Admin** | `supreme_admin@akmart.com` | `admin123` *(or `password`)* | Full System Access |
| **Store Admin** | `admin@akmart.com` | `admin123` *(or `password`)* | Store Operations & POS |
| **Branch Manager** | `manager@branch.com` | `manager123` | Branch Inventory & Sales |
| **Cashier** | `cashier@akmart.com` | `password` | POS Terminal Only |
| **B2B Wholesale Buyer**| `buyer@apexwholesale.com` | `password` | Wholesale Catalog & RFQ |

---

## 🧪 Automated Test Suite

AK-Mart includes a feature test suite verifying pricing calculations, concurrency locking, smart extraction, communication fallbacks, and security gates:

```bash
php artisan test
```

```
PASS  Tests\Feature\AuthenticationTest
PASS  Tests\Feature\BranchAndPermissionTest
PASS  Tests\Feature\CommerceRegressionAuditTest
PASS  Tests\Feature\NextGenCommerceTest
PASS  Tests\Feature\AdvancedECommerceSuiteTest
PASS  Tests\Feature\UniversalProductImporterTest
PASS  Tests\Feature\ProfileInformationTest
PASS  Tests\Feature\PasswordResetTest
PASS  Tests\Feature\TwoFactorAuthenticationSettingsTest

Tests:       75 Passed, 321 Assertions, 0 Failures
Duration:    11.89s
```

---

## 👨‍💻 Author & Maintainer

**Akhil Meleppura**
- **GitHub**: [@akhilmeleppura](https://github.com/akhilmeleppura)
- **Repository**: [https://github.com/akhilmeleppura/Ak-Mart](https://github.com/akhilmeleppura/Ak-Mart)

---

## 📝 License

This project is open-sourced under the [MIT License](LICENSE).
