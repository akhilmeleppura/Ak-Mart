<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \App\Traits\BelongsToBranch;

    protected $fillable = ['order_number', 'user_id', 'total_amount', 'payment_status', 'order_status', 'payment_method', 'shipping_address', 'billing_address', 'branch_id'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
