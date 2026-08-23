# 📜 AKMART AI — PHASE 7: FRAUD & RISK CHANGELOG

**Document ID**: AKMART-DOC-RISK-CHANGELOG-007  
**Release**: v3.0.0-AiRisk-Phase7  
**Date**: August 2026  

---

## 1. ADDED FEATURES & CAPABILITIES

### 🛡️ Multi-Factor Explainable Order Risk Engine
- Added [`RiskIntelligenceService.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/RiskIntelligenceService.php) calculating 0–100 risk score, risk level (*Low*, *Medium*, *High*, *Critical*), and human-readable evidence signals.

### 🚚 Cash-on-Delivery (COD) Risk & RTO Mitigation
- Evaluates COD refusal rates and recommends *"Require Prepaid Payment"* for chronic RTO patterns.

### 💳 Payment Anomaly & Gateway Spike Detection
- Audits 24-hour transaction failure rates for operational gateway degradation.

### 🎟️ Promotion Abuse & Cluster Detection
- Identifies rapid multi-account coupon redemption spikes.

### 🧪 Automated Test Suite
- Added [`AiRiskIntelligenceSuiteTest.php`](file:///c:/xampp/htdocs/Ak-mart/tests/Feature/AiRiskIntelligenceSuiteTest.php) with 100% pass rate.
