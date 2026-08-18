<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_credit_id',
        'amount',
        'type',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function storeCredit()
    {
        return $this->belongsTo(StoreCredit::class);
    }
}
