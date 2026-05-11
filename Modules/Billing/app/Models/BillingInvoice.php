<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customers\Customer;
use Modules\Billing\App\Models\BillingInvoiceItem;
use Modules\Billing\App\Models\BillingDebitNote;
use Modules\Billing\App\Models\BillingCreditNote;
use Modules\General\App\Models\DocumentTemplate;
use App\Models\Taxes\Tax;
use Modules\General\App\Models\Company;
use Modules\General\App\Models\Branch;

class BillingInvoice extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $table = 'billing_invoices';

    protected $fillable = [
        'document_prefix',
        'document_number',
        'customer_id',
        'issue_date',
        'due_date',
        'sub_total',
        'document_discount_type',
        'document_discount_rate',
        'document_discount_amount',
        'payment_status',
        'document_tax_id',
        'payment_method_id', // *** ADD THIS LINE ***
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    // ... (rest of your model code remains the same) ...

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
     * Get the calculated tax amount for the invoice.
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

    public function items()
    {
        return $this->hasMany(BillingInvoiceItem::class, 'document_id');
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

    public function debitNotes()
    {
        return $this->hasMany(BillingDebitNote::class, 'invoice_id');
    }

    public function creditNotes()
    {
        return $this->hasMany(BillingCreditNote::class, 'invoice_id');
    }

    public function status()
    {
        return $this->belongsTo(BillingPaymentStatus::class, 'payment_status', 'value');
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /* ----------------------
       Accessors for labels
    ---------------------- */
    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            0 => 'Not Paid',
            1 => 'Paid',
            2 => 'Partially Paid',
            default => 'Unknown',
        };
    }

    public function getDiscountTypeLabelAttribute()
    {
        return match ($this->document_discount_type) {
            1 => 'Percentage',
            2 => 'Fixed Amount',
            default => 'None',
        };
    }
}