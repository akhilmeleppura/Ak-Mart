<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class Order extends Model
{
    use HasFactory;
    use BelongsToBranch;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'total_amount'        => 'decimal:2',
            'tax_amount'          => 'decimal:2',
            'tax_breakdown'       => 'array',
            'gift_card_amount'    => 'decimal:2',
            'store_credit_amount' => 'decimal:2',
            'discount_amount'     => 'decimal:2',
            'is_pickup'           => 'boolean',
            'metadata'            => 'array',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function fulfillments()
    {
        return $this->hasMany(FulfillmentOrder::class);
    }

    public function deliverySlot()
    {
        return $this->belongsTo(DeliverySlot::class, 'delivery_slot_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
