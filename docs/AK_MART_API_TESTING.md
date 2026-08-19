# AK-Mart API & AJAX Endpoint Audit

## API Endpoint Matrix

| Method | Endpoint Route | Controller Action | Auth / Protection | Request Payload | Response Format | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :---: |
| `POST` | `/app/vendor/pos/checkout` | `PosController@checkout` | `auth:sanctum` / `web` | `items`, `payment_method`, `total` | `{"success": true, "order_id": N}` | PASSED |
| `POST` | `/app/ai/copilot` | `AICopilotController@chat` | `auth:sanctum` / `web` | `message` | `{"reply": "..."}` | PASSED |
| `GET` | `/app/global-search` | `GlobalSearchController@search` | `auth:sanctum` / `web` | `query` | `{"html": "..."}` | PASSED |
| `POST` | `/app/ecommerce/coupons` | `EcommerceCouponController@store` | `auth:sanctum` / `web` | `code`, `type`, `value`, `usage_limit` | `{"success": "..."}` | PASSED |
| `POST` | `/app/ecommerce/coupon/bulk-generate` | `EcommerceCouponController@bulkGenerate` | `auth:sanctum` / `web` | `count`, `type`, `value` | `{"success": true, "message": "..."}` | PASSED |
| `POST` | `/app/notifications/mark-all` | `SystemNotificationController@markAllAsRead` | `auth:sanctum` / `web` | None | Redirect / JSON | PASSED |
