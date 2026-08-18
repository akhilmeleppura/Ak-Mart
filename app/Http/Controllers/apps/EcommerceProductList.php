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

  /**
   * Bulk action: Update status for multiple products.
   */
  public function bulkStatus(Request $request)
  {
      $request->validate([
          'ids'    => 'required|array',
          'ids.*'  => 'exists:products,id',
          'status' => 'required|in:active,draft,archived',
      ]);

      $isActive = $request->status === 'active';
      Product::whereIn('id', $request->ids)->update(['is_active' => $isActive]);

      return response()->json([
          'success' => true,
          'message' => count($request->ids) . ' products updated to ' . ucfirst($request->status) . ' status.'
      ]);
  }

  /**
   * Bulk action: Assign category to multiple products.
   */
  public function bulkCategory(Request $request)
  {
      $request->validate([
          'ids'         => 'required|array',
          'ids.*'       => 'exists:products,id',
          'category_id' => 'required|exists:categories,id',
      ]);

      Product::whereIn('id', $request->ids)->update(['category_id' => $request->category_id]);

      return response()->json([
          'success' => true,
          'message' => count($request->ids) . ' products reassigned to selected category.'
      ]);
  }

  /**
   * Bulk action: Adjust prices (Percentage or Fixed).
   */
  public function bulkPricing(Request $request)
  {
      $request->validate([
          'ids'        => 'required|array',
          'ids.*'      => 'exists:products,id',
          'adjustment' => 'required|numeric',
          'type'       => 'required|in:percent,fixed',
      ]);

      $products = Product::whereIn('id', $request->ids)->get();
      foreach ($products as $prod) {
          $newPrice = $request->type === 'percent'
              ? round($prod->price * (1 + ($request->adjustment / 100)), 2)
              : max(0, round($prod->price + $request->adjustment, 2));

          $prod->update(['price' => $newPrice]);
      }

      return response()->json([
          'success' => true,
          'message' => count($request->ids) . ' products updated with new pricing.'
      ]);
  }

  /**
   * Duplicate product with new SKU.
   */
  public function duplicate($id)
  {
      $original = Product::with('variants')->findOrFail($id);
      $clone = $original->replicate([
          'sku',
          'barcode',
          'slug',
      ]);

      $clone->name = $original->name . ' (Copy)';
      $clone->slug = Str::slug($original->name) . '-copy-' . rand(100, 999);
      $clone->sku = 'AKM-' . strtoupper(Str::random(6));
      $clone->barcode = (string) rand(100000000000, 999999999999);
      $clone->save();

      // Duplicate variants
      foreach ($original->variants as $variant) {
          $cloneVar = $variant->replicate(['sku', 'barcode']);
          $cloneVar->product_id = $clone->id;
          $cloneVar->sku = $clone->sku . '-' . Str::slug($variant->attribute_value);
          $cloneVar->barcode = (string) rand(100000000000, 999999999999);
          $cloneVar->save();
      }

      return redirect()->route('app-ecommerce-product-edit', $clone->id)
          ->with('success', "Product successfully duplicated as '{$clone->name}'.");
  }
}
