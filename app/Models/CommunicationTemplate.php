<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommunicationTemplate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'variables_list' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('communication_templates')) {
            Schema::create('communication_templates', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('channel')->default('email'); // email, whatsapp, sms
                $table->string('category')->default('transactional'); // transactional, marketing, alert
                $table->string('subject')->nullable();
                $table->text('body');
                $table->json('variables_list')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }
}
