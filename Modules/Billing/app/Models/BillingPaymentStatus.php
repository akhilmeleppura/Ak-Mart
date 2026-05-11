<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Billing\Database\Factories\BillingPaymentStatusFactory;

class BillingPaymentStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'billing_payment_status';

    protected $fillable = [
        'name',
        'value',
    ];

    public $timestamps = true;

    /**
     * Relationship: Status can be used by many invoices
     */
    public function invoices()
    {
        return $this->hasMany(BillingInvoice::class, 'payment_status', 'value');
    }
}
