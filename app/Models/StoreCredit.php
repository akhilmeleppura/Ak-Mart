<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(StoreCreditTransaction::class);
    }

    public function credit(float $amount, string $referenceType = null, $referenceId = null, string $notes = null): StoreCreditTransaction
    {
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'amount'         => $amount,
            'type'           => 'credit',
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'notes'          => $notes,
        ]);
    }

    public function debit(float $amount, string $referenceType = null, $referenceId = null, string $notes = null): ?StoreCreditTransaction
    {
        if ($this->balance < $amount) {
            return null;
        }

        $this->balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'amount'         => $amount,
            'type'           => 'debit',
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'notes'          => $notes,
        ]);
    }
}
