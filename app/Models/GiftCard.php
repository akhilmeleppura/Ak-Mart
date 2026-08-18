<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'initial_balance',
        'current_balance',
        'currency',
        'recipient_email',
        'pin',
        'expiry_date',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'expiry_date'     => 'date',
            'is_active'       => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->is_active || $this->current_balance <= 0) {
            return false;
        }
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }
        return true;
    }

    public function deduct(float $amount): bool
    {
        if (!$this->isValid() || $amount <= 0 || $this->current_balance < $amount) {
            return false;
        }
        $this->current_balance -= $amount;
        $this->save();
        return true;
    }
}
