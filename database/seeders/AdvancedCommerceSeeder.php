<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\WorkflowRule;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Str;

class AdvancedCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Expense Categories
        $categories = [
            ['name' => 'Store Utilities & Power', 'description' => 'Electricity, water, internet bills'],
            ['name' => 'Packaging & Bags', 'description' => 'Branded shopping bags and carton packaging'],
            ['name' => 'Store Rent & Lease', 'description' => 'Monthly facility lease'],
            ['name' => 'Equipment & Hardware', 'description' => 'Barcode scanners, thermal paper, POS hardware'],
            ['name' => 'Staff Travel & Logistics', 'description' => 'Local delivery fuel and staff allowances'],
            ['name' => 'Marketing & Ads', 'description' => 'Local promotional flyers and social media campaigns'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'description' => $cat['description']]
            );
        }

        // 2. Sample Expenses
        $rentCat = ExpenseCategory::where('slug', 'store-rent-lease')->first();
        $utilCat = ExpenseCategory::where('slug', 'store-utilities-power')->first();
        $packCat = ExpenseCategory::where('slug', 'packaging-bags')->first();

        if (Expense::count() === 0) {
            if ($rentCat) {
                Expense::create([
                    'expense_category_id' => $rentCat->id,
                    'title'               => 'Monthly Storefront Commercial Rent',
                    'amount'              => 850.00,
                    'expense_date'        => now()->subDays(5)->format('Y-m-d'),
                    'payment_method'      => 'bank_transfer',
                    'reference_no'        => 'TXN-RENT-8921',
                    'notes'               => 'Paid via HDFC Bank transfer',
                    'user_id'             => 1,
                ]);
            }
            if ($utilCat) {
                Expense::create([
                    'expense_category_id' => $utilCat->id,
                    'title'               => 'Store High-Speed Fiber Internet Bill',
                    'amount'              => 65.00,
                    'expense_date'        => now()->subDays(2)->format('Y-m-d'),
                    'payment_method'      => 'card',
                    'reference_no'        => 'INV-NET-3310',
                    'notes'               => 'Airtel Broadband recurring invoice',
                    'user_id'             => 1,
                ]);
            }
            if ($packCat) {
                Expense::create([
                    'expense_category_id' => $packCat->id,
                    'title'               => 'AK-Mart Custom Biodegradable Bags (1000 pcs)',
                    'amount'              => 140.00,
                    'expense_date'        => now()->subDays(10)->format('Y-m-d'),
                    'payment_method'      => 'cash',
                    'reference_no'        => 'VOUCH-BAGS-11',
                    'notes'               => 'Cash voucher receipt #492',
                    'user_id'             => 1,
                ]);
            }
        }

        // 3. Workflow Rules
        if (WorkflowRule::count() === 0) {
            WorkflowRule::create([
                'name'          => 'High-Value VIP Order Trigger',
                'trigger_event' => 'order_created',
                'conditions'    => [
                    'field'    => 'total_amount',
                    'operator' => '>=',
                    'value'    => '200',
                ],
                'actions'       => [
                    'type'        => 'notification',
                    'message'     => 'High-value order placed over $200! Flagged for priority packing and VIP dispatch.',
                    'target_role' => 'Super Admin',
                ],
                'is_active'     => true,
            ]);

            WorkflowRule::create([
                'name'          => 'Low Stock Inventory Alert',
                'trigger_event' => 'stock_low',
                'conditions'    => [
                    'field'    => 'qty',
                    'operator' => '<=',
                    'value'    => '5',
                ],
                'actions'       => [
                    'type'        => 'create_stock_alert',
                    'message'     => 'Item reached critical minimum threshold. Supplier purchase order recommended.',
                    'target_role' => 'Super Admin',
                ],
                'is_active'     => true,
            ]);
        }

        // 4. Populate initial Stock Movements for existing products if empty
        if (StockMovement::count() === 0) {
            $products = Product::all();
            foreach ($products as $p) {
                $stockQty = $p->qty ?: 25;
                StockMovement::create([
                    'product_id'     => $p->id,
                    'branch_id'      => $p->branch_id ?? 1,
                    'type'           => 'stock_in',
                    'quantity'       => $stockQty,
                    'before_qty'     => 0,
                    'after_qty'      => $stockQty,
                    'reason'         => 'System initial stock baseline',
                    'reference_type' => 'Seeder',
                    'reference_id'   => $p->id,
                    'user_id'        => 1,
                ]);
            }
        }

        // 5. Seed Customer Loyalty Points
        $customers = User::where('user_type', 'customer')->get();
        foreach ($customers as $c) {
            if (LoyaltyTransaction::where('customer_id', $c->id)->count() === 0) {
                LoyaltyTransaction::create([
                    'customer_id' => $c->id,
                    'branch_id'   => 1,
                    'points'      => 150,
                    'type'        => 'earned',
                    'notes'       => 'Welcome bonus points on account creation',
                ]);
            }
        }
    }
}
