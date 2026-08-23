# ⚖️ AKMART AI — DETERMINISTIC RISK SCORING RULES

**Document ID**: AKMART-DOC-RISK-RULES-007  
**Date**: August 2026  

---

## 1. DETERMINISTIC RISK SCORING WEIGHTS

| Risk Factor | Trigger Condition | Score Points |
| :--- | :--- | :--- |
| **AOV Spike** | Order Value $\ge 3\times$ Customer's Historical Average AOV | +30 Points |
| **Payment Failures** | $\ge 2$ Consecutive Failed Payment Transactions | +25 Points |
| **COD RTO Rate** | Customer COD Cancellation/RTO Rate $\ge 50\%$ (Min 3 orders) | +30 Points |
| **Elevated Return Rate**| Customer Return Rate $> 40\%$ | +20 Points |

---

## 2. RISK LEVEL THRESHOLDS

- **Low**: 0–29 Points $\rightarrow$ Auto-Approve
- **Medium**: 30–59 Points $\rightarrow$ Standard Review
- **High**: 60–79 Points $\rightarrow$ Hold for Verification
- **Critical**: 80–100 Points $\rightarrow$ Manual Fraud Investigation
