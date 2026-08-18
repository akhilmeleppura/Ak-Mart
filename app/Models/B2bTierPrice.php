<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bTierPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'b2b_company_id',
        'min_qty',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function company()
    {
        return $this->belongsTo(B2bCompany::class, 'b2b_company_id');
    }
}
