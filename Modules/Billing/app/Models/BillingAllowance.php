<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Billing\Database\Factories\BillingAllowanceFactory;

class BillingAllowance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'iso_code'];

    // protected static function newFactory(): BillingAllowanceFactory
    // {
    //     // return BillingAllowanceFactory::new();
    // }
}
