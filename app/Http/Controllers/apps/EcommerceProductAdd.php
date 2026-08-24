<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EcommerceProductAdd extends Controller
{
  public function index()
  {
    $categories = Category::all();
    $parentCategories = Category::whereNull('parent_id')->with('children')->get();
    return view('content.apps.app-ecommerce-product-add', compact('categories', 'parentCategories'));
  }

  public function store(Request $request)
  {
    $request->validate([
        'productTitle' => 'required|string|max:255',
        'productPrice' => 'required|numeric|min:0',
        'quantity'     => 'nullable|numeric|min:0',
        'category_id'  => 'required|exists:categories,id',
        'productSku'   => 'nullable|string|unique:products,sku',
    ]);

    $initialQty = (int) ($request->quantity ?? 0);
    $sku = $request->productSku ?: ('AKM-' . strtoupper(Str::random(6)));
    $barcode = $request->barcode ?: rand(100000000000, 999999999999);

    $product = Product::create([
        'name'             => $request->productTitle,
        'slug'             => Str::slug($request->productTitle) . '-' . rand(100, 999),
        'brand'            => $request->brand ?? '',
        'barcode'          => $barcode,
        'description'      => $request->description ?? '',
        'price'            => $request->productPrice,
        'compare_at_price' => $request->productDiscountedPrice ?? 0,
        'qty'              => $initialQty,
        'min_stock'        => $request->min_stock ?? 5,
        'max_stock'        => $request->max_stock ?? 100,
        'sku'              => $sku,
        'category_id'      => $request->category_id,
        'image'            => $request->productImage ?? 'assets/img/ecommerce-images/product-' . rand(1, 10) . '.png',
        'is_active'        => $request->status == 'Published' ? true : false,
        'is_featured'      => $request->boolean('is_featured'),
        'meta_title'       => $request->meta_title ?? $request->productTitle,
        'meta_description' => $request->meta_description ?? Str::limit(strip_tags($request->description ?? ''), 160),
        'attributes'       => $request->attributes_json ? json_decode($request->attributes_json, true) : null,
    ]);

    // Record initial stock movement
    if ($initialQty > 0) {
        StockMovement::create([
            'product_id'     => $product->id,
            'branch_id'      => $product->branch_id ?? session('branch_id'),
            'type'           => 'stock_in',
            'quantity'       => $initialQty,
            'before_qty'     => 0,
            'after_qty'      => $initialQty,
            'reason'         => 'Initial product stock addition',
            'reference_type' => 'ProductCreation',
            'reference_id'   => $product->id,
            'user_id'        => auth()->id(),
        ]);
    }

    // Save Variants
    if ($request->has('variants') && is_array($request->variants)) {
        foreach ($request->variants as $variantData) {
            $name = $variantData['name'] ?? $variantData['attribute_name'] ?? null;
            $value = $variantData['value'] ?? $variantData['attribute_value'] ?? null;
            if ($name && $value) {
                $vQty = (int)($variantData['qty'] ?? 0);
                $product->variants()->create([
                    'attribute_name'  => $name,
                    'attribute_value' => $value,
                    'price'           => $variantData['price'] ?? $product->price,
                    'sale_price'      => $variantData['sale_price'] ?? null,
                    'qty'             => $vQty,
                    'sku'             => ($product->sku ?: 'SKU') . '-' . Str::slug($value),
                    'barcode'         => $variantData['barcode'] ?? rand(100000000000, 999999999999),
                    'weight'          => $variantData['weight'] ?? null,
                    'image'           => $variantData['image'] ?? null,
                    'status'          => $variantData['status'] ?? 'active',
                ]);
            }
        }
    }

    return redirect()->route('app-ecommerce-product-list')->with('success', 'Product added successfully with inventory tracking!');
  }

  public function edit($id)
  {
    $product    = Product::with('variants')->findOrFail($id);
    $categories = Category::all();
    $parentCategories = Category::whereNull('parent_id')->with('children')->get();
    return view('content.apps.app-ecommerce-product-add', compact('product', 'categories', 'parentCategories'));
  }

  public function update(Request $request, $id)
  {
    $product = Product::findOrFail($id);

    $request->validate([
        'productTitle' => 'required|string|max:255',
        'productPrice' => 'required|numeric|min:0',
        'quantity'     => 'nullable|numeric|min:0',
        'category_id'  => 'required|exists:categories,id',
    ]);

    $newQty = (int) ($request->quantity ?? $product->qty);
    $oldQty = (int) $product->qty;

    $product->update([
        'name'             => $request->productTitle,
        'brand'            => $request->brand ?? $product->brand,
        'barcode'          => $request->barcode ?? $product->barcode,
        'description'      => $request->description ?? '',
        'price'            => $request->productPrice,
        'compare_at_price' => $request->productDiscountedPrice ?? 0,
        'qty'              => $newQty,
        'min_stock'        => $request->min_stock ?? $product->min_stock ?? 5,
        'max_stock'        => $request->max_stock ?? $product->max_stock ?? 100,
        'sku'              => $request->productSku ?? $product->sku,
        'category_id'      => $request->category_id,
        'image'            => $request->productImage ?? $product->image,
        'is_active'        => $request->status == 'Published' ? true : false,
        'is_featured'      => $request->boolean('is_featured'),
        'meta_title'       => $request->meta_title ?? $request->productTitle,
        'meta_description' => $request->meta_description ?? $product->meta_description,
        'attributes'       => $request->attributes_json ? json_decode($request->attributes_json, true) : $product->attributes,
    ]);

    // Record stock adjustment if quantity changed
    if ($newQty !== $oldQty) {
        $diff = $newQty - $oldQty;
        StockMovement::create([
            'product_id'     => $product->id,
            'branch_id'      => $product->branch_id ?? session('branch_id'),
            'type'           => 'adjustment',
            'quantity'       => $diff,
            'before_qty'     => $oldQty,
            'after_qty'      => $newQty,
            'reason'         => 'Manual product update adjustment',
            'reference_type' => 'ProductEdit',
            'reference_id'   => $product->id,
            'user_id'        => auth()->id(),
        ]);
    }

    // Sync Variants
    if ($request->has('variants') && is_array($request->variants)) {
        $product->variants()->delete();
        foreach ($request->variants as $variantData) {
            $name = $variantData['name'] ?? $variantData['attribute_name'] ?? null;
            $value = $variantData['value'] ?? $variantData['attribute_value'] ?? null;
            if ($name && $value) {
                $product->variants()->create([
                    'attribute_name'  => $name,
                    'attribute_value' => $value,
                    'price'           => $variantData['price'] ?? $product->price,
                    'sale_price'      => $variantData['sale_price'] ?? null,
                    'qty'             => $variantData['qty'] ?? 0,
                    'sku'             => ($product->sku ?: 'SKU') . '-' . Str::slug($value),
                    'barcode'         => $variantData['barcode'] ?? rand(100000000000, 999999999999),
                    'weight'          => $variantData['weight'] ?? null,
                    'image'           => $variantData['image'] ?? null,
                    'status'          => $variantData['status'] ?? 'active',
                ]);
            }
        }
    }

    return redirect()->route('app-ecommerce-product-list')->with('success', 'Product updated successfully!');
  }
}
