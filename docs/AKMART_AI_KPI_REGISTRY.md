# 📖 AKMART AI — ENTERPRISE KPI REGISTRY

**Document ID**: AKMART-DOC-BI-KPIS-008  
**Date**: August 2026  

---

## 1. AUTHORITATIVE KPI DEFINITIONS & FORMULAS

| KPI Name | Formula | Authoritative Source | Permission Required |
| :--- | :--- | :--- | :--- |
| **Gross Revenue** | $\sum \text{Orders.total\_amount WHERE paid}$ | `orders` | Admin, Manager, Finance |
| **Average Order Value (AOV)** | $\frac{\text{Gross Revenue}}{\text{Paid Orders Count}}$ | `orders` | Admin, Manager |
| **Net Profit** | $\text{Gross Revenue} - \text{COGS} - \text{Expenses} - \text{Refunds}$ | `orders, expenses, order_returns`| Admin, Super Admin, Finance |
| **Net Profit Margin %** | $\left( \frac{\text{Net Profit}}{\text{Gross Revenue}} \right) \times 100$ | `orders, expenses` | Admin, Super Admin, Finance |
| **Return Rate %** | $\left( \frac{\text{Returns Count}}{\text{Total Orders Count}} \right) \times 100$ | `order_returns, orders` | Admin, Manager, Ops |
