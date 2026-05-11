<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use App\Models\Order;

class ReturnRequestController extends Controller
{
    /**
     * List all return requests for the vendor.
     */
    public function index()
    {
        $requests = ReturnRequest::with(['order.user'])
            ->latest()
            ->paginate(15);

        return view('content.apps.vendor.returns', compact('requests'));
    }

    /**
     * Update return request status.
     */
    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,refunded',
            'refund_amount' => 'nullable|numeric|min:0'
        ]);

        $returnRequest->update([
            'status' => $request->status,
            'refund_amount' => $request->refund_amount ?? $returnRequest->refund_amount
        ]);

        // If status is refunded, we might want to trigger wallet adjustment logic here in a real app
        
        return redirect()->back()->with('success', 'Return request updated successfully.');
    }
}
