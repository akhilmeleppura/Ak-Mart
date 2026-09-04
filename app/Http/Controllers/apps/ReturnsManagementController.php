<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ReverseLogisticsService;
use Illuminate\Http\Request;

class ReturnsManagementController extends Controller
{
    protected ReverseLogisticsService $reverseLogistics;

    public function __construct(ReverseLogisticsService $reverseLogistics)
    {
        $this->reverseLogistics = $reverseLogistics;
    }

    /**
     * Display listing of customer return requests
     */
    public function index(Request $request)
    {
        $query = ReturnRequest::with(['order.customer', 'items.orderItem.product', 'creditNote']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json(['data' => $returns]);
        }

        return view('content.apps.admin-returns', compact('returns'));
    }

    /**
     * Display a specific RMA
     */
    public function show($id)
    {
        $returnRequest = ReturnRequest::with(['order.customer', 'items.orderItem.product', 'creditNote'])->findOrFail($id);
        return response()->json(['data' => $returnRequest]);
    }

    /**
     * Inspect returned items and update decision
     */
    public function inspect(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:approved_restock,approved_vendor_return,rejected_damaged',
            'notes'    => 'nullable|string',
        ]);

        $rma = ReturnRequest::findOrFail($id);
        $updatedRma = $this->reverseLogistics->inspectAndDecide(
            $rma,
            $request->decision,
            auth()->id(),
            $request->notes
        );

        return response()->json(['success' => true, 'message' => 'Inspection decision recorded.', 'rma' => $updatedRma]);
    }

    /**
     * Issue refund & credit note
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'method' => 'required|in:wallet,original_payment',
        ]);

        $rma = ReturnRequest::findOrFail($id);
        $creditNote = $this->reverseLogistics->processRefund(
            $rma,
            $request->method,
            auth()->id()
        );

        return response()->json([
            'success'     => true,
            'message'     => 'Refund processed and Credit Note #' . $creditNote->credit_note_number . ' issued.',
            'credit_note' => $creditNote,
        ]);
    }
}
