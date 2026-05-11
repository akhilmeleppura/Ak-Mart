<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'value', 'is_global', 
        'category_id', 'branch_id', 'is_active'
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\General\App\Models\Branch::class);
    }
}
