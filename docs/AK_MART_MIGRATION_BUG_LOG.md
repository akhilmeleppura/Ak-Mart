# AK-Mart — Modular Architecture Migration Bug Log

| Bug ID | Component | Description | Root Cause | Resolution | Status |
|--------|-----------|-------------|------------|------------|--------|
| **MOD-01** | `Modules/Ecommerce` | PSR-4 namespace mismatch for `EcommerceController` | Directory casing `app/Http/Controllers` vs `Http/Controllers` | Normalized PSR-4 paths in module directories | ✅ FIXED |
| **MOD-02** | `Modules/SaaS` | KYC Review route missing dedicated Blade detail view | `KycAdminController@show` returned unauthored view | Created `kyc-detail.blade.php` | ✅ FIXED |
| **MOD-03** | `Modules/Accounting` | Trial balance ledger drill-down link route name mismatch | View used `.view` instead of `.details` | Corrected to canonical `accounting.ledger.details` | ✅ FIXED |
| **MOD-04** | `Modules/Logistics` | Delete and toggle actions missing route declarations | Methods existed in controller but omitted in web.php | Registered `destroy` and `toggle` routes | ✅ FIXED |
| **MOD-05** | `Routing` | Route serialization collision during `route:cache` | Duplicate route names in alias definitions | Cleaned route aliases & deduplicated names | ✅ FIXED |