# AK-Mart Spatie RBAC & Permission Access Control Testing

## Permission Matrix & Negative Security Testing

Testing Spatie RBAC Role + Permission + Direct Route Authorization.

| Role | Test User | Protected Route / Action | Expected Result | Actual Result | Security Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **Super Admin** | `admin@ak-mart.com` | Access Access Hub (`/app/access-hub`) | Full access to roles & permissions management | Allowed | PASSED |
| **Super Admin** | `admin@admin.com` | Access Security Audit Logs (`/app/saas/audit-logs`) | Full access to security logs & system settings | Allowed | PASSED |
| **Branch Manager** | `manager@branch.com` | Access POS Terminal (`/app/vendor/pos`) | Allowed access to POS sale terminal for active branch | Allowed | PASSED |
| **Branch Manager** | `manager@branch.com` | Direct URL: System Settings (`/app/ecommerce/settings/details`) | Blocked / Redirected unless `manage settings` permission granted | Correctly Scope Restricted | PASSED |
| **Cashier / User** | `cashier@ak-mart.com` | Access POS Terminal (`/app/vendor/pos`) | Allowed access to checkout & receipt printer | Allowed | PASSED |
| **Cashier / User** | `cashier@ak-mart.com` | Direct URL: SaaS Audit Logs (`/app/saas/audit-logs`) | Blocked with 403 / Redirected | Blocked (403 Unauthorized) | PASSED |
| **Guest / Unauth** | None | Direct URL: Dashboard (`/dashboard`) | Redirected to `/auth/login-basic` | Redirected | PASSED |
| **Guest / Unauth** | None | Direct API: POS Checkout (`/app/vendor/pos/checkout`) | 401 Unauthenticated JSON response | 401 Unauthenticated | PASSED |
