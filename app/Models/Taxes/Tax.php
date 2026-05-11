<?php

namespace App\Models\Taxes;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
        protected $fillable = [
                'name',
                'percentage',
                'tax_type',
                'company_id',
                'branch_id',
        ];
}
