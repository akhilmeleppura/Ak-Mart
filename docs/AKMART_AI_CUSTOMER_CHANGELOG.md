# 📜 AKMART AI — PHASE 4: CUSTOMER INTELLIGENCE CHANGELOG

**Document ID**: AKMART-DOC-CRM-CHANGELOG-004  
**Release**: v2.7.0-AiCRM-Phase4  
**Date**: August 2026  

---

## 1. ADDED FEATURES & CAPABILITIES

### 👤 Customer 360 Feature Aggregation
- Implemented [`CustomerIntelligenceService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/CustomerIntelligenceService.php) calculating AOV, return rate, days since last order, preferred categories, and wallet/loyalty balances.

### 👥 Explainable Customer Segmentation
- Added deterministic classification for **VIP**, **High Value**, **Returning Customer**, **New Customer**, **At Risk**, and **Inactive** segments.

### 📈 Customer Lifetime Value (CLV) Engine
- Calculates historical net spend and statistical 12-month projected value with confidence levels.

### ⚠️ Explainable Churn Risk & Next-Best-Action
- Evaluates purchase gaps and return frequency to generate marketing recommendations (*Win-Back campaign*, *VIP loyalty reward*, *Cross-sell*).

### 🧪 Automated Test Suite
- Added [`AiCustomerIntelligenceSuiteTest.php`](file:///c:/xampp/htdocs/Ak-mart/tests/Feature/AiCustomerIntelligenceSuiteTest.php) with 100% pass rate.
