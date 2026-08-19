<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorKyc;
use App\Models\VendorWallet;
use Illuminate\Support\Facades\DB;

class KycAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $kycs = VendorKyc::with('branch')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'      => VendorKyc::where('status', 'pending')->count(),
            'under_review' => VendorKyc::where('status', 'under_review')->count(),
            'approved'     => VendorKyc::where('status', 'approved')->count(),
            'rejected'     => VendorKyc::where('status', 'rejected')->count(),
        ];

        return view('content.apps.saas.kyc-admin', compact('kycs', 'status', 'counts'));
    }

    public function show(VendorKyc $vendorKyc)
    {
        return view('content.apps.saas.kyc-detail', compact('vendorKyc'));
    }

    public function approve(VendorKyc $vendorKyc)
    {
        DB::beginTransaction();
        try {
            $vendorKyc->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            // Unlock the vendor wallet for payouts
            VendorWallet::updateOrCreate(
                ['branch_id' => $vendorKyc->branch_id],
                ['kyc_verified' => true]
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'KYC approved. Vendor payouts are now unlocked.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, VendorKyc $vendorKyc)
    {
        $request->validate(['reason' => 'required|string|min:10']);

        $vendorKyc->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        // Lock wallet payouts again
        VendorWallet::where('branch_id', $vendorKyc->branch_id)->update(['kyc_verified' => false]);

        return response()->json(['success' => true, 'message' => 'KYC rejected and vendor notified.']);
    }

    public function markUnderReview(VendorKyc $vendorKyc)
    {
        $vendorKyc->update(['status' => 'under_review']);
        return response()->json(['success' => true, 'message' => 'KYC marked as Under Review.']);
    }
}
