<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchQueryLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_zero_result' => 'boolean',
            'results_count'  => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
