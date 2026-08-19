<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorWallet;
use App\Models\OrderTransaction;
use App\Models\PayoutRequest;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    private function getBranchId(): ?int
    {
        $user = auth()->user();
        return session('branch_id') ?? ($user ? $user->branch_id : null);
    }

    public function index()
    {
        $branchId = $this->getBranchId();

        $wallet = VendorWallet::firstOrCreate(
            ['branch_id' => $branchId],
            ['available_balance' => 0, 'pending_balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
        );

        $recentTransactions = OrderTransaction::where('branch_id', $branchId)
            ->with('order')
            ->latest()
            ->take(15)
            ->get();

        $payoutRequests = PayoutRequest::where('branch_id', $branchId)
            ->latest()
            ->get();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayEarnings = OrderTransaction::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->sum('vendor_earning');
            $chartData[] = round($dayEarnings, 2);
        }

        return view('content.apps.vendor.wallet', compact(
            'wallet', 'recentTransactions', 'payoutRequests', 'chartData'
        ));
    }

    public function requestPayout(Request $request)
    {
        $branchId = $this->getBranchId();

        $request->validate([
            'amount'         => 'required|numeric|min:10',
            'payout_method'  => 'required|string|in:bank_transfer,paypal,upi',
        ]);

        $wallet = VendorWallet::where('branch_id', $branchId)->first();

        if (!$wallet || !$wallet->kyc_verified) {
            return response()->json(['success' => false, 'message' => 'KYC verification required before requesting a payout.'], 403);
        }

        if ($request->amount > $wallet->available_balance) {
            return response()->json(['success' => false, 'message' => 'Insufficient available balance.'], 422);
        }

        DB::beginTransaction();
        try {
            // Deduct from wallet immediately to prevent double requests
            $wallet->decrement('available_balance', $request->amount);
            $wallet->increment('total_withdrawn', $request->amount);

            PayoutRequest::create([
                'branch_id'     => $branchId,
                'amount'        => $request->amount,
                'payout_method' => $request->payout_method,
                'status'        => 'pending',
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payout request submitted. Processing in 2-3 business days.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred. Please try again.'], 500);
        }
    }

    // Super Admin: approve payout
    public function approvePayout(PayoutRequest $payoutRequest)
    {
        $payoutRequest->update([
            'status' => 'completed',
            'transaction_reference' => 'TXN-' . strtoupper(uniqid()),
        ]);
        return response()->json(['success' => true, 'message' => 'Payout approved and processed.']);
    }

    // Super Admin: reject payout and refund wallet
    public function rejectPayout(Request $request, PayoutRequest $payoutRequest)
    {
        if ($payoutRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending payouts can be rejected.'], 422);
        }

        DB::beginTransaction();
        try {
            // Refund the deducted balance back
            $wallet = VendorWallet::where('branch_id', $payoutRequest->branch_id)->first();
            if ($wallet) {
                $wallet->increment('available_balance', $payoutRequest->amount);
                $wallet->decrement('total_withdrawn', $payoutRequest->amount);
            }

            $payoutRequest->update([
                'status'       => 'rejected',
                'admin_notes'  => $request->reason ?? 'Rejected by admin.',
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payout rejected and balance refunded to vendor.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error rejecting payout.'], 500);
        }
    }
}
