<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'product_variant_id',
        'qty',
        'committed_qty',
        'reserved_qty',
        'min_stock',
        'max_stock',
        'bin_location',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getAvailableQtyAttribute(): int
    {
        return max(0, $this->qty - $this->committed_qty - $this->reserved_qty);
    }
}
