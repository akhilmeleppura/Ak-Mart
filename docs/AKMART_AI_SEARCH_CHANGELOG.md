# 📜 AKMART AI — PHASE 2: SEARCH & SHOPPING ASSISTANT CHANGELOG

**Document ID**: AKMART-DOC-SEARCH-CHANGELOG-002  
**Release**: v2.6.0-AiSearch-Phase2  
**Date**: August 2026  

---

## 1. ADDED FEATURES & CAPABILITIES

### 🧠 Semantic & Natural Language Product Search
- Added **Commerce Synonyms Mapping** (*mobile* $\rightarrow$ *Phone*, *trainers* $\rightarrow$ *Shoes*, *fridge* $\rightarrow$ *Refrigerator*, *tv* $\rightarrow$ *Television*) in [`SemanticSearchService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/SemanticSearchService.php).
- Added **Dynamic Budget Range Filters** (*"between ₹5,000 and ₹10,000"*, *"₹10k max"*, *"under ₹3,000"*).
- Added **Multi-Condition Attribute & Spec Matching** (*Color*, *Brand*, *5G*, *RAM*, *Category*).
- Added **In-Stock Ranking Prioritization** (active purchasable products ranked before out-of-stock items).

### ⚖️ Side-by-Side Product Comparison in AI Chat
- Added `compareProducts()` method in [`SemanticSearchService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/SemanticSearchService.php) returning structured markdown table comparing Price, Stock, Rating, Category, and Warranty with `"Not specified"` for missing specs.

### 🛍️ Enhanced Customer Shopping Assistant
- Added **Coupon Discovery Tool** (retrieves active unexpired discount codes).
- Added **Pincode Delivery Check** (integrated with Indian 6-digit courier serviceability verification).
- Added **Out-of-Stock Alternative Recommendations**.

### 📊 Search & Zero-Result Analytics
- Created `search_query_logs` migration and [`SearchQueryLog.php`](file:///c:/xampp/htdocs/Ak-mart/app/Models/SearchQueryLog.php) to track customer queries, parsed intent, and zero-result queries for merchandising improvement.

### 🧪 Golden Query Dataset & Automated Tests
- Created [`AKMART_AI_SEARCH_GOLDEN_TESTS.md`](file:///c:/xampp/htdocs/Ak-mart/docs/AKMART_AI_SEARCH_GOLDEN_TESTS.md) with 105 real-world queries.
- Added [`AiSearchAndShoppingSuiteTest.php`](file:///c:/xampp/htdocs/Ak-mart/tests/Feature/AiSearchAndShoppingSuiteTest.php) with 100% pass rate.
