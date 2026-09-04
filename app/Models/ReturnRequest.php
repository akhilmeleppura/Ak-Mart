<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;
use App\Models\Branch\Branch;

class ReturnRequest extends Model
{
    use HasFactory;
    use BelongsToBranch;

    protected $fillable = [
        'order_id',
        'branch_id',
        'rma_number',
        'reason',
        'details',
        'status',
        'refund_amount',
        'inspected_by_user_id',
        'inspection_result',
        'refund_method',
        'refund_transaction_id',
        'credit_note_id',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function creditNote()
    {
        return $this->hasOne(CreditNote::class);
    }
}
