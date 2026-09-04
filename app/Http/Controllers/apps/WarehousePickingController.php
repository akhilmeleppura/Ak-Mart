<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\FulfillmentOrder;
use App\Models\FulfillmentOrderItem;
use App\Services\WarehouseFulfillmentService;
use Illuminate\Http\Request;

class WarehousePickingController extends Controller
{
    protected WarehouseFulfillmentService $fulfillmentService;

    public function __construct(WarehouseFulfillmentService $fulfillmentService)
    {
        $this->fulfillmentService = $fulfillmentService;
    }

    /**
     * Display picking & packing dashboard
     */
    public function index(Request $request)
    {
        $branchId = session('branch_id') ?? auth()->user()?->branch_id ?? 1;

        $pendingPicking = FulfillmentOrder::with(['order.customer', 'items.orderItem.product'])
            ->whereIn('status', ['unfulfilled', 'picking'])
            ->latest()
            ->paginate(15);

        $pendingPacking = FulfillmentOrder::with(['order.customer', 'items.orderItem.product', 'packages'])
            ->whereIn('status', ['picked', 'packing', 'packed'])
            ->latest()
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'pending_picking' => $pendingPicking,
                'pending_packing' => $pendingPacking,
            ]);
        }

        return view('content.apps.warehouse-picking', compact('pendingPicking', 'pendingPacking'));
    }

    /**
     * Start picking a fulfillment order
     */
    public function startPicking(Request $request, $id)
    {
        $fulfillment = FulfillmentOrder::findOrFail($id);
        $this->fulfillmentService->startPicking($fulfillment, auth()->id());

        return response()->json(['success' => true, 'message' => 'Picking started for fulfillment #' . $fulfillment->fulfillment_number]);
    }

    /**
     * Verify picked item by barcode scan
     */
    public function verifyItem(Request $request, $itemId)
    {
        $request->validate([
            'barcode' => 'required|string',
            'qty'     => 'required|numeric|min:0.001',
        ]);

        $item = FulfillmentOrderItem::findOrFail($itemId);
        $updatedItem = $this->fulfillmentService->verifyPickItem($item, $request->barcode, (float)$request->qty);

        return response()->json(['success' => true, 'message' => 'Item verified.', 'item' => $updatedItem]);
    }

    /**
     * Complete picking and advance
     */
    public function completePicking(Request $request, $id)
    {
        $fulfillment = FulfillmentOrder::findOrFail($id);
        $this->fulfillmentService->completePicking($fulfillment);

        return response()->json(['success' => true, 'message' => 'Picking completed. Fulfillment ready for packing station.']);
    }

    /**
     * Create package at packing station
     */
    public function createPackage(Request $request, $id)
    {
        $request->validate([
            'weight_kg'    => 'required|numeric|min:0.01',
            'package_type' => 'nullable|string',
        ]);

        $fulfillment = FulfillmentOrder::findOrFail($id);
        $package = $this->fulfillmentService->createPackage(
            $fulfillment,
            (float)$request->weight_kg,
            $request->input('package_type', 'carton'),
            auth()->id()
        );

        return response()->json(['success' => true, 'message' => 'Package created and sealed.', 'package' => $package]);
    }

    /**
     * Mark ready for dispatch
     */
    public function dispatch(Request $request, $id)
    {
        $request->validate([
            'carrier'         => 'nullable|string',
            'tracking_number' => 'nullable|string',
        ]);

        $fulfillment = FulfillmentOrder::findOrFail($id);
        $this->fulfillmentService->dispatchFulfillment(
            $fulfillment,
            $request->carrier,
            $request->tracking_number
        );

        return response()->json(['success' => true, 'message' => 'Fulfillment dispatched successfully.']);
    }
}
