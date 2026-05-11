<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customers\Customer;

class Ledger extends Model
{
    use HasFactory;

    protected $table = 'accounting_ledgers';

    protected $fillable = [
        'journal_id',
        'transaction_data',
        'description',
        'credit_amount',
        'debit_amount',
        'total_credit',
        'total_debit',
        'balance',
        'customer_id'  // Add customer_id to fillable
    ];

    public $timestamps = true;

    public function journalEntry() {
        return $this->belongsTo(JournalEntries::class, 'journal_id');
    }

    public function accountChart() {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function journal() {
        return $this->belongsTo(JournalIndex::class, 'journal_id');
    }

    // Add customer relationship
 public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id', 'id');
}

}