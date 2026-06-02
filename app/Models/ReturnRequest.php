<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \App\Traits\BelongsToBranch;

    protected $fillable = ['order_id', 'reason', 'status', 'created_at', 'updated_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
