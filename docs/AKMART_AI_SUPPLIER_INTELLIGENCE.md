# 🏭 AKMART AI — SUPPLIER PERFORMANCE & LEAD TIME INTELLIGENCE

**Document ID**: AKMART-DOC-INV-SUPPLIER-006  
**Date**: August 2026  

---

## 1. OBSERVED LEAD TIME EVALUATION

Evaluates actual fulfillment delivery dates against contracted lead times from `purchase_orders` and `goods_received` records.

## 2. SUPPLIER SCORECARD METRICS

- **Lead Time Reliability**: Mean deviation between promised delivery date and physical dock receipt.
- **Fill Rate**: $\frac{\text{Received Units}}{\text{Ordered Units}} \times 100$.
- **Defect/Return Rate**: Percentage of goods rejected during intake inspection.
