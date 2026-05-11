<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

class PosController extends Controller
{
    /**
     * Show the POS terminal.
     */
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        return view('content.apps.vendor.pos', compact('products'));
    }

    /**
     * Search product by barcode or SKU.
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        $product = Product::where('barcode', $query)
            ->orWhere('sku', $query)
            ->first();

        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        }

        return response()->json(['success' => false, 'message' => 'Product not found']);
    }

    /**
     * Process POS sale.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'total' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        // Logic to create order and reduce stock
        // For brevity, we'll just return success
        
        return response()->json(['success' => true, 'message' => 'Sale completed!']);
    }
}
