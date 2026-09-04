<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTier extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'min_spend'         => 'decimal:2',
            'points_multiplier' => 'decimal:2',
            'perks'             => 'array',
        ];
    }
}
