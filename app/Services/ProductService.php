<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create product with attributes and initial stock baseline
     */
    public function createProduct(array $data, ?int $userId = null): Product
    {
        return DB::transaction(function () use ($data, $userId) {
            $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::random(5);
            }

            $product = Product::create([
                'name'             => $data['name'],
                'slug'             => $slug,
                'category_id'      => $data['category_id'] ?? null,
                'branch_id'        => $data['branch_id'] ?? 1,
                'brand'            => $data['brand'] ?? null,
                'sku'              => $data['sku'] ?? ('SKU-' . strtoupper(Str::random(6))),
                'barcode'          => $data['barcode'] ?? null,
                'price'            => $data['price'],
                'compare_at_price' => $data['compare_at_price'] ?? null,
                'qty'              => $data['qty'] ?? 0,
                'min_stock'        => $data['min_stock'] ?? 5,
                'max_stock'        => $data['max_stock'] ?? 100,
                'description'      => $data['description'] ?? null,
                'attributes'       => $data['attributes'] ?? null,
                'image'            => $data['image'] ?? null,
                'is_active'        => $data['is_active'] ?? true,
                'is_featured'      => $data['is_featured'] ?? false,
                'meta_title'       => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
            ]);

            if ($product->qty > 0) {
                StockMovement::record(
                    $product->id,
                    $product->qty,
                    'stock_in',
                    'Initial product catalog baseline',
                    null,
                    $product->branch_id,
                    'Product',
                    $product->id,
                    $userId
                );
            }

            return $product;
        });
    }
}
