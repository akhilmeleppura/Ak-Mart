<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class EcommerceProductList extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
        $query = Product::with('category');

        // Date Filter
        if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $products = $query->latest()->get();
        
        $formatted = $products->map(function($product) {
            return [
                'id'           => $product->id,
                'product_name' => $product->name,
                'product_brand'=> $product->category?->name ?? 'Global',
                'category'     => $product->category ? $product->category->name : 'Misc',
                'stock'        => $product->qty > 0 ? 1 : 0,
                'sku'          => $product->sku,
                'price'        => '$' . number_format($product->price, 2),
                'quantity'     => $product->qty,
                'qty'          => $product->qty, // JS uses 'qty' in some places
                'status'       => $product->is_active ? 2 : 3,
                'branch_name'  => $product->branch->name ?? 'Global',
                'image'        => $product->image
            ];
        });
        
        return response()->json(['data' => $formatted]);
    }

    return view('content.apps.app-ecommerce-product-list');
  }

  public function destroy($id)
  {
      $product = Product::findOrFail($id);
      $product->delete();
      return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
  }
}
