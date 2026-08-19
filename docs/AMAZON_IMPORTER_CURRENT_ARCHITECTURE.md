# Amazon URL Product Importer — Current Architecture & Audit Report

## 1. Executive Summary & Existing Flow
The initial URL Product Importer in AK-Mart used a single generic helper method `extractStructuredProductData()` inside `ProductImportController.php`. While functional for basic Schema.org web pages, it exhibited notable limitations when parsing dynamic Amazon listings (`amazon.in`, `amazon.com`), leading to inaccurate prices, missed gallery images, confused seller vs brand entities, and missing technical specifications.

---

## 2. Existing Architecture Components

### Controller
- **`App\Http\Controllers\apps\ProductImportController`**:
  - `index()`: Lists imported drafts from `imported_products` table and category list.
  - `parseUrl(Request $request)`: Performs basic `Http::timeout(10)->get($url)` and delegates to `extractStructuredProductData()`.
  - `review($id)`: Displays staging review form.
  - `publish(Request $request, $id)`: Validates and creates a live `Product` in `products` table, recording initial `StockMovement`.
  - `destroy($id)`: Deletes staging draft.

### Database Schema
- **`imported_products` table**:
  - `id` (`bigint unsigned`)
  - `source_type` (`enum('file', 'url')`)
  - `source_url` (`text`)
  - `data` (`json`)
  - `status` (`varchar(50)`: `draft`, `reviewed`, `published`, `discarded`)
  - `user_id` (`bigint unsigned`, nullable)
  - `timestamps`

---

## 3. Discovered Limitations of Previous Implementation

| Domain | Previous Limitation | Root Cause |
| :--- | :--- | :--- |
| **URL Normalization** | Long URLs with referral tags (`?ref=...`, `?psc=1`) imported as-is. | Lack of Amazon URL parser and ASIN canonicalization. |
| **Price Extraction** | Fallback to hardcoded `$49.99` if JSON-LD offers were missing; could confuse delivery fee or EMI with selling price. | No DOM selector tree for Amazon currency elements (`.a-price .a-offscreen`, `#corePriceDisplay_desktop_feature_div`). |
| **MRP & Discounts** | MRP was artificially calculated as `price * 1.25` rather than extracting actual strikethrough list price. | No parser for Amazon `.basisPrice` or `.a-text-price`. |
| **Image Gallery** | Only extracted single main image (`og:image`), missing high-res gallery variations. | Did not parse Amazon `data-a-dynamic-image` JSON attributes or `#altImages`. |
| **Brand vs Seller** | Defaulted to "Generic" or confused seller store name with product brand. | Missing Amazon `#bylineInfo` and `#productOverview_feature_div` extractor. |
| **Features & Specs** | Missing bullet points and technical specifications table (`#productDetails_techSpec_section_1`). | Stored single plain description string. |
| **Security & SSRF** | No IP validation on target URLs before fetching HTTP responses. | Missing SSRF protection layer blocking loopback and private subnets. |
| **Confidence & Source** | No field-level confidence score or source attribution for debugging. | Single monolithic parser returning unranked dictionary. |

---

## 4. Upgrade Roadmap
1. Deploy `SsrfProtectionService` to protect against internal network SSRF attacks.
2. Deploy modular `AmazonProductExtractor` implementing the 6-layer extraction hierarchy.
3. Add ASIN detection, canonical URL normalization, and anti-duplicate prevention.
4. Enhance `ImportedProduct` schema to store confidence scores, extraction sources, and ASIN.
5. Upgrade Admin UI to display quality badges, confidence bars, and full specifications editor.
