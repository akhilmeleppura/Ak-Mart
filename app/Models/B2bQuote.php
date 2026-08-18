<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'b2b_company_id',
        'user_id',
        'subtotal',
        'discount',
        'total',
        'status',
        'valid_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'    => 'decimal:2',
            'discount'    => 'decimal:2',
            'total'       => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(B2bCompany::class, 'b2b_company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(B2bQuoteItem::class);
    }
}
