# 🔍 AKMART AI — PHASE 2: SEARCH & SHOPPING ASSISTANT AUDIT

**Document ID**: AKMART-DOC-SEARCH-AUDIT-002  
**Lead AI Search Engineer**: Principal Search Architect & Senior Laravel Engineer  
**Classification System**: EXISTING | PARTIAL | MISSING | DUPLICATE | NEEDS UPGRADE  
**Date**: August 2026  

---

## 1. COMPREHENSIVE SEARCH & ASSISTANT AUDIT

| Subsystem Component | Current Implementation State | Classification | Upgrade Action Required in Phase 2 |
| :--- | :--- | :--- | :--- |
| **Normal Deterministic Search** | [`StorefrontController::searchSuggestions`](file:///c:/xampp/htdocs/Ak-mart/app/Http/Controllers/Storefront/StorefrontController.php) & `shop` faceted filtering. | ✅ **EXISTING** | Preserved as primary deterministic search & offline fallback. |
| **Semantic Natural Language Parser** | [`SemanticSearchService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/SemanticSearchService.php) with basic budget extraction and typo dict. | 🟡 **NEEDS UPGRADE** | Extend with commerce synonym mapper (*mobile* $\rightarrow$ *phone*, *trainers* $\rightarrow$ *shoes*, *fridge* $\rightarrow$ *refrigerator*), multi-condition attribute filters (RAM, Color, Brand), range budgets (*between ₹5,000 and ₹10,000*), and B2B pricing awareness. |
| **Customer AI Shopping Assistant** | [`StorefrontAiAssistantController.php`](file:///c:/xampp/htdocs/Ak-mart/app/Http/Controllers/Storefront/StorefrontAiAssistantController.php) with return/shipping FAQs, product search, and anti-injection guard. | 🟡 **NEEDS UPGRADE** | Add side-by-side product comparison tool, out-of-stock alternative recommendation, coupon discovery tool, and cart summary inspection. |
| **Product Comparison Engine** | Side-by-side comparison page in [`compare.blade.php`](file:///c:/xampp/htdocs/Ak-mart/resources/views/storefront/compare.blade.php). | ✅ **EXISTING** | Expose structured table comparator directly inside AI assistant responses with "Not specified" for missing fields. |
| **Product Visibility & Guardrails** | `is_active` filter on `Product` query. | ✅ **EXISTING** | Strictly enforce hidden/draft suppression and B2B company-tier pricing in all AI searches. |
| **Search & Zero-Result Analytics** | None. | 🔴 **MISSING** | Add `SearchQueryLog` model/table to record queries, parsed intent, result counts, zero-result queries, and user latency. |
| **Multilingual Search (6 Langs)** | English (EN), Malayalam (ML), Hindi (HI), Arabic (AR RTL), French (FR), German (DE). | 🟡 **PARTIAL** | Support multilingual intent parsing (e.g. Malayalam *"₹15000-ന് താഴെ നല്ല ഫോൺ"* $\rightarrow$ Category: Phone, Max Price: 15000). |
| **Security & Customer Isolation** | [`PromptSecurityGuard.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/PromptSecurityGuard.php) | ✅ **EXISTING** | Prevent cross-customer cart/order leakage and unauthorized catalog data scraping. |

---

## 2. REUSE & NO-DUPLICATION COMMITMENT

1. **No Duplicate Chatbots**: Storefront Shopping Assistant uses the unified `StorefrontAiAssistantController` and `AiToolManager` pipeline.
2. **Deterministic Search Independence**: Normal catalog search and AJAX suggestions remain 100% operational and independent.
3. **Database Ground Truth**: The AI layer queries live `Product`, `ProductVariant`, `Category`, `B2bTierPrice`, and `StockMovement` records without hallucinating products or prices.
