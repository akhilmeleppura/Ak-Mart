<?php

namespace App\Services;

use App\Models\PosRegisterSession;
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

        $expectedCash = $session->opening_amount + $session->cash_sales;
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
}
