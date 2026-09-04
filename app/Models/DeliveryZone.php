<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch\Branch;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'min_order_amount'        => 'decimal:2',
            'free_delivery_threshold' => 'decimal:2',
            'base_delivery_fee'       => 'decimal:2',
            'per_km_fee'              => 'decimal:2',
            'max_distance_km'         => 'decimal:2',
            'is_active'               => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Calculate dynamic delivery fee based on order subtotal and distance in km
     */
    public function calculateFee(float $orderSubtotal, float $distanceKm = 0.0): float
    {
        if ($this->free_delivery_threshold > 0 && $orderSubtotal >= (float)$this->free_delivery_threshold) {
            return 0.00;
        }

        $fee = (float)$this->base_delivery_fee;
        if ($distanceKm > 0) {
            $fee += ($distanceKm * (float)$this->per_km_fee);
        }

        return round($fee, 2);
    }
}
