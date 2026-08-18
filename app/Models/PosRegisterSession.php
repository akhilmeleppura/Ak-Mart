<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosRegisterSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'opening_amount',
        'closing_amount',
        'expected_cash',
        'cash_sales',
        'card_sales',
        'upi_sales',
        'difference',
        'status',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'expected_cash'  => 'decimal:2',
            'cash_sales'     => 'decimal:2',
            'card_sales'     => 'decimal:2',
            'upi_sales'      => 'decimal:2',
            'difference'     => 'decimal:2',
            'opened_at'      => 'datetime',
            'closed_at'      => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
