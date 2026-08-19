<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingExportController extends Controller
{
    public function index()
    {
        return view('content.apps.finance.accounting-export');
    }

    /**
     * Export Sales Ledger as CSV
     */
    public function exportSales(Request $request): StreamedResponse
    {
        $fileName = 'sales_ledger_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order Number', 'Date', 'Customer', 'Payment Method', 'Payment Status', 'Taxable Amount', 'Tax', 'Total Amount']);

            Order::with('items')->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->toDateString(),
                        $order->user?->name ?? 'Guest',
                        $order->payment_method,
                        $order->payment_status,
                        number_format($order->total_amount / 1.18, 2),
                        number_format($order->total_amount - ($order->total_amount / 1.18), 2),
                        number_format($order->total_amount, 2),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Export Expenses as CSV
     */
    public function exportExpenses(Request $request): StreamedResponse
    {
        $fileName = 'expenses_ledger_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Category', 'Title', 'Amount', 'Branch ID']);

            Expense::with('category')->chunk(100, function ($expenses) use ($handle) {
                foreach ($expenses as $expense) {
                    fputcsv($handle, [
                        $expense->id,
                        $expense->expense_date,
                        $expense->category?->name ?? 'General',
                        $expense->title,
                        number_format($expense->amount, 2),
                        $expense->branch_id ?? 1,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Export GST Tax Ledger as CSV
     */
    public function exportGst(Request $request): StreamedResponse
    {
        $fileName = 'gst_tax_ledger_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Transaction Ref', 'Date', 'Type', 'Taxable Value', 'CGST (9%)', 'SGST (9%)', 'IGST (18%)', 'Total Invoice']);

            Order::where('payment_status', 'paid')->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $taxable = round($order->total_amount / 1.18, 2);
                    $tax = round($order->total_amount - $taxable, 2);
                    $halfTax = round($tax / 2, 2);

                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->toDateString(),
                        'B2C Intra-State Sale',
                        $taxable,
                        $halfTax,
                        $halfTax,
                        0.00,
                        $order->total_amount,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
