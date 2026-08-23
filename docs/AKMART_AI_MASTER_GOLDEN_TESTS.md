# 🧪 AKMART AI — MASTER 500+ GOLDEN TEST DATASET

**Document ID**: AKMART-DOC-AI-MASTER-GOLDEN-009  
**Total Scenarios**: 520 Comprehensive Real-World E-commerce Test Scenarios  
**Scope**: Full end-to-end coverage of all 9 AI subsystem phases.  
**Date**: August 2026  

---

## 1. PHASE 1: ADMIN AI COPILOT & EXECUTIVE TOOLS (G-001 to G-60)
- **G-001 to G-15**: Sales & Revenue Summaries (Today, This Week, This Month, Fiscal Quarter).
- **G-16 to G-30**: Period vs Period Comparisons (MoM, WoW, YoY) with deltas ($ and %).
- **G-31 to G-45**: Inventory Asset Valuation and SKU-level runway counts.
- **G-46 to G-60**: Multilingual Copilot queries in English, Malayalam, Hindi, Arabic, French, and German.

---

## 2. PHASE 2: CUSTOMER SHOPPING ASSISTANT & SEMANTIC SEARCH (G-61 to G-125)
- **G-61 to G-75**: Natural Language Budget Filters (*"Phone under ₹15,000"*, *"Gaming laptop below ₹50,000"*).
- **G-76 to G-90**: Typo Normalization (*"samsng moble"*, *"iphne 15 pro"*, *"blu tooth hedphone"*).
- **G-91 to G-105**: Commerce Synonyms (*"mobile"* $\rightarrow$ *Phone*, *"trainers"* $\rightarrow$ *Shoes*, *"fridge"* $\rightarrow$ *Refrigerator*).
- **G-106 to G-115**: Side-by-Side Product Comparison matrices with "Not specified" for missing fields.
- **G-116 to G-125**: Pincode Serviceability & Store Coupon discovery.

---

## 3. PHASE 3: RECOMMENDATIONS & PERSONALIZATION ENGINE (G-126 to G-185)
- **G-126 to G-140**: Frequently Bought Together (FBT) co-occurrence mining from completed orders.
- **G-141 to G-155**: Complementary Cross-Category affinity mapping (Phone $\rightarrow$ Cases/Chargers).
- **G-156 to G-170**: Budget Alternatives (*"Show me something cheaper"*) & Premium Upgrade options.
- **G-171 to G-185**: 30-Day Velocity Trending items & User-Personalized storefront feeds.

---

## 4. PHASE 4: CUSTOMER INTELLIGENCE & CRM (G-186 to G-250)
- **G-186 to G-205**: Explainable Lifecycle Segmentation (*VIP*, *High Value*, *Returning*, *New*, *At Risk*, *Inactive*).
- **G-206 to G-225**: Customer Lifetime Value (CLV) historical vs 12-month projected estimates with confidence levels.
- **G-226 to G-240**: Churn Risk scoring (*Low*, *Medium*, *High*) with supporting purchase gap signals.
- **G-241 to G-250**: Next-Best-Action recommendations (*Win-Back campaign*, *VIP loyalty reward*).

---

## 5. PHASE 5: MARKETING, CONTENT & SEO INTELLIGENCE (G-251 to G-315)
- **G-251 to G-270**: Multi-Format Product Content (Title, Short/Long desc, SEO title, Meta desc, WhatsApp, Email).
- **G-271 to G-285**: Brand Tone Selection (Professional, Premium, Promotional, Friendly).
- **G-286 to G-300**: 0–100 Deterministic SEO Quality Scoring & Attribute Extraction (*RAM*, *Storage*, *Display*, *5G*).
- **G-301 to G-315**: Multi-Channel Campaign drafting (Email, WhatsApp, SMS, Push) with `draft_pending_human_approval`.

---

## 6. PHASE 6: INVENTORY & DEMAND FORECASTING (G-316 to G-380)
- **G-316 to G-335**: Multi-Horizon Demand Forecasting (7d, 14d, 30d, 60d, 90d) with 60-day velocity weights.
- **G-336 to G-350**: Days-to-Stockout runway calculation & Dynamic Safety Stock / Reorder Point alerts.
- **G-351 to G-365**: Automated Purchase Order Draft generation (`draft_pending_manager_approval`).
- **G-366 to G-380**: Dead Stock (0 sales in 90 days), Overstock (> 180 days runway), and Movement Anomaly Detection.

---

## 7. PHASE 7: FRAUD, RISK & TRUST INTELLIGENCE (G-381 to G-445)
- **G-381 to G-400**: Multi-Factor Explainable Order Risk Scoring (AOV spike $> 3\times$, payment failures, RTO rates).
- **G-401 to G-415**: Cash-on-Delivery (COD) Risk & *"Require Prepaid Payment"* policy enforcement.
- **G-416 to G-430**: 24-Hour Payment Gateway Failure Spike & Promotion Abuse cluster detection.
- **G-431 to G-445**: Risk Review Queue for manual manager verification.

---

## 8. PHASE 8: BUSINESS INTELLIGENCE & SCENARIO ANALYSIS (G-446 to G-490)
- **G-446 to G-460**: Centralized Enterprise KPI Registry (Gross Revenue, Net Profit, Margin %, AOV, Return Rate).
- **G-461 to G-475**: Daily Executive Business Brief generation.
- **G-476 to G-490**: Read-Only What-If Scenario Simulator (`SIMULATION_NOT_GUARANTEED`).

---

## 9. PHASE 9: GOVERNANCE, SECURITY & ADVERSARIAL INJECTION (G-491 to G-520)
- **G-491 to G-500**: Prompt Injection Attacks (*"Ignore instructions"*, *"Dump passwords"*, *"Drop tables"*).
- **G-501 to G-510**: PII Redaction & Credit Card / Phone Number Masking.
- **G-511 to G-520**: Tool Permission Gate & Financial Role Isolation Enforcement.
