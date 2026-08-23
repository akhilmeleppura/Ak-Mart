# 🧪 AKMART — AUTOMATED TEST SUITE & QUALITY ASSURANCE PLAN

**Document ID**: AKMART-DOC-TEST-008  
**Framework**: PHPUnit 11.5.3 / Laravel Testing Engine  
**Current Test Coverage**: 81 Passing Feature/Unit Tests (363+ Assertions, 0 Failures)  
**Date**: August 2026  

---

## 1. AUTOMATED TEST SUITE MATRIX

| Test Suite File | Domain / Focus Area | Assertions | Status |
| :--- | :--- | :--- | :--- |
| `tests/Feature/AdvancedCommerceTest.php` | Stock movement ledger, inter-branch transfers, PO receiving, returns restocking, expense tracker, catalog health scanner, AI offline generator, Storefront API v1 | 50 | ✅ **PASS** |
| `tests/Feature/NextGenCommerceTest.php` | Multi-warehouse allocation, cycle counting reconciliation, ABC inventory analysis, B2B tier pricing & quotes, fulfillment orders, customer wishlist & saved cart, POS shift register, omnichannel feeds, webhooks & system health | 78 | ✅ **PASS** |
| `tests/Feature/CommerceRegressionAuditTest.php`| Price validation bounds, customer IDOR protection, coupon calculation rules, PO partial receiving, gift card & store credit balance constraints, smart importer offline fallback, POS atomic checkout, workflow automation rendering, notifications mark-all | 55 | ✅ **PASS** |
| `tests/Feature/InternationalizationSuiteTest.php`| 6-Language switching (EN, ML, HI, AR RTL, FR, DE), user locale persistence, Arabic RTL layout injection, AI copilot multi-language chat, artisan translation audit command | 28 | ✅ **PASS** |
| `tests/Feature/UniversalProductImporterTest.php`| Flipkart extraction, Meesho extraction, Shopify store extraction, generic e-commerce JSON-LD extractor | 24 | ✅ **PASS** |
| `tests/Feature/AmazonProductImporterTest.php` | Amazon ASIN/URL extraction, title, price, bullet features, images, and anti-SSRF protection | 20 | ✅ **PASS** |
| `tests/Feature/AuthenticationTest.php` | Login with email/password, OTP validation, invalid credential lockout | 15 | ✅ **PASS** |
| `tests/Feature/BranchAndPermissionTest.php` | Multi-branch query isolation, cashier branch boundaries, supreme admin bypass | 18 | ✅ **PASS** |
| `tests/Feature/TwoFactorAuthenticationSettingsTest.php` | 2FA enabling, recovery codes regeneration, 2FA disabling | 12 | ✅ **PASS** |
| `tests/Feature/ProfileInformationTest.php` | Customer and admin profile updating, password change validation | 14 | ✅ **PASS** |

---

## 2. CONTINUOUS TESTING & REGRESSION PROTOCOL

1. **Pre-Commit Verification**: Run `php artisan test` before any PR or merge.
2. **Deterministic Fallback Validation**: AI tools and Smart Importers must execute reliably in isolated environments where external network calls are mocked or offline.
3. **Ledger Balance Verification**: Inventory and Wallet balances are asserted before and after operations to ensure zero floating-point discrepancies or missing audit logs.
