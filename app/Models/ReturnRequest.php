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
        'reason',
        'details',
        'status',
        'refund_amount',
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
}
