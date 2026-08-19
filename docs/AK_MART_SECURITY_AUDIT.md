# AK-Mart Security, Privacy & Compliance Audit

## 1. Authentication, Authorization & RBAC
- **Role-Based Access Control (RBAC)**: Powered by `spatie/laravel-permission` with granular permissions (`view_dashboard`, `manage_products`, `manage_orders`, `manage_inventory`, `manage_settings`, `manage_finance`, `access_pos`).
- **Multi-Branch Isolation**: Scoped at the model level via `App\Traits\BelongsToBranch` to prevent cross-tenant or unauthorized cross-branch data leakage.
- **Session Security**: Configurable session lifetime, automatic CSRF token regeneration, and simultaneous session management.

---

## 2. Cryptographic Protection of Secrets
- **AES-256 Encryption**:
  All sensitive credentials stored in `store_settings` are encrypted using Laravel's native `Crypt::encryptString()` before database persistence:
  - `smtp_password`
  - `stripe_secret`
  - `paypal_secret`
  - `phonepe_salt_key`
  - `whatsapp_access_token`
  - `gemini_api_key`
  - `webhook_secret`
- **Masked Display**:
  Secret fields are masked (`••••••••`) in all Blade views and cannot be read in plaintext by browser inspection.
- **Zero Logging Policy**:
  Secrets are stripped from exception messages, `AuditLog` records, and debug outputs.

---

## 3. Web Application Security Controls

| Vulnerability Vector | Mitigation Mechanism | Verification Status |
| :--- | :--- | :---: |
| **Cross-Site Request Forgery (CSRF)** | Strict `@csrf` tokens on all POST/PUT/DELETE forms and AJAX headers | Verified (Passed) |
| **SQL Injection (SQLi)** | 100% Parameterized PDO queries through Eloquent ORM | Verified (Passed) |
| **Cross-Site Scripting (XSS)** | Blade auto-escaping `{{ }}` on all user inputs | Verified (Passed) |
| **Insecure Direct Object Reference (IDOR)** | Route model binding with ownership & branch authorization checks | Verified (Passed) |
| **Unrestricted File Upload** | MIME type verification (`image/jpeg,png,webp`), file size caps | Verified (Passed) |
| **Brute Force Attacks** | Rate limiting on authentication routes and configurable lockout rules | Verified (Passed) |

---

## 4. Audit Logging & Non-Repudiation
- All significant state changes (Profile updates, Settings modifications, Return request status changes, Manual stock adjustments) are committed to the `audit_logs` table with authenticated user ID, event name, client IP address, and timestamp.
