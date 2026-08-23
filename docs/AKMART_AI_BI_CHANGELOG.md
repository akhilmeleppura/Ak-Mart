# 📜 AKMART AI — PHASE 8: BUSINESS INTELLIGENCE CHANGELOG

**Document ID**: AKMART-DOC-BI-CHANGELOG-008  
**Release**: v3.1.0-AiBI-Phase8  
**Date**: August 2026  

---

## 1. ADDED FEATURES & CAPABILITIES

### 📖 Centralized KPI Registry
- Added authoritative registry of commercial KPIs (Gross Revenue, AOV, Net Profit, Margin %, Return Rate) in [`BusinessIntelligenceService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/BusinessIntelligenceService.php).

### 📰 Comprehensive Executive Daily Business Brief
- Multi-domain daily brief synthesizing Sales, Profit, Inventory runway, and Customer acquisitions with actionable recommendations.

### 📊 Natural-Language Period-over-Period Comparison
- Flexible date window comparison (Month-over-Month, Week-over-Week) computing exact deltas ($ and %).

### 🔍 Revenue Decomposition & Scenario Simulation
- Category revenue contribution breakdown and read-only discount/volume What-If simulation engine.

### 🧪 Automated Test Suite
- Added [`AiBusinessIntelligenceSuiteTest.php`](file:///c:/xampp/htdocs/Ak-mart/tests/Feature/AiBusinessIntelligenceSuiteTest.php) with 100% pass rate.
