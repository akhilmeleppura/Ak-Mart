# 📜 AKMART — PLATFORM CHANGELOG & UPGRADE HISTORY

**Document ID**: AKMART-DOC-LOG-010  
**Current Release**: v2.5.0-Enterprise  
**Date**: August 2026  

---

## [v2.5.0-Enterprise] — 2026-08-23
### Added
- **Complete Non-AI Omnichannel Architecture**: Audited and confirmed in [`AKMART_NON_AI_GAP_ANALYSIS.md`](file:///c:/xampp/htdocs/Ak-mart/docs/AKMART_NON_AI_GAP_ANALYSIS.md).
- **Bulk Product Operations**: Implemented `bulkStock` endpoint with immutable `StockMovement` logging, alongside existing `bulkStatus`, `bulkCategory`, `bulkPricing`, and product `duplicate`.
- **Pluggable Carrier Abstraction**: Extended `ShippingService` with Indian courier adapters (**Delhivery**, **BlueDart**, **Shiprocket**) and 6-digit Pincode serviceability checking.
- **AI Scope Containment**: Enforced single **AKMart AI Chat / Copilot** conversational boundary for store policy, sales, inventory, and profit Q&A without unverified autonomous agents.
- **Complete Architecture Documentation Suite**: 10 enterprise specification documents in `docs/` (`AKMART_AUDIT.md`, `AKMART_FEATURE_MATRIX.md`, `AKMART_ARCHITECTURE.md`, `AKMART_AI_ARCHITECTURE.md`, `AKMART_DATABASE.md`, `AKMART_API.md`, `AKMART_SECURITY.md`, `AKMART_TEST_PLAN.md`, `AKMART_DEPLOYMENT.md`, `AKMART_CHANGELOG.md`).
- **Automated Non-AI Commerce Feature Test Suite**: Added [`NonAiCommerceSuiteTest.php`](file:///c:/xampp/htdocs/Ak-mart/tests/Feature/NonAiCommerceSuiteTest.php) testing bulk stock/pricing, courier abstraction, back-in-stock subscriptions, and AI Copilot.

### Verified
- **86/86 Automated Feature & Unit Tests Passing**: 391 assertions verified with 100% pass rate.
