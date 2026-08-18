<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StockCountController extends Controller
{
    public function index()
    {
        $counts = StockCount::with(['warehouse', 'user'])->withCount('items')->latest()->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('content.apps.inventory.stock-counts', compact('counts', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'         => 'required|in:cycle,full,partial',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'notes'        => 'nullable|string',
        ]);

        $count = StockCount::create([
            'count_number' => 'CNT-' . strtoupper(Str::random(6)),
            'warehouse_id' => $request->warehouse_id,
            'branch_id'    => session('branch_id', 1),
            'type'         => $request->type,
            'status'       => 'in_progress',
            'notes'        => $request->notes,
            'user_id'      => auth()->id(),
        ]);

        // Populate items with products
        $products = Product::where('is_active', true)->get();
        foreach ($products as $p) {
            StockCountItem::create([
                'stock_count_id' => $count->id,
                'product_id'     => $p->id,
                'expected_qty'   => $p->qty,
                'counted_qty'    => $p->qty,
                'difference'     => 0,
            ]);
        }

        return redirect()->route('app-stock-counts-show', $count->id)->with('success', "Stock count {$count->count_number} initialized!");
    }

    public function show(StockCount $stockCount)
    {
        $stockCount->load(['items.product', 'warehouse', 'user']);
        return view('content.apps.inventory.stock-count-detail', compact('stockCount'));
    }

    public function updateItem(Request $request, StockCount $stockCount, StockCountItem $item)
    {
        $request->validate([
            'counted_qty' => 'required|integer|min:0',
            'remarks'     => 'nullable|string',
        ]);

        $difference = $request->counted_qty - $item->expected_qty;
        $item->update([
            'counted_qty' => $request->counted_qty,
            'difference'  => $difference,
            'remarks'     => $request->remarks,
        ]);

        return response()->json(['success' => true, 'difference' => $difference]);
    }

    public function reconcile(StockCount $stockCount)
    {
        DB::transaction(function () use ($stockCount) {
            foreach ($stockCount->items as $item) {
                if ($item->difference != 0) {
                    StockMovement::record(
                        $item->product_id,
                        (int)$item->difference,
                        'adjustment',
                        "Reconciliation from Stock Count #{$stockCount->count_number}: {$item->remarks}",
                        null,
                        $stockCount->branch_id,
                        'StockCount',
                        $stockCount->id,
                        auth()->id()
                    );
                }
            }

            $stockCount->update([
                'status'       => 'reconciled',
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('app-stock-counts')->with('success', "Stock count {$stockCount->count_number} reconciled and live inventory adjusted!");
    }
}
