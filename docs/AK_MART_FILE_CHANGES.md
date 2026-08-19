# AK-Mart File Changes Index

## Database Migrations
- `database/migrations/2026_08_18_120000_create_ak_mart_advanced_commerce_tables.php`: Migrated `stock_movements`, `stock_transfers`, `stock_transfer_items`, `purchase_order_items`, `expense_categories`, `expenses`, `loyalty_transactions`, `workflow_rules`, `imported_products`, and product barcode/attribute enhancements.
- `database/seeders/AdvancedCommerceSeeder.php`: Seeder for default expense categories, sample expenses, stock movements, loyalty points, and workflow rules.
- `database/seeders/DatabaseSeeder.php`: Registered `AdvancedCommerceSeeder`.

## Eloquent Models
- `app/Models/StockMovement.php`: Traceable stock transaction ledger and static `record(...)` helper.
- `app/Models/StockTransfer.php` & `app/Models/StockTransferItem.php`: Inter-branch inventory transfers.
- `app/Models/PurchaseOrderItem.php`: Line item breakdowns for purchase orders.
- `app/Models/ExpenseCategory.php` & `app/Models/Expense.php`: Store expense tracking and category classification.
- `app/Models/LoyaltyTransaction.php`: Loyalty point calculations, balances, and transaction logs.
- `app/Models/WorkflowRule.php`: Event-driven automation rules.
- `app/Models/ImportedProduct.php`: Staging schema for CSV/JSON and URL product imports.
- `app/Models/Product.php` & `app/Models/ProductVariant.php`: Enhanced with relationships, casts, and inventory helper methods.
- `app/Models/PurchaseOrder.php`, `app/Models/Supplier.php`, `app/Models/Order.php`, `app/Models/ReturnRequest.php`: Enhanced relationships and fillables.

## Backend Controllers
- `app/Http/Controllers/apps/Vendor/InventoryController.php`: Stock metrics, quick adjustments, movement ledger, and branch stock transfers.
- `app/Http/Controllers/apps/PurchaseOrderController.php`: Line items PO creation and atomic receiving pipeline with inventory and supplier updates.
- `app/Http/Controllers/apps/Vendor/PosController.php`: POS terminal, barcode search, discounts, taxes, customer loyalty points, held sales, and receipt generator.
- `app/Http/Controllers/apps/Vendor/ReturnRequestController.php`: Returns resolution, restock movement creation, and order status sync.
- `app/Http/Controllers/apps/EcommerceCustomerAll.php`: Customer segmentation (`VIP`, `High Value`, `Regular`, `At Risk`, `New`, `Inactive`), AOV, and loyalty points.
- `app/Http/Controllers/apps/ExpenseController.php`: Expense ledger CRUD and category management.
- `app/Http/Controllers/apps/CatalogScannerController.php`: Store Health calculation, diagnostic catalog quality scanner, duplicate detector, and 1-click auto-fix tools.
- `app/Http/Controllers/apps/ProductImportController.php`: URL scraper (JSON-LD, Schema.org, OpenGraph), CSV/JSON file parser, and Staging Review Screen.
- `app/Http/Controllers/apps/AIProductToolsController.php`: AI content generation, product quality optimizer, attribute extraction, and category suggester.
- `app/Http/Controllers/apps/WorkflowAutomationController.php`: Automation rule creation, toggling, and runner.
- `app/Http/Controllers/apps/ReportController.php`: Multi-tab reports, P&L statements, and deterministic sales forecasting.
- `app/Http/Controllers/api/v1/StorefrontController.php`: RESTful API v1 endpoints for catalog, categories, cart, orders, and coupon validation.

## Frontend Blade Views
- `resources/views/content/apps/vendor/inventory.blade.php`: Multi-tab view for stock ledger, movement audit history, and branch transfers.
- `resources/views/content/apps/purchases/index.blade.php`: Purchase order management with line item selector and receiving confirmation.
- `resources/views/content/apps/vendor/pos.blade.php`: POS interface with barcode scanner, category filters, hold sale, and live receipt modal.
- `resources/views/content/apps/vendor/returns.blade.php`: Returns & refunds resolution modal with restock controls.
- `resources/views/content/apps/expenses/index.blade.php`: Expense ledger view and category creator modal.
- `resources/views/content/apps/catalog/scanner.blade.php`: Store Health and catalog diagnostic scanner with auto-fix tools.
- `resources/views/content/apps/catalog/duplicates.blade.php`: Duplicate detection review screen.
- `resources/views/content/apps/catalog/importer.blade.php`: Smart Product Importer upload and staging list.
- `resources/views/content/apps/catalog/review.blade.php`: Staging review & edit screen before publishing to live store.
- `resources/views/content/apps/automation/index.blade.php`: Workflow automation rule manager.
- `resources/views/content/apps/reports/index.blade.php`: Reports suite with P&L statement and deterministic sales forecasting.
- `resources/views/content/authentications/auth-login-basic.blade.php`: Golden Supreme Admin 1-click login button.

## Routing & Navigation
- `routes/web.php`: Registered all web routes and aliases.
- `routes/api.php`: Registered Storefront API v1 endpoints.
- `bootstrap/app.php`: Loaded `api.php` routes.
- `resources/menu/verticalMenu.json`: Added navigation entries for new modules.

## Localization
- `lang/en.json`: Updated English master keys.
- `lang/ml.json`: Malayalam language translations.
- `lang/hi.json`: Hindi language translations.
- `lang/ta.json`: Tamil language translations.
- `lang/kn.json`: Kannada language translations.

## Automated Test Suite
- `tests/Feature/AdvancedCommerceTest.php`: 9 comprehensive feature tests verifying all new subsystems.
