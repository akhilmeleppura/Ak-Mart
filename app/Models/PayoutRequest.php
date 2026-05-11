<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'amount', 'payout_method', 
        'status', 'admin_notes', 'transaction_reference'
    ];

    public function branch()
    {
        return $this->belongsTo(\Modules\General\App\Models\Branch::class);
    }
}
