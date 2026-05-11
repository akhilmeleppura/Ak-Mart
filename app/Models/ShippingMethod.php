<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'carrier_code', 'base_cost', 'is_active', 'settings'];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array'
    ];
}
