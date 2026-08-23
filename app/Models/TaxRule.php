<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxRule extends Model
{
    use HasFactory;

    protected $table = 'tax_rules';

    protected $fillable = [
        'name',
        'tax_class',
        'tax_type',
        'rate',
        'country_code',
        'state_name',
        'postal_code_pattern',
        'is_compound',
        'priority',
        'is_active',
        'calculation_mode',
    ];

    protected $casts = [
        'rate'        => 'decimal:2',
        'is_compound' => 'boolean',
        'is_active'   => 'boolean',
        'priority'    => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
