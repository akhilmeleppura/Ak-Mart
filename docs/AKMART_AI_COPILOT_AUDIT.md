# 🤖 AKMART AI COPILOT — PHASE 1 AUDIT & UPGRADE SPECIFICATION

**Document ID**: AKMART-DOC-COPILOT-AUDIT-001  
**Lead AI Engineer**: Principal AI Architect & Senior Laravel Engineer  
**Scope**: Full Multilingual AI Copilot Extension & Tool Calling Subsystem  
**Date**: August 2026  

---

## 1. EXISTING COPILOT AUDIT

| Subsystem Component | Current Implementation State | Audit Finding | Upgrade Action |
| :--- | :--- | :--- | :--- |
| **Controller Architecture** | [`AICopilotController.php`](file:///c:/xampp/htdocs/Ak-mart/app/Http/Controllers/apps/AICopilotController.php) manages `/ai/copilot` endpoint with Gemini API integration and localized offline responder. | Supported basic revenue, orders, and low-stock numbers with 6 languages. | Extend controller to route natural language dates, comparative queries, and domain-specific tool calls. |
| **Tool Calling Manager** | [`AiToolManager.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/AiToolManager.php) | Provides authoritative sales, inventory, orders, customer 360, and profit reports. | Add product variant tools, sales comparison methods, inventory valuation, and category breakdown tools. |
| **Date Parsing Engine** | Ad-hoc today/yesterday handling. | Missing dynamic natural language date ranges (*this month vs last month*, *this week vs last week*, *last 30/90 days*). | Build safe natural language date parser `AiDateParser` converting relative strings to Carbon date boundaries. |
| **Multilingual Support** | English (EN), Malayalam (ML), Hindi (HI), Arabic (AR RTL), French (FR), German (DE). | Operational with prompt translations. | Maintain 100% 6-language translation for all new tool outputs. |
| **Security & Privacy** | [`PromptSecurityGuard.php`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/PromptSecurityGuard.php) | Blocks jailbreaks and sanitizes PII. | Verify tool-level RBAC and customer isolation. |

---

## 2. TOOL ECOSYSTEM ARCHITECTURE

```text
User Question ("Compare this month with last month")
  │
  ▼
PromptSecurityGuard (Check for injection attacks)
  │
  ▼
Natural Language Intent & Date Parser
  ├── Parse Date Range (This Month: Aug 1-Aug 23 vs Last Month: Jul 1-Jul 31)
  └── Identify Tool: get_sales_comparison
  │
  ▼
Permission Check (Check if user has permission to view sales metrics)
  │
  ▼
AiToolManager -> Eloquent Services
  ├── Current Month Orders & Revenue
  ├── Previous Month Orders & Revenue
  └── Calculate Delta & Percentage Change
  │
  ▼
Multilingual Formatter (EN / ML / HI / AR / FR / DE)
  │
  ▼
Validated Response to User
```

---

## 3. COMPREHENSIVE TOOLS SPECIFICATION

1. **Date & Sales Comparison Tools**:
   - `getSalesComparison($currentPeriod, $previousPeriod, $branchId)`
   - Computes Revenue, Orders, AOV, and Difference (Amount & %).
2. **Product & Review Tools**:
   - `getProductDetails($skuOrId)`: Pricing, variants, stock, ratings.
   - `getCategorySales()`: Revenue distribution across top categories.
3. **Inventory & Valuation Tools**:
   - `getInventoryValuation()`: Total asset valuation ($\sum \text{qty} \times \text{cost/price}$).
   - `getDetailedStockMovements($limit)`: Traceable audit movements from `stock_movements`.
4. **Customer Spend & Loyalty Tools**:
   - `getCustomerOrders($customerId)`: Order history and wallet/loyalty ledger balances.
