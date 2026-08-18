<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Product;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount('stocks')->latest()->get();
        $totalWarehouses = $warehouses->count();
        $products = Product::where('is_active', true)->get();

        return view('content.apps.inventory.warehouses', compact('warehouses', 'totalWarehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'nullable|string|max:50|unique:warehouses,code',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string',
            'contact_person' => 'nullable|string',
            'phone'          => 'nullable|string',
        ]);

        $code = $request->code ?: ('WH-' . strtoupper(Str::random(4)));

        Warehouse::create([
            'name'           => $request->name,
            'code'           => $code,
            'address'        => $request->address,
            'city'           => $request->city,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'is_active'      => true,
        ]);

        return redirect()->route('app-warehouses')->with('success', "Warehouse {$request->name} created successfully!");
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stocks.product']);
        $products = Product::where('is_active', true)->get();

        return view('content.apps.inventory.warehouse-details', compact('warehouse', 'products'));
    }

    public function updateStock(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'qty'          => 'required|integer|min:0',
            'bin_location' => 'nullable|string',
        ]);

        $stock = WarehouseStock::firstOrNew([
            'warehouse_id' => $warehouse->id,
            'product_id'   => $request->product_id,
        ]);

        $stock->qty = $request->qty;
        if ($request->filled('bin_location')) {
            $stock->bin_location = $request->bin_location;
        }
        $stock->save();

        return back()->with('success', 'Warehouse inventory allocation updated successfully!');
    }
}
