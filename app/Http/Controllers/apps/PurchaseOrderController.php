<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with(['supplier', 'items.product'])->latest()->paginate(15);
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('content.apps.purchases.index', compact('orders', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'notes'         => 'nullable|string',
            'items'         => 'nullable|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity'   => 'required_with:items|integer|min:1',
            'items.*.unit_cost'  => 'required_with:items|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $poNumber = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
            $calculatedTotal = 0;

            $itemsData = $request->input('items', []);

            if (!empty($itemsData)) {
                foreach ($itemsData as $item) {
                    $calculatedTotal += ($item['quantity'] * $item['unit_cost']);
                }
            } else {
                $calculatedTotal = $request->input('total_amount', 0);
            }

            $po = PurchaseOrder::create([
                'po_number'    => $poNumber,
                'supplier_id'  => $validated['supplier_id'],
                'total_amount' => $calculatedTotal,
                'status'       => 'ordered',
                'notes'        => $validated['notes'] ?? null,
            ]);

            // Save line items
            if (!empty($itemsData)) {
                foreach ($itemsData as $item) {
                    $subtotal = $item['quantity'] * $item['unit_cost'];
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'product_id'        => $item['product_id'],
                        'quantity'          => $item['quantity'],
                        'received_quantity' => 0,
                        'unit_cost'         => $item['unit_cost'],
                        'subtotal'          => $subtotal,
                    ]);
                }
            }

            // Update supplier balance
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->increment('balance', $calculatedTotal);
            }

            return redirect()->back()->with('success', "Purchase Order {$poNumber} created successfully for $" . number_format($calculatedTotal, 2));
        });
    }

    /**
     * Complete and receive purchase order into inventory.
     */
    public function markReceived($id)
    {
        $po = PurchaseOrder::with(['items.product', 'supplier'])->findOrFail($id);

        if ($po->status === 'received') {
            return response()->json(['success' => false, 'message' => 'This purchase order has already been received.'], 400);
        }

        return DB::transaction(function () use ($po) {
            $po->status = 'received';
            $po->save();

            // If PO had specific line items, increment each product stock
            if ($po->items->count() > 0) {
                foreach ($po->items as $item) {
                    $item->received_quantity = $item->quantity;
                    $item->save();

                    // Increment inventory & log stock movement
                    StockMovement::record(
                        $item->product_id,
                        $item->quantity,
                        'purchase',
                        "Received from PO #{$po->po_number} (Supplier: {$po->supplier?->name})",
                        $item->product_variant_id,
                        session('branch_id'),
                        'PurchaseOrder',
                        $po->id
                    );
                }
            }

            // Record audit log
            if (class_exists(AuditLog::class)) {
                AuditLog::create([
                    'user_id'        => auth()->id(),
                    'event'          => 'PurchaseOrderReceived',
                    'auditable_type' => PurchaseOrder::class,
                    'auditable_id'   => $po->id,
                    'new_values'     => json_encode(['status' => 'received', 'total' => $po->total_amount]),
                    'url'            => request()->fullUrl(),
                    'ip_address'     => request()->ip(),
                    'user_agent'     => request()->userAgent(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Purchase Order {$po->po_number} marked as received. Product inventory successfully updated!"
            ]);
        });
    }
}
