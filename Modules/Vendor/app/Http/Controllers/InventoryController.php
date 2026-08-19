<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Branch\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Show the advanced inventory management dashboard.
     */
    public function index(Request $request)
    {
        $branchId = session('branch_id');
        
        $query = Product::with(['category', 'variants']);
        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $query->where('branch_id', $branchId);
        }

        $products = $query->get();
        
        // Metrics
        $totalProducts = $products->count();
        $totalStockQty = $products->sum('qty');
        $totalValuation = $products->sum(function ($p) {
            return $p->qty * $p->price;
        });

        $lowStockProducts = $products->filter(function ($p) {
            return $p->isLowStock();
        });

        $outOfStockProducts = $products->filter(function ($p) {
            return $p->isOutOfStock();
        });

        $overstockProducts = $products->filter(function ($p) {
            return $p->qty > ($p->max_stock ?? 100);
        });

        // Stock movements log
        $movements = StockMovement::with(['product', 'user', 'branch'])
            ->latest()
            ->take(50)
            ->get();

        // Stock transfers
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'items.product', 'user'])
            ->latest()
            ->get();

        $branches = Branch::all();

        return view('content.apps.vendor.inventory', compact(
            'products',
            'totalProducts',
            'totalStockQty',
            'totalValuation',
            'lowStockProducts',
            'outOfStockProducts',
            'overstockProducts',
            'movements',
            'transfers',
            'branches'
        ));
    }

    /**
     * Record a stock adjustment with full audit movement trail.
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:stock_in,stock_out,adjustment,damaged,expired',
            'qty'        => 'required|integer',
            'reason'     => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);
        $beforeQty = (int) $product->qty;
        $adjustmentQty = (int) $request->qty;

        if (in_array($request->type, ['stock_out', 'damaged', 'expired'])) {
            $adjustmentQty = -abs($adjustmentQty);
        }

        $afterQty = max(0, $beforeQty + $adjustmentQty);
        $product->qty = $afterQty;
        $product->save();

        // Log movement
        $movement = StockMovement::create([
            'product_id'     => $product->id,
            'branch_id'      => $product->branch_id ?? session('branch_id'),
            'type'           => $request->type,
            'quantity'       => $adjustmentQty,
            'before_qty'     => $beforeQty,
            'after_qty'      => $afterQty,
            'reason'         => $request->reason ?? 'Manual stock adjustment',
            'reference_type' => 'ManualAdjustment',
            'reference_id'   => null,
            'user_id'        => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully. Audited in movement ledger.',
            'product' => $product,
            'movement' => $movement,
        ]);
    }

    /**
     * Create an inter-branch stock transfer.
     */
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id'   => 'required|exists:branches,id|different:from_branch_id',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $transfer = StockTransfer::create([
                'transfer_number' => 'TRF-' . strtoupper(Str::random(6)),
                'from_branch_id'  => $request->from_branch_id,
                'to_branch_id'    => $request->to_branch_id,
                'status'          => 'in_transit',
                'notes'           => $request->notes,
                'user_id'         => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['qty'];

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id'        => $product->id,
                    'quantity'          => $qty,
                    'received_quantity' => 0,
                ]);

                // Deduct stock from source branch
                StockMovement::record(
                    $product->id,
                    -$qty,
                    'transfer_out',
                    "Transferred to branch #{$request->to_branch_id} via {$transfer->transfer_number}",
                    null,
                    $request->from_branch_id,
                    'StockTransfer',
                    $transfer->id
                );
            }

            return redirect()->back()->with('success', "Stock Transfer {$transfer->transfer_number} created and dispatched!");
        });
    }

    /**
     * Complete and receive a stock transfer at destination branch.
     */
    public function receiveTransfer(Request $request, $id)
    {
        $transfer = StockTransfer::with('items.product')->findOrFail($id);

        if ($transfer->status === 'completed') {
            return redirect()->back()->with('error', 'Transfer already completed.');
        }

        return DB::transaction(function () use ($transfer) {
            $transfer->status = 'completed';
            $transfer->save();

            foreach ($transfer->items as $item) {
                $item->received_quantity = $item->quantity;
                $item->save();

                // Add stock to destination branch product
                StockMovement::record(
                    $item->product_id,
                    $item->quantity,
                    'transfer_in',
                    "Received transfer {$transfer->transfer_number} from branch #{$transfer->from_branch_id}",
                    null,
                    $transfer->to_branch_id,
                    'StockTransfer',
                    $transfer->id
                );
            }

            return redirect()->back()->with('success', "Transfer {$transfer->transfer_number} marked received! Destination stock updated.");
        });
    }
}
