<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EcommerceProductAdd extends Controller
{
  public function index()
  {
    $categories = Category::all();
    return view('content.apps.app-ecommerce-product-add', compact('categories'));
  }

  public function store(Request $request)
  {
    $request->validate([
        'productTitle' => 'required|string|max:255',
        'productPrice' => 'required|numeric',
        'quantity'     => 'nullable|numeric',
        'category_id'  => 'required|exists:categories,id',
    ]);

    $product = Product::create([
        'name'             => $request->productTitle,
        'slug'             => Str::slug($request->productTitle),
        'description'      => $request->description ?? '',
        'price'            => $request->productPrice,
        'compare_at_price' => $request->productDiscountedPrice ?? 0,
        'qty'              => $request->quantity ?? 0,
        'sku'              => $request->productSku ?? '',
        'category_id'      => $request->category_id,
        'image'            => $request->productImage,
        'is_active'        => $request->status == 'Published' ? true : false,
        'meta_title'       => $request->meta_title,
        'meta_description' => $request->meta_description,
    ]);

    if ($request->has('variants')) {
        foreach ($request->variants as $variantData) {
            if ($variantData['name'] && $variantData['value']) {
                $product->variants()->create([
                    'attribute_name'  => $variantData['name'],
                    'attribute_value' => $variantData['value'],
                    'price'           => $variantData['price'],
                    'qty'             => $variantData['qty'] ?? 0,
                    'sku'             => $product->sku . '-' . $variantData['value'],
                ]);
            }
        }
    }

    return redirect()->route('app-ecommerce-product-list')->with('success', 'Product added successfully!');
  }

  public function edit($id)
  {
    $product    = Product::findOrFail($id);
    $categories = Category::all();
    return view('content.apps.app-ecommerce-product-add', compact('product', 'categories'));
  }

  public function update(Request $request, $id)
  {
    $product = Product::findOrFail($id);

    $request->validate([
        'productTitle' => 'required|string|max:255',
        'productPrice' => 'required|numeric',
        'quantity'     => 'nullable|numeric',
        'category_id'  => 'required|exists:categories,id',
    ]);

    $product->update([
        'name'             => $request->productTitle,
        'slug'             => Str::slug($request->productTitle),
        'description'      => $request->description ?? '',
        'price'            => $request->productPrice,
        'compare_at_price' => $request->productDiscountedPrice ?? 0,
        'qty'              => $request->quantity ?? 0,
        'sku'              => $request->productSku ?? '',
        'category_id'      => $request->category_id,
        'image'            => $request->productImage,
        'is_active'        => $request->status == 'Published' ? true : false,
        'meta_title'       => $request->meta_title,
        'meta_description' => $request->meta_description,
    ]);

    // Sync Variants
    if ($request->has('variants')) {
        $product->variants()->delete();
        foreach ($request->variants as $variantData) {
            if ($variantData['name'] && $variantData['value']) {
                $product->variants()->create([
                    'attribute_name'  => $variantData['name'],
                    'attribute_value' => $variantData['value'],
                    'price'           => $variantData['price'],
                    'qty'             => $variantData['qty'] ?? 0,
                    'sku'             => $product->sku . '-' . $variantData['value'],
                ]);
            }
        }
    }

    return redirect()->route('app-ecommerce-product-list')->with('success', 'Product updated successfully!');
  }
}
