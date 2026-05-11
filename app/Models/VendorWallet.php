<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'available_balance', 'pending_balance', 
        'total_earned', 'total_withdrawn', 'kyc_verified',
        'payout_method', 'payout_details'
    ];

    protected $casts = [
        'kyc_verified' => 'boolean',
        'payout_details' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(\Modules\General\App\Models\Branch::class, 'branch_id');
    }
}
