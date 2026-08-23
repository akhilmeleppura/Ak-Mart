# 🛡️ AKMART AI — PHASE 7: FRAUD, RISK & TRUST AUDIT

**Document ID**: AKMART-DOC-RISK-AUDIT-007  
**Lead Risk Architect**: Principal E-commerce Risk Architect & Security Engineer  
**Classification System**: COMPLETE | PARTIAL | MISSING | DUPLICATE | BROKEN | NEEDS UPGRADE  
**Date**: August 2026  

---

## 1. COMPREHENSIVE FRAUD & RISK SUBSYSTEM AUDIT

| Subsystem Component | Current State | Classification | Upgrade Action in Phase 7 |
| :--- | :--- | :--- | :--- |
| **Order Fraud Scoring** | Basic `fraud_score` column on `orders`. | 🟡 **NEEDS UPGRADE** | Implement multi-factor explainable risk scoring (Order Risk, Payment Risk, COD Risk, Return Risk, Coupon Abuse). |
| **Cash-on-Delivery (COD) Risk** | Basic COD flag. | 🟡 **NEEDS UPGRADE** | Add COD refusal and RTO velocity analyzer with "Require Prepaid" business recommendations. |
| **Payment Anomaly Detection** | [`OrderTransaction.php`](file:///c:/xampp/htdocs/Ak-mart/app/Models/OrderTransaction.php) status checks. | 🟡 **NEEDS UPGRADE** | Detect sudden spikes in gateway failures, velocity anomalies, and repeated card decline patterns. |
| **Coupon & Promotion Abuse** | Single-use coupon flag. | 🟡 **PARTIAL** | Implement promotion cluster analysis detecting rapid multi-account new-user coupon exploitation. |
| **Return & Refund Risk** | [`OrderReturn.php`](file:///c:/xampp/htdocs/Ak-mart/app/Models/OrderReturn.php) records. | 🟡 **PARTIAL** | Calculate return-to-order ratio and high-frequency refund alerts without customer discrimination. |
| **Risk Review Queue** | None. | 🔴 **MISSING** | Create dedicated Admin Risk Review Queue showing risk level, confidence, and supporting evidence. |
| **Privacy & Zero-Bias Governance** | Strict RBAC in place. | ✅ **COMPLETE** | Strictly enforce zero sensitive demographic markers in risk scoring. |

---

## 2. REUSE & NO-DUPLICATION COMMITMENT

1. **No Autonomous Customer Punishment**: AI generates risk scores and signals; human managers retain decision authority.
2. **Checkout Fault Tolerance**: If the AI risk service experiences latency or failure, orders continue through deterministic fallback rules.
3. **Deterministic Source of Truth**: Underlying transaction statuses, payment gateway tokens, and order returns in MySQL/SQLite remain the authoritative source.
