# 📋 AKMART AI — DYNAMIC REORDER & SAFETY STOCK RULES

**Document ID**: AKMART-DOC-INV-REORDER-006  
**Date**: August 2026  

---

## 1. REORDER POINT FORMULA

$$\text{Reorder Point} = (\text{Daily Velocity} \times \text{Supplier Lead Days}) + \text{Safety Stock}$$

$$\text{Safety Stock} = \lceil \text{Daily Velocity} \times 3 \text{ Days} \rceil$$

---

## 2. HUMAN-IN-THE-LOOP APPROVAL FLOW

1. System flags SKU crossing `Reorder Point`.
2. `InventoryIntelligenceService` generates draft Purchase Order.
3. Inventory Manager reviews supplier, lead time, and quantity.
4. On approval, Purchase Order is committed to the immutable inventory system.
