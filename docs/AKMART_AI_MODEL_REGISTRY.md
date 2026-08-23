# 🤖 AKMART AI — MODEL & PROVIDER REGISTRY

**Document ID**: AKMART-DOC-AI-MODELS-009  
**Date**: August 2026  

---

## 1. SUPPORTED AI MODELS & ROUTING

| Feature Area | Recommended Tier | Primary Model | Offline Fallback Engine |
| :--- | :--- | :--- | :--- |
| **Executive Copilot & BI** | High Capability | Gemini 1.5 Pro / GPT-4o | Deterministic Laravel Services |
| **Storefront Shopping Chat** | Fast / Conversational | Gemini 1.5 Flash / Claude 3.5 Haiku | [`StorefrontAiAssistantController`](file:///c:/xampp/htdocs/Ak-mart/app/Http/Controllers/Storefront/StorefrontAiAssistantController.php) |
| **Semantic Search & Typo** | Local / Deterministic | Regex & Synonym Matrix | [`SemanticSearchService`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/SemanticSearchService.php) |
| **Demand Forecasting & Risk** | Statistical / Math | Mathematical Regression | [`InventoryIntelligenceService`](file:///c:/xampp/htdocs/Ak-mart/app/Services/Ai/InventoryIntelligenceService.php) |
