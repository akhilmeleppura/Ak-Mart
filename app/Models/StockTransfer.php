<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch\Branch;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'from_branch_id',
        'to_branch_id',
        'status', // pending, in_transit, completed, cancelled
        'notes',
        'user_id',
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
