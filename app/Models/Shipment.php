<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'shipping_method_id', 'tracking_id', 
        'label_url', 'status', 'carrier_response', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'carrier_response' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }
}
