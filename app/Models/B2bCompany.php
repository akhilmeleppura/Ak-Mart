<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_code',
        'tax_id',
        'contact_email',
        'contact_phone',
        'billing_address',
        'credit_limit',
        'current_balance',
        'payment_terms',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit'    => 'decimal:2',
            'current_balance' => 'decimal:2',
        ];
    }

    public function buyers()
    {
        return $this->hasMany(B2bBuyer::class);
    }

    public function tierPrices()
    {
        return $this->hasMany(B2bTierPrice::class);
    }

    public function quotes()
    {
        return $this->hasMany(B2bQuote::class);
    }

    public function getAvailableCreditAttribute(): float
    {
        return max(0, (float)$this->credit_limit - (float)$this->current_balance);
    }
}
