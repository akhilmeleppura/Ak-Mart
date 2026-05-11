<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Taxes\Tax;
use Modules\General\App\Models\Company;
use Modules\General\App\Models\Branch;

class BillingInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'billing_invoices_items';

    protected $fillable = [
        'document_id',
        'item_id',
        'quantity',
        'selling_unit_price',
        'tax_id',
        'discount_rate',
        'discount_amount',
        'discount_type',
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
        return $this->belongsTo(BillingInvoice::class, 'document_id');
    }

    public function billingItem()
    {
        return $this->belongsTo(BillingItem::class, 'item_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /* ----------------------
       Accessors (calculated fields)
    ---------------------- */

    // Line subtotal (before discount/tax)
    public function getLineSubtotalAttribute()
    {
        return $this->quantity * $this->selling_unit_price;
    }

    // Discount amount
    public function getDiscountAmountAttribute($value)
    {
        if (!is_null($value)) {
            return $value; // use stored value if present
        }
        $rate = $this->discount_rate ?? 0;
        return $this->line_subtotal * ($rate / 100);
    }

    // Total price after discount + tax
    public function getTotalPriceWithTaxAttribute()
    {
        $base = $this->line_subtotal - $this->discount_amount;
        return $base + $this->tax_amount;
    }
}
