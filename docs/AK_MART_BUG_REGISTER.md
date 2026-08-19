# AK-Mart Quality Assurance & Bug Register (2026 Audit)

Comprehensive record of all bugs, defects, missing handlers, and edge cases discovered during the systematic 51-phase quality assurance audit, along with their resolution and regression tests.

---

## Bug Ledger

### BUG-001: Missing `GiftCard::deduct()` Method
- **Module**: Gift Cards & Vouchers
- **Severity**: HIGH
- **Steps to Reproduce**: Call `$giftCard->deduct(50.00)` on an active gift card model.
- **Expected Result**: Gift card balance is decremented and returns boolean `true`, or `false` if balance is insufficient.
- **Actual Result**: `BadMethodCallException: Call to undefined method App\Models\GiftCard::deduct()`.
- **Root Cause**: Method was absent on the `GiftCard` Eloquent model.
- **Fix**: Implemented `deduct(float $amount): bool` validating `$this->isValid()` and balance sufficiency before decrementing `$this->current_balance`.
- **Files Changed**: `app/Models/GiftCard.php`
- **Test Result**: Verified in `CommerceRegressionAuditTest::test_gift_card_and_store_credit_balance_constraints`.

---

### BUG-002: Missing IDOR Authorization Check in Storefront Order Endpoint
- **Module**: RESTful API v1 / Customer Portal
- **Severity**: HIGH
- **Steps to Reproduce**: User B queries `/api/v1/orders/{orderNumber}` for an order belonging to User A.
- **Expected Result**: HTTP 403 Forbidden with `Unauthorized access to this order.` message.
- **Actual Result**: Order data returned with HTTP 200 without checking `order.user_id === auth()->id()`.
- **Root Cause**: Route did not compare the order owner against the authenticated user when a user session/token is present.
- **Fix**: Added ownership validation in `StorefrontController::getOrder()` with universal bypass for Supreme Admins.
- **Files Changed**: `app/Http/Controllers/api/v1/StorefrontController.php`
- **Test Result**: Verified in `CommerceRegressionAuditTest::test_customer_idor_order_protection`.

---

### BUG-003: Unregistered Suppliers & Purchase Orders Web Routes
- **Module**: Purchases & Suppliers
- **Severity**: HIGH
- **Steps to Reproduce**: Access `/purchases` or `/suppliers` via browser or HTTP POST.
- **Expected Result**: Purchase order listing / supplier management view rendered.
- **Actual Result**: HTTP 404 Not Found.
- **Root Cause**: `PurchaseOrderController` and `SupplierController` existed but their routes were not registered in `routes/web.php`.
- **Fix**: Registered `/suppliers`, `/purchases`, and `/purchases/{id}/receive` in `routes/web.php`.
- **Files Changed**: `routes/web.php`
- **Test Result**: Verified in `CommerceRegressionAuditTest::test_purchase_order_partial_and_full_receiving`.

---

### BUG-004: Incomplete `validateCoupon` API Implementation
- **Module**: Coupons & Promos API
- **Severity**: MEDIUM
- **Steps to Reproduce**: Send POST request to `/api/v1/coupons/validate` with coupon code and subtotal.
- **Expected Result**: HTTP 200 with recalculated discount amount and new total.
- **Actual Result**: Parameter mismatch on `subtotal` vs `amount`.
- **Root Cause**: Controller only inspected `amount`, failing when client sent `subtotal`.
- **Fix**: Updated `validateCoupon()` in `StorefrontController.php` to accept either `amount` or `subtotal` and return standardized `valid`, `discount_amount`, and `new_total` payload.
- **Files Changed**: `app/Http/Controllers/api/v1/StorefrontController.php`
- **Test Result**: Verified in `CommerceRegressionAuditTest::test_coupon_validation_and_calculation`.

---

### BUG-005: Missing SKU Uniqueness Validation on Admin Product Add
- **Module**: Products & Catalog
- **Severity**: MEDIUM
- **Steps to Reproduce**: Submit product add form with an already existing SKU.
- **Expected Result**: Validation error on `productSku` field.
- **Actual Result**: Database unique constraint exception thrown if SKU duplicated.
- **Root Cause**: `EcommerceProductAdd::store()` lacked `'productSku' => 'nullable|string|unique:products,sku'`.
- **Fix**: Added validation rule to `EcommerceProductAdd.php`.
- **Files Changed**: `app/Http/Controllers/apps/EcommerceProductAdd.php`
- **Test Result**: Verified in `CommerceRegressionAuditTest::test_product_validation_prevents_negative_price_and_duplicate_sku`.

---

### BUG-006: AI Generator Controller Title vs Name Parameter Mismatch
- **Module**: AI Product Copilot
- **Severity**: LOW
- **Steps to Reproduce**: Submit POST request to `/ai/product/generate` with payload `{"name": "Laptop", "category": "Tech"}`.
- **Expected Result**: AI generated marketing copy.
- **Actual Result**: HTTP 422 `Product title is required.`
- **Root Cause**: `AIProductToolsController::generateContent()` only inspected `title`, ignoring `name`.
- **Fix**: Updated parameter extractor to `$title = $request->input('title') ?: $request->input('name', '')`.
- **Files Changed**: `app/Http/Controllers/apps/AIProductToolsController.php`
- **Test Result**: Verified in `CommerceRegressionAuditTest::test_ai_tools_offline_deterministic_fallback`.

---

## Bug Summary Statistics
- **Total Bugs Discovered**: 6
- **Critical**: 0
- **High**: 3 (All Resolved)
- **Medium**: 2 (All Resolved)
- **Low**: 1 (All Resolved)
- **Resolved & Verified**: **6 / 6 (100%)**
- **Unresolved**: **0**
