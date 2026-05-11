<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class JournalIndex extends Model
{
    use HasFactory;

    protected $table = 'accounting_journal_indexes';

    protected $fillable = [
        'transaction_date',
        'journal_number',
        'created_by',
        'number_of_entries',
        'transaction_amount',
        'summary',

    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries()
    {
        return $this->hasMany(JournalEntries::class, 'journal_id');
    }
    public function ledgers()
    {
        return $this->hasMany(Ledger::class, 'journal_id');
    }
    public function ledger()
{
    return $this->hasOne(Ledger::class, 'journal_id', 'id');
}

    // protected static function newFactory(): JournalIndexFactory
    // {
    //     // return JournalIndexFactory::new();
    // }
    
}

