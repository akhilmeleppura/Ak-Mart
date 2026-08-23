# 🌐 AKMART — RESTFUL API & WEBHOOK PLATFORM SPECIFICATION

**Document ID**: AKMART-DOC-API-006  
**API Version**: `v1` (Semantic JSON API)  
**Authentication**: Laravel Sanctum Bearer Token / Session Guard  
**Date**: August 2026  

---

## 1. RESTFUL ENDPOINTS OVERVIEW

All API v1 responses adhere to the standard JSON payload structure:

```json
{
  "success": true,
  "status": "success",
  "data": {},
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

### Endpoints:

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/products` | Get paginated product catalog with search, category filters | No |
| `GET` | `/api/v1/products/{id}` | Get product details with real-time available stock & variants | No |
| `GET` | `/api/v1/categories` | Get active category hierarchy with product counts | No |
| `GET` | `/api/v1/inventory/status`| Check live real-time stock availability for a SKU/ID | No |
| `POST`| `/api/v1/orders` | Place storefront/guest order with atomic stock deduction | No / Optional |
| `GET` | `/api/v1/orders/{orderNumber}` | Retrieve order status, item breakdown, and tracking status | No / Token |
| `POST`| `/api/v1/coupons/validate` | Validate coupon code against cart amount and restrictions | No |
| `GET` | `/api/user` | Retrieve authenticated user profile, roles, and branch info | Yes (Sanctum) |

---

## 2. WEBHOOK SUBSYSTEM & EVENT DISPATCH

AKMart includes incoming and outgoing webhook support:

### Incoming Webhook Endpoints:
- `POST /api/payment/webhook`: Razorpay / Stripe / Cashfree signature-verified transaction webhooks with idempotency locking.
- `GET /api/whatsapp/webhook`: Meta WhatsApp Cloud API hub challenge verification.
- `POST /api/whatsapp/webhook`: Incoming WhatsApp customer messages and delivery receipt status callbacks with HMAC validation.

### Outgoing Webhook Subscriptions:
The `WebhookDispatcher` dispatches events asynchronously:
- `order.created`, `order.status_changed`, `stock.low`, `payment.completed`, `return.requested`.
- Configurable signature signing (`X-AKMart-Signature: sha256`), auto-retry with exponential backoff, and full delivery logs in `webhook_logs`.
