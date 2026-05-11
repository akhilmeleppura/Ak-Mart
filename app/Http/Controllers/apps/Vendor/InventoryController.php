<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class InventoryController extends Controller
{
    /**
     * Show the inventory management dashboard.
     */
    public function index()
    {
        $products = Product::all();
        
        $lowStockProducts = Product::whereRaw('qty <= stock_alert_level')
            ->where('qty', '>', 0)
            ->get();
            
        $outOfStockProducts = Product::where('qty', '<=', 0)->get();

        return view('content.apps.vendor.inventory', compact('products', 'lowStockProducts', 'outOfStockProducts'));
    }

    /**
     * Update stock levels quickly.
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:0'
        ]);

        $product = Product::find($request->product_id);
        $product->update(['qty' => $request->qty]);

        return response()->json(['success' => true, 'message' => 'Stock updated successfully.']);
    }
}
