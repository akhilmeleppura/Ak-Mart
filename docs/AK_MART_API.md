# AK-Mart 2026 RESTful Storefront & Commerce API Reference

## Base URL
`/api/v1`

## Authentication
Token-based authentication using **Laravel Sanctum**.
Pass the Bearer token in the request header:
`Authorization: Bearer <token>`

---

## Endpoints

### 1. Authentication
- `POST /api/v1/auth/login`: Authenticate and receive Sanctum API token.
- `POST /api/v1/auth/register`: Register new retail customer account.
- `POST /api/v1/auth/logout`: Revoke active Sanctum token.

### 2. Catalog & Products
- `GET /api/v1/products`: Filterable list of published products (supports `?category_id=`, `?search=`, `?sort=`, `?min_price=`, `?max_price=`).
- `GET /api/v1/products/{id}`: Detailed product specifications, variants, gallery images, reviews, and stock availability.
- `GET /api/v1/categories`: Hierarchical category tree.

### 3. Cart & Checkout
- `POST /api/v1/cart/items`: Add or update item in cart session.
- `DELETE /api/v1/cart/items/{id}`: Remove item from cart.
- `POST /api/v1/coupons/validate`: Check promo code discount validity.
- `POST /api/v1/checkout`: Place an atomic order with stock deduction, gift card / store credit application, and loyalty points.

### 4. Gift Cards & Store Credit
- `POST /api/v1/gift-cards/lookup`: Check digital voucher code validity, expiration date, and remaining balance.

### 5. Feeds & Omnichannel Endpoints
- `GET /feeds/google.xml`: Live RSS 2.0 XML catalog for Google Merchant Center.
- `GET /feeds/meta.csv`: Comma-separated product catalog for Facebook and Instagram Shop tagging.
- `GET /feeds/tiktok.json`: Real-time JSON format for TikTok Catalog Partner integration.
