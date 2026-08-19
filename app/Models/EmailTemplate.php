<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Render template with dynamic payload
     */
    public function render(array $data): array
    {
        $renderedSubject = $this->subject;
        $renderedBody = $this->body;

        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $renderedSubject = str_replace($placeholder, (string)$value, $renderedSubject);
            $renderedBody = str_replace($placeholder, (string)$value, $renderedBody);
        }

        return [
            'subject' => $renderedSubject,
            'body'    => $renderedBody,
        ];
    }
}
