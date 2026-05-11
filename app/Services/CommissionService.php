<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CommissionRule;
use App\Models\OrderTransaction;
use App\Models\VendorWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Process commission for a confirmed order.
     */
    public function processOrder(Order $order)
    {
        if ($order->order_status !== 'Confirmed') {
            return false;
        }

        // Prevent duplicate processing
        if (OrderTransaction::where('order_id', $order->id)->exists()) {
            return false;
        }

        // An order has a branch_id (the vendor). Let's assume order belongs to one branch.
        $vendorId = $order->branch_id;
        
        if (!$vendorId) {
            Log::warning("Order {$order->id} has no branch_id associated. Cannot process commission.");
            return false;
        }

        // 1. Determine the active Commission Rule. 
        // We look for a specific rule for this vendor, then a specific category, then the global active rule.
        // For simplicity, we just grab the global active rule or vendor specific rule.
        $rule = CommissionRule::where('branch_id', $vendorId)->where('is_active', true)->first();
        
        if (!$rule) {
            $rule = CommissionRule::where('is_global', true)->where('is_active', true)->first();
        }

        $platformFee = 0;
        $totalAmount = $order->total_amount; // Assuming order total_amount exists

        if ($rule) {
            if ($rule->type === 'percentage') {
                $platformFee = ($totalAmount * $rule->value) / 100;
            } else {
                $platformFee = $rule->value;
            }
        }

        // Prevent negative vendor earnings
        if ($platformFee > $totalAmount) {
            $platformFee = $totalAmount;
        }

        $vendorEarning = $totalAmount - $platformFee;

        DB::beginTransaction();
        try {
            // 2. Log the transaction
            OrderTransaction::create([
                'order_id' => $order->id,
                'branch_id' => $vendorId,
                'total_amount' => $totalAmount,
                'platform_fee' => $platformFee,
                'vendor_earning' => $vendorEarning,
                'commission_rule_id' => $rule ? $rule->id : null,
                'status' => 'pending' // pending until payout is requested/cleared
            ]);

            // 3. Update Vendor Wallet
            $wallet = VendorWallet::firstOrCreate(
                ['branch_id' => $vendorId],
                ['available_balance' => 0, 'pending_balance' => 0, 'total_earned' => 0]
            );

            $wallet->increment('pending_balance', $vendorEarning);
            $wallet->increment('total_earned', $vendorEarning);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process commission for Order {$order->id}: " . $e->getMessage());
            return false;
        }
    }
}
