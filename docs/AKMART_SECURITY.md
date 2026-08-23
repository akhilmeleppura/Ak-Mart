# 🔒 AKMART — ENTERPRISE SECURITY & PERMISSION AUDIT

**Document ID**: AKMART-DOC-SEC-007  
**Security Standard**: OWASP Top 10 Enterprise Hardened  
**Date**: August 2026  

---

## 1. DEFENSE-IN-DEPTH MATRIX

| Attack Vector / Security Domain | Protection Mechanism in AKMart | Audit Status |
| :--- | :--- | :--- |
| **Authentication & Brute Force** | OTP rate limiters (3 attempts / 15 mins), password hashing (Argon2id/Bcrypt), 2FA Jetstream. | ✅ Hardened |
| **Insecure Direct Object Reference (IDOR)** | Customer portal and order tracking verify customer ID or secure random tracking token; branch isolation middleware prevents cross-tenant access. | ✅ Hardened |
| **Cross-Site Request Forgery (CSRF)** | CSRF tokens enforced on all POST/PUT/DELETE web routes; Sanctum CSRF-cookie validation for SPA. | ✅ Hardened |
| **Cross-Site Scripting (XSS)** | Blade auto-escaping `{{ }}` on all user inputs, reviews, comments; HTMLPurifier on rich CMS blocks. | ✅ Hardened |
| **SQL Injection & Mass Assignment** | Eloquent PDO parameter binding throughout; explicit `$guarded = []` and strict casts on all models. | ✅ Hardened |
| **Server-Side Request Forgery (SSRF)**| `SsrfProtectionService` validates and filters all URLs in product importer before outbound HTTP client calls (blocks private IP ranges `127.0.0.1`, `10.0.0.0/8`, `192.168.0.0/16`, AWS metadata `169.254.169.254`). | ✅ Hardened |
| **File Upload Security** | Whitelist extension check (jpg, png, webp, pdf), MIME-type verification, and random hash filename generation on storage. | ✅ Hardened |
| **Financial Concurrency Exploits** | Database transactions with `lockForUpdate()` during cart checkout and POS payment deduction. | ✅ Hardened |
| **AI Action Boundary Security** | AI actions run through controlled application services with permission checks (`ai.use`, `ai.approve`) and zero direct write access to database schemas. | ✅ Hardened |
| **Audit Trail & Observability** | Critical operations recorded in `audit_logs` and `stock_movements` with timestamp, user ID, IP address, and previous/new values. | ✅ Hardened |

---

## 2. ROLE-BASED ACCESS CONTROL (RBAC) POLICIES

- **Supreme Admin (`super_admin`)**: Universal gate bypass for system recovery and global oversight.
- **Branch Admin (`branch_admin`)**: Full operational access within assigned branch.
- **Store Manager (`manager`)**: Catalog management, order processing, stock receiving, supplier management.
- **Cashier (`cashier`)**: POS checkout, register shift management, barcode lookups.
- **Logistics Driver (`driver`)**: Dispatch portal, assigned deliveries, POD collection.
- **Vendor / Seller (`vendor`)**: Multi-vendor product listing, order fulfillment, payout requests.
- **Customer (`customer`)**: Self-service profile, order tracking, address book, wishlist, reviews, returns.
