<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bQuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'b2b_quote_id',
        'product_id',
        'qty',
        'requested_price',
        'approved_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'requested_price' => 'decimal:2',
            'approved_price'  => 'decimal:2',
            'subtotal'        => 'decimal:2',
        ];
    }

    public function quote()
    {
        return $this->belongsTo(B2bQuote::class, 'b2b_quote_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
