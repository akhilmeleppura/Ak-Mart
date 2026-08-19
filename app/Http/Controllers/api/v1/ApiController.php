<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Get paginated products
     */
    public function products(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true)->with(['category', 'variants']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }

        $perPage = min(50, (int)$request->input('per_page', 15));
        $products = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $products,
        ]);
    }

    /**
     * Get product categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)->withCount('products')->get();
        return response()->json([
            'status' => 'success',
            'data'   => $categories,
        ]);
    }

    /**
     * Get single product detail with real-time available stock
     */
    public function productDetails($id): JsonResponse
    {
        $product = Product::with(['category', 'variants', 'attributeValues.attribute', 'attributeValues.value'])
            ->where('is_active', true)
            ->findOrFail($id);

        $availableStock = app(InventoryService::class)->getAvailableStock($product->id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'product'         => $product,
                'available_stock' => $availableStock,
            ]
        ]);
    }

    /**
     * Get live stock status
     */
    public function inventoryStatus(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $available = app(InventoryService::class)->getAvailableStock((int)$request->product_id);

        return response()->json([
            'status'          => 'success',
            'product_id'      => (int)$request->product_id,
            'available_stock' => $available,
        ]);
    }
}
