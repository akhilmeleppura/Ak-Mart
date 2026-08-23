# 📈 AKMART AI — PHASE 4: CUSTOMER LIFETIME VALUE (CLV) MODEL

**Document ID**: AKMART-DOC-CRM-CLV-004  
**Date**: August 2026  

---

## 1. FORMAL MATHEMATICAL CLV FORMULA

$$\text{CLV}_{\text{Total}} = \text{Spend}_{\text{Historical}} + \text{Value}_{\text{Predicted (12M)}}$$

$$\text{Value}_{\text{Predicted (12M)}} = \left( \frac{\text{Orders}}{\text{Tenure (Days)}} \times 365 \right) \times \text{AOV} \times \text{Retention Factor (0.85)}$$

---

## 2. CONFIDENCE CLASSIFICATION

- **High Confidence**: Customer has placed $\ge 5$ orders across $\ge 60$ days.
- **Medium Confidence**: Customer has placed $2 - 4$ orders.
- **Low / Insufficient Data**: Customer has placed $\le 1$ order (insufficient historical repeat variance).
