<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FulfillmentOrder;
use App\Models\Order;
use App\Models\Warehouse;
use App\Services\FulfillmentService;

class FulfillmentController extends Controller
{
    public function index()
    {
        $fulfillments = FulfillmentOrder::with(['order.customer', 'warehouse', 'items.orderItem'])
            ->latest()
            ->paginate(20);

        $pendingCount = FulfillmentOrder::where('status', 'unfulfilled')->count();
        $inTransitCount = FulfillmentOrder::where('status', 'shipped')->count();
        $deliveredCount = FulfillmentOrder::where('status', 'delivered')->count();

        $unfulfilledOrders = Order::where('order_status', '!=', 'completed')->with('items')->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('content.apps.fulfillment.index', compact(
            'fulfillments',
            'pendingCount',
            'inTransitCount',
            'deliveredCount',
            'unfulfilledOrders',
            'warehouses'
        ));
    }

    public function store(Request $request, FulfillmentService $fulfillmentService)
    {
        $request->validate([
            'order_id'     => 'required|exists:orders,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'items'        => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.qty'           => 'required|integer|min:1',
        ]);

        $fulfillment = $fulfillmentService->createFulfillmentOrder(
            $request->order_id,
            $request->items,
            $request->warehouse_id,
            session('branch_id', 1)
        );

        return redirect()->route('app-fulfillment')->with('success', "Fulfillment #{$fulfillment->fulfillment_number} created!");
    }

    public function updateStatus(Request $request, FulfillmentOrder $fulfillment, FulfillmentService $fulfillmentService)
    {
        $request->validate([
            'status'           => 'required|in:unfulfilled,picking,packed,shipped,delivered,cancelled',
            'shipping_carrier' => 'nullable|string',
            'tracking_number'  => 'nullable|string',
        ]);

        $fulfillmentService->updateShipmentStatus(
            $fulfillment->id,
            $request->status,
            $request->tracking_number,
            $request->shipping_carrier
        );

        return back()->with('success', "Fulfillment status updated to {$request->status}!");
    }

    public function pickPackList(FulfillmentOrder $fulfillment)
    {
        $fulfillment->load(['order.customer', 'warehouse', 'items.orderItem.product']);
        return view('content.apps.fulfillment.pick-pack-list', compact('fulfillment'));
    }
}
