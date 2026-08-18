<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'fulfillment_number',
        'order_id',
        'warehouse_id',
        'branch_id',
        'status',
        'shipping_carrier',
        'tracking_number',
        'tracking_url',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipped_at'   => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(FulfillmentOrderItem::class);
    }
}
