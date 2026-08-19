<?php

namespace App\Services;

use App\Models\PosRegisterSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\ReturnRequest;
use App\Models\OrderTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /**
     * Calculate Indian GST Tax Breakdown (Intra-state CGST + SGST vs Inter-state IGST)
     */
    public function calculateGst(float $amount, float $gstRate = 18.0, bool $isInterState = false): array
    {
        $taxAmount = ($amount * ($gstRate / 100));

        if ($isInterState) {
            return [
                'taxable_amount' => round($amount, 2),
                'gst_rate'       => $gstRate,
                'cgst_rate'      => 0.0,
                'cgst_amount'    => 0.0,
                'sgst_rate'      => 0.0,
                'sgst_amount'    => 0.0,
                'igst_rate'      => $gstRate,
                'igst_amount'    => round($taxAmount, 2),
                'total_tax'      => round($taxAmount, 2),
                'total_with_tax' => round($amount + $taxAmount, 2),
            ];
        }

        $halfRate = $gstRate / 2;
        $halfTax = $taxAmount / 2;

        return [
            'taxable_amount' => round($amount, 2),
            'gst_rate'       => $gstRate,
            'cgst_rate'      => $halfRate,
            'cgst_amount'    => round($halfTax, 2),
            'sgst_rate'      => $halfRate,
            'sgst_amount'    => round($halfTax, 2),
            'igst_rate'      => 0.0,
            'igst_amount'    => 0.0,
            'total_tax'      => round($taxAmount, 2),
            'total_with_tax' => round($amount + $taxAmount, 2),
        ];
    }

    /**
     * Close POS Register Session with cash reconciliation
     */
    public function closeRegister(int $sessionId, float $closingCashCounted, ?string $notes = null): PosRegisterSession
    {
        $session = PosRegisterSession::findOrFail($sessionId);

        $expectedCash = (float)$session->opening_amount + (float)$session->cash_sales;
        $difference = $closingCashCounted - $expectedCash;

        $session->update([
            'closing_amount' => $closingCashCounted,
            'expected_cash'  => $expectedCash,
            'difference'     => $difference,
            'status'         => 'closed',
            'notes'          => $notes,
            'closed_at'      => now(),
        ]);

        return $session;
    }

    /**
     * Calculate True Business Net Profit
     * Formula: Gross Revenue - Discounts - Returns - COGS - Shipping - Gateway Fees - Operating Expenses = NET PROFIT
     */
    public function calculateNetProfit($startDate = null, $endDate = null, ?int $branchId = null): array
    {
        $start = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end   = $endDate ? \Carbon\Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        // 1. Gross Revenue
        $ordersQuery = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $grossRevenue = (float)$ordersQuery->sum('total_amount');
        $ordersCount = $ordersQuery->count();

        // 2. Cost of Goods Sold (COGS)
        $orderIds = (clone $ordersQuery)->pluck('id')->toArray();
        $cogs = 0.0;
        if (!empty($orderIds)) {
            $orderItems = OrderItem::with('product')->whereIn('order_id', $orderIds)->get();
            foreach ($orderItems as $item) {
                $cost = (float)($item->product?->cost_price ?: ($item->price * 0.6));
                $cogs += $cost * $item->qty;
            }
        }

        // 3. Refunds & Returns
        $refunds = (float)ReturnRequest::where('status', 'refunded')
            ->whereBetween('created_at', [$start, $end])
            ->sum('refund_amount');

        // 4. Operating Expenses
        $expenses = (float)Expense::whereBetween('expense_date', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        // 5. Estimated Payment Gateway Fees (approx 2% for digital payments)
        $digitalRevenue = (float)Order::where('payment_status', 'paid')
            ->whereIn('payment_method', ['card', 'upi', 'razorpay', 'stripe'])
            ->whereBetween('created_at', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');
        $paymentFees = round($digitalRevenue * 0.02, 2);

        // 6. Net Profit Calculation
        $netProfit = $grossRevenue - $cogs - $refunds - $expenses - $paymentFees;
        $profitMargin = $grossRevenue > 0 ? round(($netProfit / $grossRevenue) * 100, 2) : 0.0;

        return [
            'start_date'     => $start->toDateString(),
            'end_date'       => $end->toDateString(),
            'orders_count'   => $ordersCount,
            'gross_revenue'  => round($grossRevenue, 2),
            'cogs'           => round($cogs, 2),
            'refunds'        => round($refunds, 2),
            'expenses'       => round($expenses, 2),
            'payment_fees'   => round($paymentFees, 2),
            'net_profit'     => round($netProfit, 2),
            'profit_margin'  => $profitMargin,
        ];
    }
}
