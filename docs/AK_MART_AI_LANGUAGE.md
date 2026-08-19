# AK-MART AI Copilot Multilingual Engine

## 1. Overview
The AK-Mart AI Copilot automatically aligns its greeting, UI placeholders, generative prompts, and deterministic fallback responses with the user's active locale (`en`, `ml`, `hi`, `ar`, `fr`, `de`).

---

## 2. Dynamic Language Injection in Gemini Prompts

When Google Gemini Generative AI is active, the system prompt dynamically appends language-specific instructions:

| Active Locale | System Instruction Injected into Gemini |
| :---: | :--- |
| **`ml`** | `Always answer in Malayalam language (മലയാളത്തിൽ മറുപടി നൽകുക).` |
| **`hi`** | `Always answer in Hindi language (हिन्दी में उत्तर दें).` |
| **`ar`** | `Always answer in Arabic language (يرجى الرد باللغة العربية).` |
| **`fr`** | `Always answer in French language (Répondez en français).` |
| **`de`** | `Always answer in German language (Antworten Sie auf Deutsch).` |
| **`en`** | `Answer in English.` |

---

## 3. Intelligent Deterministic Fallback Engine

When operating offline or without an API key, the business engine parses keywords across all supported languages (e.g. `stock`, `inventory`, `സ്റ്റോക്ക്`, `स्टॉक`, `المخزون`, `orders`, `sales`, `വിൽപ്പന`, `المبيعات`) and outputs real-time branch and catalog metrics in natural, grammatically correct phrasing for each language.
