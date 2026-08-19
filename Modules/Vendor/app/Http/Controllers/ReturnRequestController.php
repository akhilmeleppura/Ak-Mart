<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    /**
     * List all return requests for the store/branch.
     */
    public function index()
    {
        $branchId = session('branch_id');
        $query = ReturnRequest::with(['order.items.product', 'order.customer'])->latest();

        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $query->where('branch_id', $branchId);
        }

        $requests = $query->paginate(15);

        return view('content.apps.vendor.returns', compact('requests'));
    }

    /**
     * Process return request and refund resolution.
     */
    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status'        => 'required|in:pending,approved,rejected,refunded',
            'refund_amount' => 'nullable|numeric|min:0',
            'restock_items' => 'nullable|boolean',
            'action_notes'  => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $returnRequest) {
            $oldStatus = $returnRequest->status;
            $newStatus = $request->status;
            $refundAmount = $request->refund_amount ?? $returnRequest->refund_amount;

            $returnRequest->update([
                'status'        => $newStatus,
                'refund_amount' => $refundAmount,
                'details'       => ($returnRequest->details ? $returnRequest->details . "\n" : "") . 
                                   "[" . date('Y-m-d H:i') . "] Status changed to {$newStatus}. " . ($request->action_notes ?? ''),
            ]);

            // Handle stock return when approved/refunded and restock is selected
            if (in_array($newStatus, ['approved', 'refunded']) && $request->boolean('restock_items')) {
                $order = $returnRequest->order;
                if ($order && $order->items) {
                    foreach ($order->items as $item) {
                        StockMovement::record(
                            $item->product_id,
                            $item->qty,
                            'return',
                            "Restocked from Return Request #{$returnRequest->id} for Order #{$order->order_number}",
                            null,
                            $returnRequest->branch_id ?? session('branch_id'),
                            'ReturnRequest',
                            $returnRequest->id
                        );
                    }
                }
            }

            // Update order status if fully refunded
            if ($newStatus === 'refunded' && $returnRequest->order) {
                $order = $returnRequest->order;
                if ($refundAmount >= $order->total_amount) {
                    $order->payment_status = 'refunded';
                    $order->order_status = 'refunded';
                } else {
                    $order->payment_status = 'partially_refunded';
                }
                $order->save();
            }

            // Audit log
            if (class_exists(AuditLog::class)) {
                AuditLog::create([
                    'user_id'        => auth()->id(),
                    'event'          => 'ReturnRequestUpdated',
                    'auditable_type' => ReturnRequest::class,
                    'auditable_id'   => $returnRequest->id,
                    'old_values'     => json_encode(['status' => $oldStatus]),
                    'new_values'     => json_encode(['status' => $newStatus, 'refund_amount' => $refundAmount]),
                    'url'            => request()->fullUrl(),
                    'ip_address'     => request()->ip(),
                    'user_agent'     => request()->userAgent(),
                ]);
            }

            return redirect()->back()->with('success', "Return Request #{$returnRequest->id} successfully updated to " . ucfirst($newStatus) . ".");
        });
    }
}
