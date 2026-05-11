<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Billing\Database\Factories\BillingItemFactory;

class BillingItem extends Model
{
    use HasFactory;

    protected $table = 'billing_items';

    protected $fillable = [
        'name',
        'type',
        'selling_unit_price',
    ];

    public $timestamps = true;

    /**
     * Relationship: Item can belong to many invoices through invoice items
     */
    public function invoices()
    {
        return $this->belongsToMany(
            BillingInvoice::class,
            'billing_invoices_items',
            'item_id',
            'document_id'
        )->withPivot('taxes', 'company_id', 'branch_id')->withTimestamps();
    }

    /**
     * Relationship: Item has many invoice line entries
     */
    public function invoiceItems()
    {
        return $this->hasMany(BillingInvoiceItem::class, 'item_id');
    }

    /**
     * Accessor for item type
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            1 => 'Product',
            2 => 'Service',
            default => 'Unknown',
        };
    }
}
