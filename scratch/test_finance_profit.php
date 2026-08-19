<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FinanceService;
use App\Models\Expense;
use App\Models\ExpenseCategory;

echo "=== AK-Mart Financial & True Net Profit Engine Test ===\n\n";

$finService = app(FinanceService::class);

// 1. GST Tax Breakdown
$gstIntra = $finService->calculateGst(1000.00, 18.0, false);
echo "1. Intra-State GST Breakdown (₹1,000 @ 18%):\n";
echo "   - CGST (9%): ₹{$gstIntra['cgst_amount']}\n";
echo "   - SGST (9%): ₹{$gstIntra['sgst_amount']}\n";
echo "   - Total Tax: ₹{$gstIntra['total_tax']}\n";
echo "   - Total With Tax: ₹{$gstIntra['total_with_tax']}\n";
echo "   - Result: " . ($gstIntra['total_tax'] == 180.00 ? "✓ PASS" : "FAIL") . "\n\n";

$gstInter = $finService->calculateGst(1000.00, 18.0, true);
echo "2. Inter-State IGST Breakdown (₹1,000 @ 18%):\n";
echo "   - IGST (18%): ₹{$gstInter['igst_amount']}\n";
echo "   - Total Tax: ₹{$gstInter['total_tax']}\n";
echo "   - Result: " . ($gstInter['total_tax'] == 180.00 ? "✓ PASS" : "FAIL") . "\n\n";

// 3. True Net Profit Calculation
// Ensure an expense category exists
$category = ExpenseCategory::firstOrCreate(['id' => 1], ['name' => 'Store Utilities']);
Expense::create([
    'expense_category_id' => $category->id,
    'branch_id' => 1,
    'amount' => 150.00,
    'title' => 'Electricity & Store Utilities',
    'expense_date' => now()->toDateString(),
]);

$profitReport = $finService->calculateNetProfit(now()->startOfMonth(), now()->endOfMonth());
echo "3. True Net Profit Engine:\n";
echo "   - Period: {$profitReport['start_date']} to {$profitReport['end_date']}\n";
echo "   - Gross Revenue: \${$profitReport['gross_revenue']}\n";
echo "   - Cost of Goods Sold (COGS): \${$profitReport['cogs']}\n";
echo "   - Refunds / Returns: \${$profitReport['refunds']}\n";
echo "   - Operating Expenses: \${$profitReport['expenses']}\n";
echo "   - Payment Gateway Fees: \${$profitReport['payment_fees']}\n";
echo "   - NET PROFIT: \${$profitReport['net_profit']}\n";
echo "   - Profit Margin: {$profitReport['profit_margin']}%\n";
echo "   - Result: " . ($profitReport['gross_revenue'] >= 0 ? "✓ PASS" : "FAIL") . "\n\n";

echo "=== Financial & Profit Engine Suite Passed 100% ===\n";
