<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use App\Models\CreditNote;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreCredit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReverseLogisticsService
{
    /**
     * Create a formal RMA Return Request with line items
     */
    public function createRma(Order $order, array $itemsWithReason, ?string $overallReason = null): ReturnRequest
    {
        // Eligibility Check: Must be completed or delivered within return window (7 days)
        if (!in_array(strtolower($order->order_status), ['delivered', 'completed'])) {
            throw ValidationException::withMessages([
                'order' => 'Only delivered or completed orders are eligible for return.'
            ]);
        }

        if ($order->created_at->diffInDays(now()) > 14) {
            throw ValidationException::withMessages([
                'order' => 'Order exceeds the 14-day return eligibility window.'
            ]);
        }

        return DB::transaction(function () use ($order, $itemsWithReason, $overallReason) {
            $rmaNumber = 'RMA-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $returnRequest = ReturnRequest::create([
                'rma_number'    => $rmaNumber,
                'order_id'      => $order->id,
                'branch_id'     => $order->branch_id ?? 1,
                'reason'        => $overallReason ?? 'Customer return request',
                'details'       => 'Items submitted for RMA processing',
                'status'        => 'pending',
                'refund_amount' => 0,
            ]);

            $totalEstimatedRefund = 0.0;

            foreach ($itemsWithReason as $line) {
                $orderItem = OrderItem::where('order_id', $order->id)->where('id', $line['order_item_id'])->first();
                if (!$orderItem) continue;

                $qty = (float)($line['qty'] ?? 1);
                $unitPrice = (float)$orderItem->price;
                $lineRefund = round($unitPrice * $qty, 2);

                ReturnRequestItem::create([
                    'return_request_id' => $returnRequest->id,
                    'order_item_id'     => $orderItem->id,
                    'qty'               => $qty,
                    'reason'            => $line['reason'] ?? 'Defective / unwanted',
                    'condition'         => $line['condition'] ?? 'unopened',
                    'refund_amount'     => $lineRefund,
                ]);

                $totalEstimatedRefund += $lineRefund;
            }

            $returnRequest->update(['refund_amount' => $totalEstimatedRefund]);

            return $returnRequest->fresh(['items']);
        });
    }

    /**
     * Inspect returned items and execute restock or vendor return
     */
    public function inspectAndDecide(ReturnRequest $rma, string $decision, ?int $inspectorId = null, ?string $notes = null): ReturnRequest
    {
        return DB::transaction(function () use ($rma, $decision, $inspectorId, $notes) {
            $rma->update([
                'inspected_by_user_id' => $inspectorId,
                'inspection_result'    => $decision, // approved_restock, approved_vendor_return, rejected_damaged
                'status'               => str_starts_with($decision, 'approved') ? 'approved' : 'rejected',
                'details'              => $notes ?? $rma->details,
            ]);

            // If decision is restock, restore stock atomically
            if ($decision === 'approved_restock') {
                foreach ($rma->items as $item) {
                    $orderItem = $item->orderItem;
                    if ($orderItem && $orderItem->product_id) {
                        $product = Product::lockForUpdate()->find($orderItem->product_id);
                        if ($product) {
                            StockMovement::record(
                                $product->id,
                                (int)$item->qty,
                                'return',
                                "RMA #{$rma->rma_number} returned to inventory",
                                null,
                                $rma->branch_id,
                                'ReturnRequest',
                                $rma->id,
                                $inspectorId
                            );
                        }
                    }
                }
            }

            return $rma->fresh();
        });
    }

    /**
     * Process refund and issue credit note
     */
    public function processRefund(ReturnRequest $rma, string $method = 'wallet', ?int $staffId = null): CreditNote
    {
        return DB::transaction(function () use ($rma, $method, $staffId) {
            $order = $rma->order;
            $refundAmount = (float)$rma->refund_amount;

            // Generate Credit Note
            $creditNoteNumber = 'CN-' . date('Y') . '-' . strtoupper(Str::random(6));

            $creditNote = CreditNote::create([
                'credit_note_number' => $creditNoteNumber,
                'order_id'           => $order->id,
                'return_request_id'  => $rma->id,
                'user_id'            => $order->user_id,
                'subtotal'           => $refundAmount,
                'tax_amount'         => 0.00,
                'total_amount'       => $refundAmount,
                'status'             => 'issued',
            ]);

            // Issue refund to Customer Wallet
            if ($method === 'wallet' && $order->user_id) {
                $storeCredit = StoreCredit::firstOrCreate(
                    ['user_id' => $order->user_id],
                    ['currency' => 'USD', 'balance' => 0]
                );
                $storeCredit->credit($refundAmount, 'refund', $rma->id, "Refund for RMA #{$rma->rma_number} (Credit Note #{$creditNoteNumber})");
            }

            $rma->update([
                'status'                => 'refunded',
                'refund_method'         => $method,
                'credit_note_id'        => $creditNote->id,
                'refund_transaction_id' => $creditNoteNumber,
            ]);

            $order->update([
                'credit_note_number' => $creditNoteNumber,
            ]);

            return $creditNote;
        });
    }
}
