<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorKyc;
use App\Models\VendorWallet;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    private function getBranchId(): ?int
    {
        $user = auth()->user();
        return session('branch_id') ?? ($user ? $user->branch_id : null);
    }

    public function index()
    {
        $branchId = $this->getBranchId();
        $kyc = VendorKyc::where('branch_id', $branchId)->first();
        return view('content.apps.vendor.kyc', compact('kyc'));
    }

    public function store(Request $request)
    {
        $branchId = $this->getBranchId();

        $request->validate([
            'business_name'       => 'required|string|max:255',
            'business_type'       => 'required|in:sole_proprietor,llc,partnership,company',
            'document_type'       => 'required|in:aadhar,passport,driving_license,voter_id',
            'document_front'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'document_back'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'selfie'              => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'pan_number'          => 'nullable|string|max:20',
            'gst_number'          => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:30',
            'bank_ifsc_code'      => 'nullable|string|max:15',
            'bank_name'           => 'nullable|string|max:100',
        ]);

        $existing = VendorKyc::where('branch_id', $branchId)->first();
        if ($existing && in_array($existing->status, ['pending', 'under_review', 'approved'])) {
            return response()->json(['success' => false, 'message' => 'A KYC application already exists with status: ' . ucfirst($existing->status)]);
        }

        $folder = "kyc/{$branchId}";

        $frontPath = $request->file('document_front')->store($folder, 'local');
        $backPath  = $request->hasFile('document_back')  ? $request->file('document_back')->store($folder, 'local')  : null;
        $selfiePath = $request->hasFile('selfie') ? $request->file('selfie')->store($folder, 'local') : null;

        VendorKyc::updateOrCreate(
            ['branch_id' => $branchId],
            [
                'business_name'       => $request->business_name,
                'business_type'       => $request->business_type,
                'document_type'       => $request->document_type,
                'document_front_path' => $frontPath,
                'document_back_path'  => $backPath,
                'selfie_path'         => $selfiePath,
                'pan_number'          => $request->pan_number,
                'gst_number'          => $request->gst_number,
                'bank_account_number' => $request->bank_account_number,
                'bank_ifsc_code'      => $request->bank_ifsc_code,
                'bank_name'           => $request->bank_name,
                'status'              => 'pending',
                'rejection_reason'    => null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'KYC submitted successfully. Our team will review within 2-3 business days.']);
    }
}
