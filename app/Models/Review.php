<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'rating', 'title', 'comment', 'status', 'is_verified_purchase'];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'rating'               => 'integer',
    ];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
