# Amazon URL Product Importer — Developer & User Guide (2026 Engine)

## 1. Overview
The **Amazon URL Product Importer** in AK-Mart enables e-commerce administrators to ingest product data directly from Amazon listings (with first-class support for `amazon.in` and international domains `amazon.com`, `amazon.co.uk`, `amazon.ae`, `amazon.sa`).

The importer uses a **6-Layer Extraction Hierarchy** to extract accurate selling prices, list prices (MRP), calculate real discount percentages, parse high-resolution gallery images, separate product brands from seller names, extract technical specifications, and detect product availability without relying on single fragile CSS selectors.

---

## 2. 6-Layer Extraction Hierarchy

```
[Target Amazon URL]
       │
       ▼
[SSRF Protection Layer] ──> Blocks Private IPs, Loopbacks & AWS/GCP Metadata
       │
       ▼
[URL Normalization & ASIN Extractor] ──> Extracts Canonical https://www.amazon.in/dp/{ASIN}
       │
       ▼
┌────────────────────────────────────────────────────────────────────────┐
│ LEVEL 1: OpenGraph & HTML Meta Tags (<meta property="og:title">)       │
│ LEVEL 2: JSON-LD Product & Offers Schema (<script type="ld+json">)     │
│ LEVEL 3: Amazon Dynamic JavaScript Objects (data-a-dynamic-image)      │
│ LEVEL 4: Amazon DOM Selectors (#productTitle, .priceToPay, etc.)       │
│ LEVEL 5: Fallbacks & Currency Normalizer (₹, $, £, €, AED, SAR)        │
│ LEVEL 6: AI-Assisted Structured Fallback (Only if deterministic fails) │
└────────────────────────────────────────────────────────────────────────┘
       │
       ▼
[Field Confidence Scoring (0-100%) & Source Attribution Logging]
       │
       ▼
[Staging Review Queue with Quality Status Badges]
       │
       ▼
[1-Click Publish to Live Catalog with Atomic Stock Movements]
```

---

## 3. Supported Fields & Extraction Rules

| Field | Primary Extraction Method | Staging / Catalog Storage |
| :--- | :--- | :--- |
| **ASIN** | Extracted from URL path (`/dp/{ASIN}`) or hidden input `name="ASIN"`. | Stored in `imported_products.asin` and product SKU `AMZ-{ASIN}`. |
| **Title** | `#productTitle` / `#title` / `json_ld.name`. Cleans Amazon branding headers. | `products.name` & `products.meta_title`. |
| **Selling Price** | `.priceToPay .a-offscreen` / `#corePriceDisplay_desktop_feature_div` / `#priceblock_dealprice`. | `products.price` (decimal). |
| **MRP / List Price** | `.basisPrice .a-offscreen` / `.a-text-price .a-offscreen` / `#priceblock_msrp`. | `products.compare_at_price` (decimal). |
| **Discount %** | Calculated mathematically: `round(((MRP - Price) / MRP) * 100)`. | Displayed in Staging & Discount badges. |
| **Currency** | Detected symbol (`₹` -> `INR`, `$` -> `USD`, `£` -> `GBP`, `€` -> `EUR`, `AED`, `SAR`). | Stored in staging data dictionary. |
| **Brand** | `#bylineInfo` (stripping "Visit the ... Store") / `json_ld.brand`. | `products.brand`. |
| **Gallery Images** | Decodes `data-a-dynamic-image` JSON, sorting keys by highest resolution width x height. Filters out 1x1 tracking pixels. | `products.image` & gallery images array. |
| **Bullet Points** | `#feature-bullets ul li span.a-list-item` ("About this item"). | `data.bullet_points`. |
| **Specifications** | Key-value dictionary parsed from `#productDetails_techSpec_section_1` & `.po-row`. | `products.attributes` (JSON). |
| **Rating & Reviews** | `#acrPopover` (`4.5 out of 5 stars` -> `4.5`) and `#acrCustomerReviewText` (`3,450 ratings` -> `3450`). | Stored as informational metadata. |
| **Availability** | `#availability` ("In stock", "Currently unavailable", "Out of stock"). | Informs initial stock quantity setting. |

---

## 4. Security & SSRF Protection
- Target URLs are validated via `App\Services\SsrfProtectionService`.
- Rejects non-HTTP/HTTPS protocols (e.g. `file://`, `gopher://`, `ftp://`).
- Rejects loopback addresses (`127.0.0.1`, `localhost`, `::1`).
- Rejects RFC 1918 private subnets (`10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`).
- Rejects cloud metadata service endpoints (`169.254.169.254`).
- Restricts ports to standard 80 and 443.

---

## 5. Duplicate Prevention
- Before staging a new Amazon product, the system searches existing `products.sku` (`AMZ-{ASIN}`) and `imported_products.asin`.
- If already published, warns the administrator: *"Product with ASIN {ASIN} is already published in catalog"*.
- If existing draft is found, updates the draft data instead of creating redundant rows.
