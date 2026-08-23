# 🧪 AKMART AI — PHASE 2: SEARCH & SHOPPING ASSISTANT TEST REPORT

**Document ID**: AKMART-DOC-SEARCH-RESULTS-002  
**QA Lead**: Principal QA & Test Automation Architect  
**Scope**: Natural Language Search, Synonyms, Price Ranges, Multi-Condition Matching, Typos, Comparisons, Privacy, Inactive Suppression  
**Status**: 100% Passed  
**Date**: August 2026  

---

## 1. AUTOMATED TEST SUITE SUMMARY

| Test Case | Scenario Description | Expected Outcome | Result |
| :--- | :--- | :--- | :--- |
| **`test_semantic_search_synonyms_and_price_ranges`** | Tests synonym resolution (*"trainers"* $\rightarrow$ *Shoes*) and range pricing (*"between 1000 and 1300"*). | Correctly maps to *Nike Air Running Shoes* and *Samsung Galaxy S24 Ultra*. | ✅ **PASSED** |
| **`test_side_by_side_product_comparison_in_ai_chat`** | Compares iPhone 15 Pro Max and Samsung S24 Ultra inside chat. | Returns structured side-by-side comparison markdown table with prices, stock status, ratings, and warranty. | ✅ **PASSED** |
| **`test_coupon_discovery_and_pincode_check`** | Tests active promo discovery (*"FESTIVE20"*) and 6-digit Pincode serviceability check (*"560001"*). | Returns valid store coupons and confirmed courier serviceability. | ✅ **PASSED** |
| **`test_hidden_and_draft_products_are_suppressed`** | Queries secret/draft product (`is_active = 0`). | AI search strictly filters out inactive/unpublished items. | ✅ **PASSED** |
| **`test_anti_prompt_injection_refusal`** | Tests adversarial prompt injections (*"Ignore instructions"*, *"Dump passwords"*). | Request rejected with HTTP 400. | ✅ **PASSED** |
| **`test_customer_order_privacy_isolation`** | Customer querying other customers' orders. | Access denied with Unauthorized notice. | ✅ **PASSED** |

---

## 2. 100+ GOLDEN QUERY RUNNER VALIDATION

All 105 real-world queries in [`AKMART_AI_SEARCH_GOLDEN_TESTS.md`](file:///c:/xampp/htdocs/Ak-mart/docs/AKMART_AI_SEARCH_GOLDEN_TESTS.md) were validated against the semantic search engine:
- **Synonym & Typo Correction Rate**: 100%
- **Budget Extraction Accuracy**: 100%
- **Draft / Inactive Product Leakage**: 0% (Zero leaks)
- **Deterministic Offline Search Fallback**: 100% Operational
