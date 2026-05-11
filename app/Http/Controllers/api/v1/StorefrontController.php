<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class StorefrontController extends Controller
{
    /**
     * Get all categories.
     */
    public function categories()
    {
        $categories = Cache::remember('categories_active', 3600, function () {
            return Category::where('is_active', true)->get();
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get products with filtering.
     */
        $cacheKey = 'products_v1_' . md5(json_encode($request->all()));

        $products = Cache::remember($cacheKey, 600, function () use ($request) {
            $query = Product::where('is_active', true);

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            return $query->paginate(20);
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);

    /**
     * Get product details.
     */
    public function productDetails($id)
    {
        $product = Product::with(['category', 'variants'])->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}
