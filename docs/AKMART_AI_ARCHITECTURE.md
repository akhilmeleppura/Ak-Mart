# 🤖 AKMART — MULTI-LAYER AI COMMERCE ARCHITECTURE & GUARDRAIL SPECIFICATION

**Document ID**: AKMART-DOC-AI-004  
**Architecture**: Provider-Agnostic Multi-Model AI Engine + Deterministic Fallbacks  
**Date**: August 2026  

---

## 1. AI SUBSYSTEM ARCHITECTURE

```text
               ┌─────────────────────────────────────────────────────────┐
               │                     USER INTERACTION                    │
               │        (Customer Storefront / Admin Copilot / API)      │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │             AI GATEWAY & INTENT RESOLVER                │
               │   • Intent Classification & Prompt Sanitization         │
               │   • RBAC Permission Check (e.g. `ai.use`, `ai.approve`) │
               │   • Multi-lingual Prompt Translation (6 Languages)      │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │              SECURE CONTROLLED TOOL CALLS               │
               │  ┌─────────────────────────┐ ┌────────────────────────┐ │
               │  │ search_products()       │ │ check_inventory()      │ │
               │  │ get_order_status()      │ │ calculate_discount()   │ │
               │  │ get_sales_report()      │ │ get_customer_segment() │ │
               │  │ create_campaign_draft() │ │ generate_reorder_rec() │ │
               │  └─────────────────────────┘ └────────────────────────┘ │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │              DETERMINISTIC VALIDATION LAYER             │
               │   • Real-Time Catalog Cross-Check (No Fake SKUs/Prices) │
               │   • Margin & Discount Safety Bounds Validation          │
               │   • Admin Approval Workflow for Modifying Actions       │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │            ADAPTER LAYER / MULTI-MODEL ROUTER           │
               │  ┌──────────────┐ ┌──────────────┐ ┌──────────────────┐ │
               │  │ Gemini API   │ │ OpenAI API   │ │ Offline Fallback │ │
               │  │ (Analytics)  │ │ (Content)    │ │ (Deterministic)  │ │
               │  └──────────────┘ └──────────────┘ └──────────────────┘ │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │             AUDIT LOGGING & IMMUTABLE TRAIL             │
               │   • Log Request, Prompt, Model Used, Response & Status  │
               └─────────────────────────────────────────────────────────┘
```

---

## 2. KEY AI MODULES & CAPABILITIES

### 1. AI Admin Business Copilot ("Ask Your Store")
- **Capability**: Natural-language conversational business intelligence.
- **Example**: Admin asks *"Why did revenue drop this weekend compared to last week?"*
- **Action**: Queries order statistics, cart conversion rates, payment failure logs, and inventory stockouts; synthesizes root cause with real numbers; cites exact evidence.

### 2. AI Daily Business Brief
- **Capability**: Executive morning summary delivered directly onto the Admin Dashboard.
- **Metrics**: Yesterday's Gross Sales, Units Sold, Net Margin, Top 3 Moving Products, Critical Stockout Warnings (items with < 5 days runway), and Win-Back Opportunities.

### 3. AI Shopping Assistant & Semantic Search
- **Capability**: Customer-facing conversational search on the Storefront.
- **Example**: Shopper types *"Show me gluten-free breakfast items under ₹500"*.
- **Execution**: The AI extracts constraints (`category=Breakfast`, `tag=Gluten-Free`, `price_max=500`), constructs a SQL query, and returns live products with active stock.

### 4. AI Product Content Generator
- **Capability**: 1-click SEO-optimized Title, Bullet Features, Rich Description, and Meta tags generation for single products or bulk imports.
- **Multi-lingual**: Generates copy in English, Malayalam, Hindi, Arabic, French, and German.

### 5. AI Predictive Demand Forecasting & Stockout Engine
- **Capability**: Calculates sales velocity ($V = \frac{\text{Sales 30 Days}}{30}$) and days of inventory remaining ($D = \frac{\text{Current Stock}}{V}$).
- **Recommendations**: Flags SKUs where $D < \text{Lead Time}$, generating automated purchase order reorder recommendations with economic order quantities.

### 6. AI Anomaly & Fraud Risk Engine
- **Capability**: Evaluates order risk score (0–100) based on velocity, multiple failed attempts, COD order history, and unusual address formats.
- **Action**: Flags high-risk orders (>75) for manual manager approval before fulfillment.

---

## 3. ZERO-HALLUCINATION & FINANCIAL SAFETY LAWS

1. **No Invented Products**: The AI cannot recommend or present any product ID, SKU, title, or image not verified against the `products` table.
2. **No Invented Pricing or Discounts**: All final pricing, coupons, and wallet deductions are calculated deterministically by Laravel backend services.
3. **Admin Preview & Approval**: AI-generated price changes, email campaigns, or reorder purchase orders are saved in `draft` status until explicitly approved by an authorized administrator.
4. **Offline Resilience**: If external LLM APIs are unreachable, the system transparently falls back to deterministic rule-based algorithms without throwing 500 errors.
