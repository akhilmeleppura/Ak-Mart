# 🛡️ AKMART AI — RISK & FRAUD ARCHITECTURE

**Document ID**: AKMART-DOC-RISK-ARCH-007  
**Date**: August 2026  

---

## 1. END-TO-END RISK EVALUATION PIPELINE

```text
Customer Checkout / Order Placed
  │
  ▼
RiskIntelligenceService (Non-Blocking Assessment)
  ├── Order Risk (Velocity, AOV Deviation > 3x)
  ├── Payment Risk (Gateway Failures, Decline Spikes)
  ├── COD Risk (Cancellation & RTO Rate >= 50%)
  └── Return Risk (Abnormal Return Frequency > 40%)
  │
  ▼
Score Aggregation (0–100) & Risk Level (Low / Medium / High / Critical)
  │
  ▼
Explainable Signal Generation
  │
  ▼
Admin Risk Review Queue (Human Approval for High/Critical)
```
