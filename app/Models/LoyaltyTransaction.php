<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customers\Customer;
use App\Models\Branch\Branch;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'branch_id',
        'points',
        'type', // earned, redeemed, expired, adjusted
        'order_id',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Record a loyalty points transaction.
     */
    public static function recordPoints(int $customerId, int $points, string $type, ?int $orderId = null, ?string $notes = null, ?int $branchId = null): self
    {
        return self::create([
            'customer_id' => $customerId,
            'branch_id' => $branchId ?? session('branch_id'),
            'points' => $points,
            'type' => $type,
            'order_id' => $orderId,
            'notes' => $notes,
        ]);
    }

    /**
     * Get net points balance for a customer.
     */
    public static function getCustomerBalance(int $customerId): int
    {
        return (int) self::where('customer_id', $customerId)->sum('points');
    }
}
