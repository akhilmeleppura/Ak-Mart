<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Taxes\Tax;

class BillingDebitNoteItem extends Model
{
    use HasFactory;

    protected $table = 'billing_debit_note_items';

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

    // Add this method to calculate tax amount for each item
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
        $discountAmount = ($itemTotal * $this->discount_rate) / 100;
        $totalAfterDiscount = $itemTotal - $discountAmount;

        return ($totalAfterDiscount * $tax->percentage) / 100;
    }

    public function document()
    {
        return $this->belongsTo(BillingDebitNote::class, 'document_id');
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
