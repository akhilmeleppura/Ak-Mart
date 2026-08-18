<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bBuyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'b2b_company_id',
        'user_id',
        'role',
        'spending_limit',
        'can_approve_orders',
    ];

    protected function casts(): array
    {
        return [
            'spending_limit'     => 'decimal:2',
            'can_approve_orders' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(B2bCompany::class, 'b2b_company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
