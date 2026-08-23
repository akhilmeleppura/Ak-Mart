# 🏗️ AKMART AI — PHASE 2: SEARCH & SHOPPING ASSISTANT ARCHITECTURE

**Document ID**: AKMART-DOC-SEARCH-ARCH-002  
**Lead AI Search Architect**: Principal Search Architect & Senior Laravel Engineer  
**Scope**: Customer Shopping Assistant, Semantic Search, Comparison, Analytics, and Multilingual Discovery  
**Date**: August 2026  

---

## 1. TARGET END-TO-END DATA FLOW

```text
               ┌─────────────────────────────────────────────────────────┐
               │                     CUSTOMER INPUT                      │
               │   "Find me a Samsung phone under ₹20,000 with 5G"       │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │              PROMPT SECURITY & INJECTION GUARD          │
               │   • Anti-Prompt Injection Filter                        │
               │   • Mask Card Numbers & PII                             │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │               SEMANTIC QUERY UNDERSTANDING              │
               │   • Typo Normalizer (samsng -> Samsung)                 │
               │   • Synonym Mapper (mobile -> phone)                    │
               │   • Budget Range Parser (under 20000 -> price <= 20000) │
               │   • Attribute & Spec Extractor (Brand: Samsung, 5G)     │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │               AUTHENTICATION & B2B RESOLVER             │
               │   • Guest / Retail Customer -> Public Active Catalog    │
               │   • B2B Customer -> Check Tier Pricing & Company Rules  │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │             DETERMINISTIC SQL QUERY BUILDER             │
               │   SELECT * FROM products WHERE is_active = 1            │
               │   AND brand LIKE '%Samsung%' AND price <= 20000         │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │               RANKING & AVAILABILITY ENGINE             │
               │   1. In-Stock Items Ranked Higher                       │
               │   2. Exact Brand / Spec Match Score                     │
               │   3. Rating & Featured Flag Ranking                     │
               └────────────────────────────┬────────────────────────────┘
                                            │
                                            ▼
               ┌─────────────────────────────────────────────────────────┐
               │         RESPONSE FORMATTER & ZERO-RESULT ANALYTICS      │
               │   • If Results > 0: Rich Product Cards + Direct Links   │
               │   • If Out of Stock: Recommend Close Alternatives       │
               │   • If 0 Results: Log to Search Analytics for Merch    │
               └─────────────────────────────────────────────────────────┘
```

---

## 2. SYNONYM & COMMERCE TERMINOLOGY MAP

```php
protected static array $synonyms = [
    'mobile'       => 'phone',
    'cell phone'   => 'phone',
    'smartphone'   => 'phone',
    'trainers'     => 'shoes',
    'sneakers'     => 'shoes',
    'tv'           => 'television',
    'fridge'       => 'refrigerator',
    'earphones'    => 'headphones',
    'earbuds'      => 'headphones',
    'tshirt'       => 'shirt',
    'tee'          => 'shirt',
];
```

---

## 3. ZERO-RESULT & SEARCH ANALYTICS MODEL

Every search query is recorded with:
- `query`: Raw search query string.
- `cleaned_query`: Normalized query with typos/synonyms handled.
- `results_count`: Number of products returned.
- `is_zero_result`: Boolean flag (true if results = 0).
- `user_id`: Optional authenticated user ID.
- `locale`: Active language (`en`, `ml`, `hi`, `ar`, `fr`, `de`).
- `created_at`: Timestamp.

This allows administrators to identify unmet customer demand, missing stock, and catalog gaps directly from the admin dashboard.

---

## 4. PRODUCT COMPARISON MATRIX SCHEMA

When comparing Product A and Product B:

| Feature | Product A | Product B |
| :--- | :--- | :--- |
| **Brand** | Apple | Samsung |
| **Price** | $999.00 | $799.00 |
| **Rating** | 4.8 ★ (120 reviews) | 4.6 ★ (95 reviews) |
| **Availability** | In Stock (25 units) | In Stock (14 units) |
| **Warranty** | 1 Year Official | 1 Year Official |
| **Key Specs** | 256GB Storage, A17 Pro | 256GB Storage, Snapdragon 8 |

*If any specification is not recorded in the database, the system outputs `"Not specified"` to prevent AI hallucination.*
