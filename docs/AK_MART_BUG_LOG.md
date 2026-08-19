# AK-Mart — Priority-Based Bug Log & Resolution History

| Bug ID | Severity | Category | Description | Root Cause | Resolution Status |
|--------|----------|----------|-------------|------------|-------------------|
| **BUG-01** | P0 (Blocker) | Routing | Duplicate route name `app-ai-copilot-chat` broke `route:cache` | Two controllers registered identical route name | ✅ RESOLVED (Alias renamed) |
| **BUG-02** | P0 (Blocker) | Routing | Duplicate route name `samplemodule.index` collided with resource route | SampleModule defined manual route with same name as resource | ✅ RESOLVED (Renamed to `.page1`) |
| **BUG-03** | P1 (High) | UI / Routing | Customer details tabs (Security, Billing, Notifications) 404 on click | Tab links lacked `/{id}` parameter | ✅ RESOLVED (Dynamic customer ID wired) |
| **BUG-04** | P1 (High) | UI Data | Customer tabs displayed hardcoded 'Lorine Hischke' | View used static template markup | ✅ RESOLVED (Replaced with dynamic Eloquent bindings) |
| **BUG-05** | P2 (Medium) | Navigation | Navbar shortcut links for Coupons, Expenses, Automation pointing to unregistered names | Outdated route names in JS shortcut preset dictionary | ✅ RESOLVED (Updated to canonical route names) |
| **BUG-06** | P2 (Medium) | Module | Logistics shipping deletion route `app-logistics-shipping-destroy` missing from web.php | Controller had method but route was omitted | ✅ RESOLVED (Route registered) |
| **BUG-07** | P2 (Medium) | SaaS | SaaS KYC detail review view missing (`content.apps.saas.kyc-detail`) | Controller returned view that was not yet authored | ✅ RESOLVED (Created full Blade review view) |
| **BUG-08** | P3 (Low) | Module | Trial balance ledger drill-down link name mismatch | `trialbalance/index.blade.php` used `.view` instead of `.details` | ✅ RESOLVED (Updated to `.details`) |
