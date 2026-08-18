<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'mfg_date',
        'expiry_date',
        'cost_price',
        'qty',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mfg_date'    => 'date',
            'expiry_date' => 'date',
            'cost_price'  => 'decimal:2',
            'is_active'   => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
