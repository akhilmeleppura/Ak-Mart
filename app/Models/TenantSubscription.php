<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    protected $fillable = [
        'branch_id', 'subscription_plan_id', 'stripe_subscription_id', 'stripe_customer_id',
        'status', 'trial_ends_at', 'current_period_start', 'current_period_end', 'canceled_at'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function branch()
    {
        if (class_exists(\App\Models\Branch\Branch::class)) {
            return $this->belongsTo(\App\Models\Branch\Branch::class, 'branch_id');
        }
        return $this->belongsTo(\Modules\General\App\Models\Branch::class, 'branch_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class, 'tenant_subscription_id');
    }

    public function isActive()
    {
        return $this->status === 'active' || 
               ($this->status === 'trialing' && $this->trial_ends_at && $this->trial_ends_at->isFuture()) ||
               ($this->status === 'canceled' && $this->current_period_end && $this->current_period_end->isFuture());
    }
}
