<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'max_orders',
        'days_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'days_available' => 'array',
            'is_active'      => 'boolean',
        ];
    }
}
