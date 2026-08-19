<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch\Branch;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_subscription_id',
        'branch_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'payment_method',
        'plan_name',
        'billing_period_start',
        'billing_period_end',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_period_start' => 'datetime',
        'billing_period_end' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(TenantSubscription::class, 'tenant_subscription_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
