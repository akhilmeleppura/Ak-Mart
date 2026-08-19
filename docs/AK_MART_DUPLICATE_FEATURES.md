# AK-Mart — Duplicate Feature Audit & Canonical Resolution

This document audits and clarifies legacy vs active implementations across AK-Mart.

| Feature Area | Primary / Active Implementation | Legacy / Scaffold Implementation | Resolution / Status |
|--------------|---------------------------------|-----------------------------------|---------------------|
| **Settings System** | Unified 28-Section `SettingsHubController` backed by `store_settings` | Legacy static tab views | Consolidated into single database-backed Hub |
| **Customer Details** | `/customers/{id}/overview`, `/security`, `/billing`, `/notifications` | `/app/ecommerce/customer/details/*` | Fixed tab routing to use dynamic customer IDs |
| **SaaS Billing** | `/billing` (`SubscriptionController`) | Mock static HTML pages | Live database subscription tier limits & invoices |
| **User Profile** | `/account/settings` (`ProfileController`) | Unconnected HTML forms | Dynamic avatar uploads, password hashing, and preference persistence |
| **Route Aliasing** | Standard REST URIs | Legacy Sneat template URIs | Canonical route names registered with backwards-compatible aliases |
