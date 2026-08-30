# 🛒 AK-MART — Enterprise Multi-Branch E-Commerce & Retail OS

<p align="center">
  <img src="public/assets/img/branding/ak-mart-logo.svg" alt="AK-Mart Logo" width="180" />
</p>

<p align="center">
  <strong>Production-Grade Multi-Branch E-Commerce + Retail POS + Real OTP Auth + Smart AI Importer + B2B Wholesale + Inventory 2.0 + WhatsApp & Email Communication Platform</strong>
</p>

<p align="center">
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  </a>
  <a href="https://php.net">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
  </a>
  <a href="https://livewire.laravel.com">
    <img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3" />
  </a>
  <a href="https://www.mysql.com">
    <img src="https://img.shields.io/badge/MySQL-Supported-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Supported" />
  </a>
  <a href="https://www.postgresql.org">
    <img src="https://img.shields.io/badge/PostgreSQL-Ready-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL Ready" />
  </a>
  <img src="https://img.shields.io/badge/OTP%20Security-Bcrypt%20Hashed%20%7C%20Zero--Plaintext-success?style=for-the-badge&logo=auth0" alt="OTP Security" />
  <a href="LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License MIT" />
  </a>
</p>

---

## 🌐 Live Demo & Source Code

<p align="center">

### 🚀 LIVE APPLICATION

<a href="https://ak-mart.onrender.com/">
  <img src="https://img.shields.io/badge/🚀%20LIVE%20DEMO-ak--mart.onrender.com-2ea44f?style=for-the-badge" alt="Live Demo" />
</a>

<br><br>

### 💻 GITHUB REPOSITORY

<a href="https://github.com/akhilmeleppura/Ak-Mart">
  <img src="https://img.shields.io/badge/💻%20SOURCE%20CODE-GitHub-181717?style=for-the-badge&logo=github" alt="GitHub Repository" />
</a>

</p>

### 🔗 Direct Links

**🌐 Live Demo:**  
https://ak-mart.onrender.com/

**💻 GitHub Repository:**  
https://github.com/akhilmeleppura/Ak-Mart

> The live application provides access to the AK-Mart platform and its implemented retail, e-commerce, inventory, POS, authentication, and management features.

---

## 🌟 Overview

**AK-Mart** is an enterprise-grade Omni-Channel Retail and E-Commerce operating system built with **Laravel 12**.

Designed for multi-branch retail networks, department stores, wholesale suppliers, and digital brands, AK-Mart brings together:

- Real-time POS checkout
- Multi-branch inventory
- Multi-warehouse stock management
- Smart AI product importing
- B2B wholesale workflows
- Modular SaaS-oriented architecture
- Secure OTP authentication
- WhatsApp and Email communication
- Workflow automation
- Multi-language localization

into a unified platform.

---

## 🚀 Key Modules & Capabilities

### 1. 🔐 Cryptographic OTP Authentication Layer

- **Zero Plaintext Storage:** All OTPs are generated cryptographically using `random_int()` and stored strictly as Bcrypt hashes via `Hash::make()`.
- **Purpose Isolation:** Strict segregation between `LOGIN`, `PASSWORD_RESET`, `EMAIL_VERIFICATION`, and `PHONE_VERIFICATION`.
- **Single-Use & Replay Protection:** Automatic invalidation after successful verification or attempt exhaustion.
- **Rate Limiting & Resend Cooldown:** 60-second resend cooldown, maximum 3 resends per flow, and 5 failed-attempt brute-force lockouts.
- **Segmented UI:** Interactive 6-digit input boxes with auto-focus, backspace navigation, clipboard paste parsing, dynamic countdown timer, and AJAX resend.
- **3-Step Password Recovery:** Email request → OTP verification → Short-lived authorization token (10 min) → Secure password reset.

---

### 2. 🔍 Universal Searchable Select System

- **Centralized Select2 Component:** Reusable `<x-searchable-select>` Blade component.
- **Server-Side Pagination:** Efficient AJAX-driven search with pagination.
- **Keyboard Navigation:** Improved usability for admin workflows.
- **Debouncing:** 300ms search debounce to reduce unnecessary server requests.

#### Supported Search Endpoints

- **Products:** Search by Name, SKU, and Barcode with live price and stock information.
- **Customers:** Search by Name, Email, and Phone.
- **Branches:** Dynamically scoped to authorized branch permissions.
- **Suppliers:** Search by Name, Company Name, and Contact.
- **Categories**
- **Roles**
- **Staff Users**

#### Security

- Client-submitted IDs are re-validated server-side.
- Tenant scope and active permissions are checked before processing.

---

### 3. 🤖 Smart Product Engine 2.0 & AI Importer

- **Universal Multi-Platform Scraping**
  - Amazon (ASIN)
  - Flipkart
  - Meesho
  - Shopify
  - Schema.org JSON-LD

- Automated extraction of:
  - Product information
  - Images
  - Variants
  - Specifications

- **Deterministic Ground-Truth Parsing:** Fallback mechanisms are designed to prevent hallucination of product prices, quantities, or details.
- **Listing Quality Score:** 0–100% score based on:
  - Title completeness
  - Description quality
  - SKU/barcode validity
  - Media quality
  - SEO readiness

- **Staging & Approval Queue**
  - Import drafts
  - Review extracted data
  - Refine pricing
  - Publish with one click

- **AI Content Generator & Copilot**
  - SEO titles
  - Meta descriptions
  - Selling points
  - Category suggestions

---

### 4. 🏢 Multi-Branch & Multi-Warehouse Inventory 2.0

- **Multi-Location Fulfillment**
  - Independent branch stock
  - Regional warehouse stock

- **Atomic Concurrency Locking**
  - Database row locking
  - Stock reservations
  - Protection against overselling during high-volume transactions

- **ABC Inventory Intelligence**
  - Class A — Fast-Moving
  - Class B — Moderate
  - Class C — Dead Stock

- **Cycle Counting**
  - Physical inventory audit
  - Reconciliation
  - Variance adjustment

- **Stock Transfers**
  - Inter-branch transfers
  - Transfer tracking

---

### 5. 💳 POS Terminal 2.0

- **High-Speed Barcode Scanning**
- Hotkey accelerators
- Variant selection
- Fast item lookup

#### Register Shift Sessions

- Opening float
- Cash sales
- Paid-in / paid-out transactions
- Closing counts
- Discrepancy reports

#### Multi-Tender Payments

- Cash
- Card
- UPI
- Digital Gift Cards
- Split Payments

**POS Route:**

```text
/pos
