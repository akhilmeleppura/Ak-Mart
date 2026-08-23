# 📈 AKMART AI — DEMAND FORECASTING MATHEMATICAL MODEL

**Document ID**: AKMART-DOC-INV-FORECAST-006  
**Date**: August 2026  

---

## 1. MULTI-HORIZON DEMAND FORMULA

$$\text{Daily Velocity} = \frac{\sum_{t=1}^{60} \text{Units Sold}}{\text{Lookback Window (60 Days)}}$$

$$\text{Forecast}(N \text{ Days}) = \lceil \text{Daily Velocity} \times N \rceil$$

---

## 2. RUNWAY & DAYS-TO-STOCKOUT

$$\text{Days to Stockout} = \left\lfloor \frac{\text{Current Stock} + \text{Incoming Stock} - \text{Committed Stock}}{\text{Daily Velocity}} \right\rfloor$$

| Risk Level | Runway Horizon | Action |
| :--- | :--- | :--- |
| **Critical** | $\le 3$ Days | Immediate expedited PO generation. |
| **High** | $4 - 7$ Days | Standard PO generation. |
| **Medium** | $8 - 14$ Days | Monitor reorder threshold. |
| **Low** | $> 14$ Days | Healthy inventory. |
