<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Accounting\Database\Factories\PrefixFactory;

class Prefix extends Model
{
    use HasFactory;
    
    protected $table = 'accounting_prefixes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['journal_name'];

    // protected static function newFactory(): PrefixFactory
    // {
    //     // return PrefixFactory::new();
    // }
}
