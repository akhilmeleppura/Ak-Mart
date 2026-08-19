<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class Order extends Model
{
    use HasFactory;
    use BelongsToBranch;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'payment_status',
        'order_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'branch_id',
        'delivery_slot_id',
        'is_pickup',
        'gift_card_code',
        'gift_card_amount',
        'store_credit_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount'        => 'decimal:2',
            'gift_card_amount'   => 'decimal:2',
            'store_credit_amount' => 'decimal:2',
            'is_pickup'           => 'boolean',
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
}
