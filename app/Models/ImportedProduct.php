<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedProduct extends Model
{
    protected $fillable = [
        'source_type', // file, url
        'source_url',
        'asin',
        'canonical_url',
        'domain',
        'confidence_score',
        'sources',
        'warnings',
        'data',
        'status', // draft, reviewed, published, discarded
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'data'             => 'array',
            'sources'          => 'array',
            'warnings'         => 'array',
            'confidence_score' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
