# AK-Mart 2026 Enterprise Integrations Architecture

## 1. Omnichannel Marketplace Product Feeds

| Channel | Format | Endpoint | Specification & Capabilities |
| :--- | :---: | :--- | :--- |
| **Google Merchant Center** | XML / RSS 2.0 | `/feeds/google.xml` | RFC compliant XML feed with `<g:id>`, `<g:title>`, `<g:description>`, `<g:price>`, `<g:availability>`, `<g:image_link>`, `<g:brand>`, `<g:gtin>`, `<g:mpn>`. |
| **Meta Commerce / Instagram** | CSV | `/feeds/meta.csv` | Comma-separated product catalog supporting Dynamic Product Ads (DPA) and Instagram Shopping tagging. |
| **TikTok Shop** | JSON | `/feeds/tiktok.json` | Real-time JSON structured format for TikTok Catalog Partner API and video showcase links. |

---

## 2. Outbound Event Webhooks Engine

| Event Key | Trigger Condition | Payload Contract |
| :--- | :--- | :--- |
| `order.created` | New order placed via Storefront, POS, or API | Order Number, User ID, Line Items, Total Amount, Payment Method, Shipping Address |
| `order.paid` | Payment confirmed by gateway or cash drawer | Order ID, Amount, Payment Gateway Reference, Paid Timestamp |
| `order.shipped` | Fulfillment order dispatched with carrier tracking | Fulfillment #, Carrier, Tracking Number, Tracking URL, Line Items |
| `product.updated` | Product title, price, or specifications modified | Product ID, SKU, Name, Price, New Attributes |
| `inventory.updated` | Live stock quantity changed across branch or warehouse | Product ID, Warehouse ID, Before Qty, After Qty, Delta, Reason |
| `customer.created` | New retail customer or B2B buyer account created | Customer ID, Name, Email, Phone, Segment |

### Webhook Security
- Outbound requests include header `X-AKMart-Signature: <hmac_sha256_hash>` generated using the subscription's secret key.
- Endpoints timeout safely after 5 seconds to prevent request queuing delays.

---

## 3. Payment Gateway Webhook Handling
- **Incoming Payment Webhook**: `/payment/webhook` receives instant IPNs/webhooks from Stripe, PayPal, Razorpay, or custom gateways to update `orders.payment_status = 'paid'` and trigger order confirmation automation.

---

## 4. AI Copilot & Content Generator Engine
- **Provider**: Google Gemini 1.5 Flash API with deterministic offline fallback.
- **Service Interfaces**:
  - `AIProductToolsController@generateContent`: Automated marketing copy, bullet points, meta tags.
  - `AIProductToolsController@optimizeProduct`: 0-100 product completeness evaluation.
  - `AIProductToolsController@extractAttributes`: Regular expression & LLM spec attribute parsing.
  - `AIProductToolsController@suggestCategory`: Semantic store category classification.
