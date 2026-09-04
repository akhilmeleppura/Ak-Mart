<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentPackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:3',
        ];
    }

    public function fulfillmentOrder()
    {
        return $this->belongsTo(FulfillmentOrder::class);
    }

    public function sealedByUser()
    {
        return $this->belongsTo(User::class, 'sealed_by_user_id');
    }
}
