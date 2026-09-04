<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReconciliation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'             => 'decimal:2',
            'gateway_fee'        => 'decimal:2',
            'net_settlement'     => 'decimal:2',
            'signature_verified' => 'boolean',
            'raw_payload'        => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
