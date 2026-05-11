<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Taxes\Tax;
use Modules\General\App\Models\Company;
use Modules\General\App\Models\Branch;
use App\Models\Customers\Customer;

class BillingDebitNote extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $table = 'billing_debit_notes';

    protected $fillable = [
        'document_prefix',
        'document_number',
        'customer_id',
        'invoice_id',
        'issue_date',
        'due_date',
        'sub_total',
        'document_discount_type',
        'document_discount_rate',
        'document_discount_amount',
        'document_tax_id',
        'note',
        'payment_status',
        'company_id',
        'branch_id',
        'created_by',
        'updated_by',
        'payment_method_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the calculated document discount amount.
     * This properly calculates the discount based on the document net amount after item adjustments.
     *
     * @return float
     */
    public function getDocumentDiscountAmountAttribute()
    {
        if (!$this->document_discount_rate || $this->document_discount_rate <= 0) {
            return 0;
        }

        // Calculate the document net amount after item-level adjustments
        $itemTotal = 0;
        $itemDiscountTotal = 0;
        $itemTaxTotal = 0;

        foreach ($this->items as $item) {
            $itemSubtotal = $item->quantity * $item->selling_unit_price;
            $itemTotal += $itemSubtotal;

            $itemDiscountAmount = $itemSubtotal * ($item->discount_rate / 100);
            $itemDiscountTotal += $itemDiscountAmount;

            if ($item->tax) {
                $itemTaxableAmount = $itemSubtotal - $itemDiscountAmount;
                $itemTaxAmount = ($itemTaxableAmount * $item->tax->percentage) / 100;
                $itemTaxTotal += $itemTaxAmount;
            }
        }

        // Document net amount after item-level adjustments
        $documentNetAmount = $itemTotal - $itemDiscountTotal + $itemTaxTotal;

        // Calculate document discount based on the type
        if ($this->document_discount_type == 1) { // Percentage
            return ($documentNetAmount * $this->document_discount_rate) / 100;
        } else { // Fixed amount
            return $this->document_discount_rate;
        }
    }

    /**
     * Get the calculated tax amount for the debit note.
     * This properly accounts for item-level discounts and taxes, then document discount.
     *
     * @return float
     */
    public function getTaxAmountAttribute()
    {
        if (!$this->document_tax_id) {
            return 0;
        }

        $tax = Tax::find($this->document_tax_id);
        if (!$tax) {
            return 0;
        }

        // Calculate the document net amount after item-level adjustments
        $itemTotal = 0;
        $itemDiscountTotal = 0;
        $itemTaxTotal = 0;

        foreach ($this->items as $item) {
            $itemSubtotal = $item->quantity * $item->selling_unit_price;
            $itemTotal += $itemSubtotal;

            $itemDiscountAmount = $itemSubtotal * ($item->discount_rate / 100);
            $itemDiscountTotal += $itemDiscountAmount;

            if ($item->tax) {
                $itemTaxableAmount = $itemSubtotal - $itemDiscountAmount;
                $itemTaxAmount = ($itemTaxableAmount * $item->tax->percentage) / 100;
                $itemTaxTotal += $itemTaxAmount;
            }
        }

        // Document net amount after item-level adjustments
        $documentNetAmount = $itemTotal - $itemDiscountTotal + $itemTaxTotal;

        // Apply document discount
        $documentDiscountAmount = $this->document_discount_amount;
        $taxableAmount = $documentNetAmount - $documentDiscountAmount;

        if ($taxableAmount < 0) {
            $taxableAmount = 0;
        }

        // Calculate document tax
        return ($taxableAmount * $tax->percentage) / 100;
    }

    /**
     * Get the document net amount after item-level adjustments.
     *
     * @return float
     */
    public function getDocumentNetAmountAttribute()
    {
        $itemTotal = 0;
        $itemDiscountTotal = 0;
        $itemTaxTotal = 0;

        foreach ($this->items as $item) {
            $itemSubtotal = $item->quantity * $item->selling_unit_price;
            $itemTotal += $itemSubtotal;

            $itemDiscountAmount = $itemSubtotal * ($item->discount_rate / 100);
            $itemDiscountTotal += $itemDiscountAmount;

            if ($item->tax) {
                $itemTaxableAmount = $itemSubtotal - $itemDiscountAmount;
                $itemTaxAmount = ($itemTaxableAmount * $item->tax->percentage) / 100;
                $itemTaxTotal += $itemTaxAmount;
            }
        }

        return $itemTotal - $itemDiscountTotal + $itemTaxTotal;
    }

    /**
     * Get the taxable amount after document discount.
     *
     * @return float
     */
    public function getDocumentTaxableAmountAttribute()
    {
        return $this->document_net_amount - $this->document_discount_amount;
    }

    /**
     * Get the total amount including all taxes and discounts.
     *
     * @return float
     */
    public function getTotalAmountAttribute()
    {
        return $this->document_net_amount - $this->document_discount_amount + $this->tax_amount;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function invoice()
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }

    public function items()
    {
        return $this->hasMany(BillingDebitNoteItem::class, 'document_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'document_tax_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
