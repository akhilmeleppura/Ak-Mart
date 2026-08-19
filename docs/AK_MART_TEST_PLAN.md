# AK-Mart Master QA & E-Commerce Test Plan

**Document Version**: 2.0  
**Status**: Executed & Verified  
**Coverage**: 100% Core Commerce, Communication, Quality, POS & Security Modules  

---

## 1. Automated & Browser Master Test Matrix

| Feature | Module | Test Case | Expected Result | Actual Result | Status | Severity | Browser Tested | Backend Tested | DB Tested | API Tested | Fixed | Retested |
| :--- | :--- | :--- | :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Authentication** | Auth | Login with Valid Credentials | Authenticated redirect to dashboard | Success | `PASS` | Critical | Yes | Yes | Yes | Yes | N/A | Yes |
| **Authentication** | Auth | Login with Invalid Password | Form error "These credentials do not match" | Success | `PASS` | High | Yes | Yes | Yes | Yes | N/A | Yes |
| **RBAC / Security** | Permissions | Supreme Admin Gate Bypass | Full system access unrestricted | Success | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **RBAC / Security** | Customer | IDOR Customer Order Boundary | User A cannot access User B order | 403 Forbidden | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **Pricing Engine** | Pricing | Zero-Trust Server Recalculation | Frontend price tampering ignored | Calculated | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **Pricing Engine** | Pricing | B2B Volume Tier Pricing | Unit price drops at quantity breakpoint | Tier applied | `PASS` | High | Yes | Yes | Yes | Yes | Yes | Yes |
| **Pricing Engine** | Pricing | Split Tender (Coupon+GC+Credit) | Accurate deduction & non-negative total | Exact total | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **Smart Importer** | Importer | Amazon/Flipkart/Shopify URL Extract | JSON-LD / DOM structured extract | Exact data | `PASS` | High | Yes | Yes | Yes | Yes | Yes | Yes |
| **Smart Importer** | Quality | Product Quality Scoring (0-100) | Completeness score & diagnostic tips | Scored | `PASS` | Medium | Yes | Yes | Yes | Yes | Yes | Yes |
| **POS Terminal** | POS | Cashier Shift Register Lifecycle | Open float, cash sales, variance | Accurate | `PASS` | High | Yes | Yes | Yes | Yes | N/A | Yes |
| **Inventory 2.0** | Stock | Multi-Warehouse Stock Allocation | Warehouse specific reservation & lock | Reserved | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **Inventory 2.0** | ABC | Dead Stock & Turnover Analysis | Products classified into A, B, C | Classified | `PASS` | Medium | Yes | Yes | Yes | Yes | N/A | Yes |
| **Fulfillment** | Shipping | Advanced Fulfillment State Flow | Order items split & tracking assigned | Shipped | `PASS` | High | Yes | Yes | Yes | Yes | N/A | Yes |
| **Communication** | Email | Transactional Order Dispatch | Interpolated `{{order_number}}` sent | Delivered | `PASS` | High | Yes | Yes | Yes | Yes | Yes | Yes |
| **Communication** | WhatsApp | WhatsApp Cloud API Dispatch | Standard template & payload created | Delivered | `PASS` | High | Yes | Yes | Yes | Yes | Yes | Yes |
| **Communication** | Preferences| Customer Marketing Opt-Out | Skip promotional messages if opted-out | Skipped | `PASS` | High | Yes | Yes | Yes | Yes | Yes | Yes |
| **Communication** | Fail-Safe | Isolation from Commerce Orders | Order finishes even if SMTP fails | Unblocked | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **Marketing** | Campaigns | Broadcast Campaign Targeting | Audience filter (VIP/Inactive) | Dispatched | `PASS` | Medium | Yes | Yes | Yes | Yes | Yes | Yes |
| **Webhooks** | Integration | Idempotent Webhook Processing | Duplicate webhook payload executed once | Idempotent | `PASS` | Critical | Yes | Yes | Yes | Yes | Yes | Yes |
| **UI / Theme** | Sidebar | Settings Inner Menu Navigation | Bordered card, exact active state | High contrast | `PASS` | UI | Yes | Yes | N/A | N/A | Yes | Yes |
