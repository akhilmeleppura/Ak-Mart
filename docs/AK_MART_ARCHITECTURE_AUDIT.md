# AK-Mart — Full Architecture Audit & System Topology

## System Overview
- **Framework:** Laravel 12.x on PHP 8.2.12
- **Architecture Pattern:** Multi-Tenant E-Commerce SaaS with Branch Isolation
- **Database Engine:** MySQL with 66 Normalized Migrations
- **Total Endpoints:** 518 Production-Cached Routes
- **Total Views:** 401 Blade Templates
- **Total Controllers:** 251
- **Localization:** 6 Supported Locales (EN, ML, HI, AR with RTL, FR, DE)

## Core Architectural Layers
1. **Presentation Layer (Blade):** Rich Bootstrap 5 & Sneat Design System with responsive grid, ApexCharts, Flatpickr, SweetAlert2.
2. **Routing & Middleware Layer:** Multi-tenant subscription gating (`tenant.subscription`), Spatie role/permission checking, CSRF validation.
3. **Controller & Service Layer:** Clean separation between presentation logic (`app/Http/Controllers`) and business domain services (`app/Services/SettingsService.php`, `PlanLimitService.php`, etc.).
4. **Persistence Layer (Eloquent):** Multi-tenant branch scoped models with automated fallbacks and encrypted credentials.
