<?php

namespace App\Models\AccountType;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $table = 'account_types';

    protected $fillable = [
        'name',
    ];
}
