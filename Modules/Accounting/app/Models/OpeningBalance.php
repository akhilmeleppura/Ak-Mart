<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Accounting\Database\Factories\OpeningBalanceFactory;

class OpeningBalance extends Model
{
  
 use HasFactory;

    protected $table = 'accounting_opening_balances';

    protected $fillable = [
        'journal_id',
        'debit_amount',
        'credit_amount',
        'chart_of_account_id',
        'description',
    ];

    /**
     * Relationships
     */

    public function journal()
{
    return $this->belongsTo(JournalEntries::class, 'journal_id');
}

 
    // Chart of Account Relationship
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}