<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;


class JournalEntries extends Model
{
    use HasFactory;

    protected $table = 'accounting_journal_entries';

    protected $fillable = [
        'journal_id',
        'debit_amount',
        'credit_amount',
        'chart_of_account_id',
        'description',

    ];

    public function journal()
    {
        return $this->belongsTo(JournalIndex::class, 'journal_id');
    }

//    public function chartOfAccount()
// {
//     return $this->belongsTo(AccountChart::class, 'account_chart_id', 'id');
// }

 public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
    
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}


    // protected static function newFactory(): JournalEntryFactory
    // {
    //     // return JournalEntryFactory::new();
    // }
}

