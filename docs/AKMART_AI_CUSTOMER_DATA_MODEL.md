# 📊 AKMART AI — PHASE 4: CUSTOMER INTELLIGENCE DATA MODEL

**Document ID**: AKMART-DOC-CRM-DATA-004  
**Date**: August 2026  

---

## 1. FACTUAL DATA VS PREDICTIVE INTELLIGENCE DATA MODEL

```text
┌────────────────────────────────────────────────────────┐
│                   FACTUAL DATA (STORE OF TRUTH)        │
├────────────────────────────────────────────────────────┤
│ • Total Orders Placed          • Lifetime Net Spend    │
│ • Average Order Value (AOV)    • Days Since Last Order │
│ • Wallet & Loyalty Balances    • Return History        │
│ • Preferred Categories         • Account Created Date  │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼ Feature Aggregation
┌────────────────────────────────────────────────────────┐
│               PREDICTED / DERIVED INTELLIGENCE         │
├────────────────────────────────────────────────────────┤
│ • Lifecycle Segment (VIP / High-Value / At-Risk / etc.)│
│ • 12-Month Projected CLV ($) & Confidence Score        │
│ • Churn Risk (Low / Medium / High) + Supporting Signals│
│ • Next-Best-Action Recommendation (Campaign / VIP / etc│
└────────────────────────────────────────────────────────┘
```

---

## 2. METRIC DEFINITIONS & METHODOLOGIES

1. **Historical Net Spend**: $\text{Historical Spend} = \sum \text{Orders (Paid)} - \text{Refunds}$.
2. **Average Order Value (AOV)**: $\text{AOV} = \frac{\text{Historical Net Spend}}{\text{Total Orders}}$.
3. **Return Rate %**: $\text{Return Rate} = \frac{\text{Total Returns}}{\text{Total Orders}} \times 100$.
4. **Projected 12M CLV**: $\text{Projected CLV} = \text{Annual Frequency} \times \text{AOV} \times \text{Retention Factor (0.85)}$.
