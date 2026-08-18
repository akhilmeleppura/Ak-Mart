<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cart_data',
    ];

    protected function casts(): array
    {
        return [
            'cart_data' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
