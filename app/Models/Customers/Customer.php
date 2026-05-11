<?php

namespace App\Models\Customers;

use Modules\Billing\App\Models\BillingInvoice;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\App\Models\Ledger;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address','status', 'customer_type_id'];

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }

    public function invoices()
    {
        return $this->hasMany(BillingInvoice::class);
    }
    /**
     * Get status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        $badgeClass = $this->status === 'active' ? 'bg-label-success' : 'bg-label-secondary';
        $statusText = ucfirst($this->status);
        
        return "<span class='badge {$badgeClass}'>{$statusText}</span>";
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function ledgerAccounts()
{
    return $this->hasMany(Ledger::class, 'customer_id', 'id');
}

}
