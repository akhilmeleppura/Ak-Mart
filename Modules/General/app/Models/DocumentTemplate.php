<?php

namespace Modules\General\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $table = 'general_document_templates';

    protected $fillable = [
        'uuid',
        'name',
        'type',
        'header_image',
        'footer_image',
        'is_active',
        'template_id',
        'header_image',
        'footer_image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    // Accessors for image URLs
    public function getHeaderImageUrlAttribute()
    {
        return $this->header_image ? asset('storage/' . $this->header_image) : null;
    }

    public function getFooterImageUrlAttribute()
    {
        return $this->footer_image ? asset('storage/' . $this->footer_image) : null;
    }
       public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
