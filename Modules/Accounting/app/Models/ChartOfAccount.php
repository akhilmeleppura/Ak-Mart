<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customers\Customer;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'accounting_chartofaccounts';

    protected $fillable = [
        'account_name',
        'identifier',
        'subcategory_id',
        'main_category_id',
        'cumulative_debit',
        'cumulative_credit',
        'customer_id',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cumulative_debit' => 'decimal:2',
        'cumulative_credit' => 'decimal:2',
    ];

    /**
     * Get the opening balance for the account.
     */
    public function openingBalance()
    {
        return $this->hasOne(OpeningBalance::class, 'chart_of_account_id');
    }

    /**
     * Get the subcategory that owns the account.
     */
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    /**
     * Get the main category that owns the account.
     */
    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

    /**
     * Get the ledger entries for the account.
     * Fixed foreign key from journal_id to chart_of_account_id
     */
    public function ledgerEntries()
    {
        return $this->hasMany(Ledger::class, 'chart_of_account_id');
    }

    /**
     * Get the user who created the account.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who updated the account.
     */
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Get the journal entries for the account.
     */
    public function journalEntries()
    {
        return $this->hasMany(JournalEntries::class, 'chart_of_account_id', 'id');
    }

    /**
     * Get the customer associated with the account.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the current balance for the account.
     */
    public function getCurrentBalanceAttribute()
    {
        return $this->cumulative_credit - $this->cumulative_debit;
    }

    /**
     * Scope a query to only include active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by account type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * Get the full account name with identifier.
     */
    public function getFullAccountNameAttribute()
    {
        return "{$this->identifier} - {$this->account_name}";
    }

    /**
     * Get the account type name based on the account type value.
     */
    public function getAccountTypeNameAttribute()
    {
        $types = [
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expense',
        ];

        return $types[$this->account_type] ?? 'Unknown';
    }



}