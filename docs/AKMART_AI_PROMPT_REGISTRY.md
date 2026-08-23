# 📜 AKMART AI — PROMPT & TOOL REGISTRY

**Document ID**: AKMART-DOC-AI-PROMPTS-TOOLS-009  
**Date**: August 2026  

---

## 1. PROMPT REGISTRY

- **`COPILOT_ADMIN_V3`**: System prompt governing the Admin Copilot with strict zero-SQL and tool-calling constraints.
- **`SHOPPING_ASSISTANT_V2`**: Customer storefront assistant prompt enforcing strict customer privacy isolation.
- **`MARKETING_CONTENT_V2`**: Multi-format product copywriting and SEO prompt across 4 brand tones.

---

## 2. TOOL REGISTRY & ACCESS LEVELS

| Tool Name | Scope | Risk Classification |
| :--- | :--- | :--- |
| `search_products` | Public | READ |
| `get_store_policy` | Public | READ |
| `get_order_details` | Authenticated (Owner/Admin) | READ |
| `get_sales_summary` | Admin / Finance | READ |
| `get_profit_summary` | Super Admin / Finance | READ |
| `generate_purchase_order` | Inventory Manager | LOW_RISK (Draft) |
| `generate_campaign_draft` | Marketing Manager | LOW_RISK (Draft) |
| `execute_refund` | Finance / Super Admin | HIGH_RISK (Human Approval Required) |
