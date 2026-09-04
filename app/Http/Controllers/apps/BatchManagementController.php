<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\ProductBatch;
use App\Models\Product;
use App\Services\BatchExpiryService;
use Illuminate\Http\Request;

class BatchManagementController extends Controller
{
    protected BatchExpiryService $batchService;

    public function __construct(BatchExpiryService $batchService)
    {
        $this->batchService = $batchService;
    }

    /**
     * Display batches and expiry dashboard
     */
    public function index(Request $request)
    {
        $batches = ProductBatch::with('product')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        $nearExpiry = $this->batchService->getNearExpiryBatches(7);

        if ($request->ajax()) {
            return response()->json([
                'batches'     => $batches,
                'near_expiry' => $nearExpiry,
            ]);
        }

        return view('content.apps.inventory-batches', compact('batches', 'nearExpiry'));
    }

    /**
     * Store new batch
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'batch_number' => 'required|string',
            'qty'          => 'required|integer|min:1',
            'cost_price'   => 'nullable|numeric|min:0',
            'mfg_date'     => 'nullable|date',
            'expiry_date'  => 'nullable|date',
        ]);

        $batch = ProductBatch::create([
            'product_id'     => $request->product_id,
            'batch_number'   => $request->batch_number,
            'qty'            => $request->qty,
            'available_qty'  => $request->qty,
            'cost_price'     => $request->cost_price ?? 0,
            'mfg_date'       => $request->mfg_date,
            'expiry_date'    => $request->expiry_date,
            'received_date'  => now()->toDateString(),
            'is_active'      => true,
            'status'         => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Batch created successfully.', 'batch' => $batch]);
    }

    /**
     * Trigger expired batches scan and write-off
     */
    public function sweepExpired(Request $request)
    {
        $count = $this->batchService->markExpiredBatches(auth()->id());
        return response()->json(['success' => true, 'message' => "Expired inventory sweep completed. {$count} batches marked as expired."]);
    }

    /**
     * FEFO Allocation Simulator
     */
    public function simulateFefo(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
        ]);

        $allocations = $this->batchService->allocateFefo((int)$request->product_id, (int)$request->qty, auth()->id());

        return response()->json([
            'success'     => true,
            'allocations' => $allocations,
        ]);
    }
}
