# 📜 AKMART AI — PHASE 6: INVENTORY INTELLIGENCE CHANGELOG

**Document ID**: AKMART-DOC-INV-CHANGELOG-006  
**Release**: v2.9.0-AiInventory-Phase6  
**Date**: August 2026  

---

## 1. ADDED FEATURES & CAPABILITIES

### 📦 Multi-Horizon Demand Forecasting
- Added **Multi-Horizon Forecast Engine** (7d, 14d, 30d, 60d, 90d) with confidence score in [`InventoryIntelligenceService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/InventoryIntelligenceService.php).

### ⏳ Dynamic Stockout & Reorder Point Engine
- Calculates runway days, dynamic safety stock, and lead-time-aware reorder points.

### 📝 Automated Purchase Order & Transfer Drafter
- Generates structured PO and stock transfer drafts with mandatory manager approval.

### 🚨 Dead Stock, Overstock & Anomaly Intelligence
- Classifies Dead Stock (0 sales in 90 days), Overstock (> 180 days runway), and detects large manual adjustment anomalies.

### 🧪 Automated Test Suite
- Added [`AiInventoryIntelligenceSuiteTest.php`](file:///c:/xampp/htdocs/Ak-mart/tests/Feature/AiInventoryIntelligenceSuiteTest.php) with 100% pass rate.
