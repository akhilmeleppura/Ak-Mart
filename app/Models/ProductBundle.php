<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_product_id',
        'item_product_id',
        'qty',
        'discount_rate',
    ];

    public function bundleProduct()
    {
        return $this->belongsTo(Product::class, 'bundle_product_id');
    }

    public function itemProduct()
    {
        return $this->belongsTo(Product::class, 'item_product_id');
    }
}
