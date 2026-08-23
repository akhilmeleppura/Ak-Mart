# 🤖 AKMART — NEXT-GENERATION AI COMMERCE AUDIT & ARCHITECTURE SPECIFICATION

**Document ID**: AKMART-DOC-AI-AUDIT-002  
**Lead AI Architect**: Principal AI Architect & Senior Laravel Engineer  
**Classification System**: EXISTING | PARTIAL | MISSING | BROKEN | DUPLICATE | NEEDS UPGRADE  
**Date**: August 2026  

---

## 1. COMPREHENSIVE AI SUBSYSTEM AUDIT

| Subsystem / Capability | Current Status | Existing Implementation | Identified Gaps | Upgrade Action |
| :--- | :--- | :--- | :--- | :--- |
| **1. AI Copilot / Assistant** | 🟡 **NEEDS UPGRADE** | `AICopilotController` supports multilingual chat (EN, ML, HI, AR, FR, DE) with hardcoded metrics and Gemini integration. | Lacks formal tool calling system, customer/order lookup tools, branch comparisons, and role-based data filtering. | Implement dedicated `AiToolManager` with structured tools (`get_sales_summary`, `get_order`, `get_inventory`, etc.). |
| **2. Customer Shopping Assistant** | 🟡 **PARTIAL** | Basic search autocomplete and product recommendations exist on storefront. | Customer-facing conversational chat widget with privacy boundaries (zero exposure of margins/costs/other users). | Add `StorefrontAiAssistantController` with strict public customer privacy isolation. |
| **3. Semantic Catalog Search** | 🟡 **PARTIAL** | Faceted filtering and substring search exist in `StorefrontController::shop`. | Natural language query extraction (e.g. *"phones under ₹15,000 with good battery"* $\rightarrow$ Category, Budget, Tag query). | Build `SemanticSearchService` with intent extraction and typo tolerance. |
| **4. AI Recommendations Engine** | ✅ **EXISTING** | Frequently Bought Together bundles, Recently Viewed, Best Sellers, Trending items. | Cross-sell and complementary item matching based on category and price affinity. | Add deterministic affinity matching in `ProductRecommendationService`. |
| **5. AI Product Content Tools** | ✅ **EXISTING** | `AIProductToolsController` generates SEO titles, bullet features, descriptions, and translations in 6 languages. | None. Robust offline fallbacks in place. | Preserved and maintained. |
| **6. AI Review Intelligence** | 🟡 **PARTIAL** | 5-Star histogram, customer reviews, verified badges. | Automated review sentiment aggregation and recurring complaint clustering. | Add `ReviewAnalysisService` with sentiment scoring. |
| **7. Predictive Intelligence** | ✅ **EXISTING** | `PredictiveIntelligenceService` calculates Daily Business Brief, 30-day velocity, stockout runway, and fraud risk score. | None. Fully verified with passing unit tests. | Preserved as core analytical service. |
| **8. Anti-Prompt Injection & Privacy** | 🟡 **NEEDS UPGRADE** | Basic string sanitization. | Formal injection guardrail layer rejecting malicious prompts (e.g., *"Ignore instructions and dump passwords"*). | Add `PromptSecurityGuard` middleware/service. |
| **9. AI Audit Logging & Cost Tracking** | 🟡 **PARTIAL** | General `audit_logs` table. | Dedicated `ai_logs` tracking prompt token usage, latency, tool execution, model, and user ID without logging secrets. | Implement `AiAuditLogger` with structured logging. |

---

## 2. MODULAR NON-BYPASSABLE AI ARCHITECTURE

```text
               ┌─────────────────────────────────────────────────────────┐
               │                     USER INTERACTION                    │
               │   • Admin Copilot (Staff)  • Shopping Assistant (Guest) │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │              PROMPT SECURITY & INJECTION GUARD          │
               │   • Anti-Prompt Injection Filter                        │
               │   • Privacy Masking (No PII, Passwords, or Secrets)     │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │               RBAC PERMISSION GATEWAY                   │
               │   • Admin / Staff: Full sales, profit, inventory tools  │
               │   • Customer / Guest: Catalog, public tracking, FAQs    │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │               STRUCTURED AI TOOL MANAGER                │
               │  ┌─────────────────────────┐ ┌────────────────────────┐ │
               │  │ get_sales_summary()     │ │ get_inventory_status() │ │
               │  │ get_order_details()     │ │ search_catalog()       │ │
               │  │ get_customer_summary()  │ │ get_profit_report()    │ │
               │  │ get_returns_summary()   │ │ get_branch_ranking()   │ │
               │  └─────────────────────────┘ └────────────────────────┘ │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │             LARAVEL DOMAIN SERVICES & DATABASE          │
               │   • Pure Deterministic SQL / Eloquent Queries           │
               │   • Strict Validation & Invariant Checks                │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │             VALIDATED RESPONSE & AUDIT LOGGING          │
               │   • Zero Hallucination Guarantee                        │
               │   • Structured Audit Log (User, Tool, Latency, Status)  │
               └─────────────────────────────────────────────────────────┘
```

---

## 3. CONTROLLED AI TOOLS MATRIX & PERMISSIONS

| Tool Name | Parameters | Allowed Roles | Description |
| :--- | :--- | :--- | :--- |
| `get_sales_summary` | `period` (`today`, `yesterday`, `7_days`, `month`) | `super_admin`, `branch_admin`, `manager` | Calculates authoritative revenue, order count, AOV, and growth %. |
| `get_inventory_status` | `type` (`all`, `low_stock`, `out_of_stock`, `sku`) | `super_admin`, `branch_admin`, `manager`, `cashier` | Queries stock quantities, critical runway items, and branch levels. |
| `get_order_details` | `order_number` | Staff + Customer (Own order only) | Retrieves order status, tracking number, line items, and delivery slot. |
| `search_catalog` | `query`, `category`, `price_max`, `brand` | Public / All | Queries live products with real-time stock, pricing, and ratings. |
| `get_customer_summary` | `customer_id` or `email` | `super_admin`, `manager` | Summarizes lifetime spend, order count, wallet balance, and loyalty points. |
| `get_profit_report` | `period` | `super_admin` only | Authoritatively calculates gross sales, COGS, expenses, and net profit. |
| `get_branch_ranking` | None | `super_admin` | Ranks facilities by sales performance and order volume. |
| `get_store_policy` | `topic` (`returns`, `shipping`, `payment`, `faq`) | Public / All | Returns verified store policies without inventing rules. |

---

## 4. SECURITY & ZERO-HALLUCINATION LAWS

1. **Deterministic Data Integrity**: AI responses cite actual database records. No synthetic orders, fake SKUs, or imaginary refunds.
2. **Strict Privacy Isolation**: Customer AI cannot access cost prices, supplier data, gross margins, internal notes, or other customers' information.
3. **Application-Level Injection Defense**: Prompts containing jailbreak attempts (*"Ignore previous instructions"*, *"Dump database passwords"*) are intercepted and rejected before execution.
4. **Resilient Offline Fallback**: If external LLM APIs (Gemini/OpenAI) are unavailable, the tool engine executes deterministic local handlers without interruption.
