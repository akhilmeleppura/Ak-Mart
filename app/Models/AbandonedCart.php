<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'cart_data',
        'total_amount',
        'recovery_token',
        'recovery_emails_sent',
        'recovered_at',
    ];

    protected function casts(): array
    {
        return [
            'cart_data'    => 'array',
            'total_amount' => 'decimal:2',
            'recovered_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
