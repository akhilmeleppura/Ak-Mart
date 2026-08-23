# 📦 AKMART AI — PHASE 6: INVENTORY & FORECASTING AUDIT

**Document ID**: AKMART-DOC-INV-AUDIT-006  
**Lead Supply Chain Architect**: Principal AI Supply Chain & Inventory Systems Architect  
**Classification System**: COMPLETE | PARTIAL | MISSING | DUPLICATE | BROKEN | NEEDS UPGRADE  
**Date**: August 2026  

---

## 1. COMPREHENSIVE INVENTORY SUBSYSTEM AUDIT

| Subsystem Component | Current State | Classification | Upgrade Action in Phase 6 |
| :--- | :--- | :--- | :--- |
| **Immutable Stock Movement Ledger** | [`StockMovement.php`](file:///c:/xampp/htdocs/Ak-mart/app/Models/StockMovement.php) recording all ins/outs. | ✅ **COMPLETE** | Strictly preserved as single immutable source of truth. |
| **Demand Forecasting** | Basic daily sales velocity. | 🟡 **NEEDS UPGRADE** | Implement multi-horizon forecasting (7d, 14d, 30d, 60d, 90d) with confidence score & data coverage analysis. |
| **Stockout Risk & Runway** | Basic low-stock alert (`qty <= low_stock_threshold`). | 🟡 **NEEDS UPGRADE** | Implement dynamic Days-to-Stockout runway calculation: $\frac{\text{Current Stock} + \text{Incoming} - \text{Committed}}{\text{Daily Velocity}}$. |
| **Safety Stock & Reorder Point** | Static reorder point field. | 🟡 **NEEDS UPGRADE** | Dynamic reorder point: $\text{Reorder Point} = (\text{Lead Time (Days)} \times \text{Daily Demand}) + \text{Safety Stock}$. |
| **Purchase Order Drafter** | Manual PO creation in [`PurchaseOrder.php`](file:///c:/xampp/htdocs/Ak-mart/app/Models/PurchaseOrder.php). | 🟡 **NEEDS UPGRADE** | Generate automated draft purchase orders for low-stock items with supplier selection and quantity recommendations. |
| **Multi-Branch Balancing** | [`Branch`](file:///c:/xampp/htdocs/Ak-mart/app/Models/Branch/Branch.php) and [`Warehouse`](file:///c:/xampp/htdocs/Ak-mart/app/Models/Warehouse.php). | 🟡 **NEEDS UPGRADE** | Implement inter-branch stock rebalancing transfer recommendations (e.g. Branch A surplus $\rightarrow$ Branch B deficit). |
| **Dead Stock & Overstock Detection** | None. | 🔴 **MISSING** | Identify dead stock (0 sales in 90 days) and overstocked SKUs (> 180 days runway) with clearance suggestions. |
| **Inventory Anomaly & Shrinkage** | None. | 🔴 **MISSING** | Detect unusual large stock adjustments, sudden negative variances, and cycle count discrepancies. |
| **Cycle Count Prioritization** | Basic ABC classification. | 🟡 **PARTIAL** | Prioritize cycle count schedules based on ABC value and recent anomaly flags. |

---

## 2. REUSE & NO-DUPLICATION COMMITMENT

1. **Immutable Ledger Integrity**: Stock balances are modified solely through `StockMovement::record()` and validated transactional services.
2. **Deterministic Source of Truth**: Core calculations (current stock, committed stock, ledger balances) use database mathematical aggregates.
3. **No Autonomous Mutations**: All PO generation and branch transfers are created in a `draft` state requiring manager approval.
