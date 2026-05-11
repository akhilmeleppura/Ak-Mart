<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Taxes\Tax;

class BillingCreditNoteItem extends Model
{
    use HasFactory;

    protected $table = 'billing_credit_note_items';

    protected $fillable = [
        'document_id',
        'item_id',
        'description',
        'quantity',
        'selling_unit_price',
        'tax_id',
        'discount_rate',
        'subtotal',
        'company_id',
        'branch_id'
    ];

    /**
     * Get the calculated tax amount for this item.
     *
     * @return float
     */
    public function getTaxAmountAttribute()
    {
        if (!$this->tax_id) {
            return 0;
        }
        
        $tax = Tax::find($this->tax_id);
        if (!$tax) {
            return 0;
        }
        
        $itemTotal = $this->quantity * $this->selling_unit_price;
        $discountAmount = $itemTotal * ($this->discount_rate / 100);
        $taxableAmount = $itemTotal - $discountAmount;
        
        return ($taxableAmount * $tax->percentage) / 100;
    }

    /**
     * Get the total amount for this item including tax.
     *
     * @return float
     */
    public function getTotalAmountAttribute()
    {
        return $this->subtotal + $this->tax_amount;
    }

    public function document()
    {
        return $this->belongsTo(BillingCreditNote::class, 'document_id');
    }

    public function item()
    {
        return $this->belongsTo(BillingItem::class, 'item_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}