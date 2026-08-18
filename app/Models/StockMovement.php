<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch\Branch;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'branch_id',
        'type', // stock_in, stock_out, adjustment, transfer_in, transfer_out, damaged, expired, purchase, sale, return
        'quantity',
        'before_qty',
        'after_qty',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a traceable stock movement.
     */
    public static function record(
        int $productId,
        int $quantity,
        string $type,
        string $reason = null,
        ?int $variantId = null,
        ?int $branchId = null,
        ?string $refType = null,
        ?int $refId = null,
        ?int $userId = null
    ): self {
        $product = Product::find($productId);
        $beforeQty = $product ? $product->qty : 0;
        $afterQty = $beforeQty + $quantity;

        if ($product) {
            $product->qty = max(0, $afterQty);
            $product->save();
        }

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $vBefore = $variant->qty;
                $variant->qty = max(0, $vBefore + $quantity);
                $variant->save();
            }
        }

        return self::create([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'branch_id' => $branchId ?? ($product ? $product->branch_id : null) ?? session('branch_id'),
            'type' => $type,
            'quantity' => $quantity,
            'before_qty' => $beforeQty,
            'after_qty' => max(0, $afterQty),
            'reason' => $reason,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}
