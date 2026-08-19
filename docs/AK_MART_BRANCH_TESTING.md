# AK-Mart Branch Data Isolation & Context Switching Test Report

## Branch Isolation Architecture
AK-Mart enforces strict multi-tenant branch isolation via `session('branch_id')`, `user->branch_id`, and `BelongsToBranch` Eloquent global scopes.

---

## Branch Testing Results

| Test Scenario | Action Performed | Expected Data Scoping | Verified Result | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Branch Context Switch** | Select "London Flagship" (`/branch/2`) | `session('branch_id')` set to `2` | Active branch updated in session & topbar UI | PASSED |
| **Order Data Isolation** | View Orders on Branch 1 vs Branch 2 | Only orders belonging to active `branch_id` returned | Branch 1 orders isolated from Branch 2 | PASSED |
| **Inventory Isolation** | Check low stock alerts on Branch 3 | Low stock calculations scoped strictly to `branch_id = 3` | Stock alerts display branch-specific counts | PASSED |
| **POS Checkout Scoping** | Perform sale on Branch 1 | Order created with `branch_id = 1`, inventory decremented for Branch 1 | Sale & stock reduction bound to Branch 1 | PASSED |
| **URL Hijack Prevention** | User in Branch 1 attempts `/app/ecommerce/branch/2/edit` | Blocked unless user has Super Admin or multi-branch manager role | IDOR attempt blocked cleanly | PASSED |
| **Super Admin Bypass** | Super Admin switches between all 5 branches | Full access across Global HQ, London, Dubai, Main, Sub | Seamless branch navigation and overview | PASSED |
