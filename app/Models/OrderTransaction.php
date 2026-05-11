<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'branch_id', 'total_amount', 
        'platform_fee', 'vendor_earning', 'commission_rule_id', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\General\App\Models\Branch::class);
    }

    public function rule()
    {
        return $this->belongsTo(CommissionRule::class, 'commission_rule_id');
    }
}
