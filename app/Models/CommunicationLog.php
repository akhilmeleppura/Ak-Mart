<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommunicationLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'provider_response' => 'array',
        ];
    }

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('communication_logs')) {
            Schema::create('communication_logs', function (Blueprint $table) {
                $table->id();
                $table->string('channel')->default('email'); // email, whatsapp, in_app, sms
                $table->string('recipient');
                $table->string('recipient_name')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->string('template_code')->nullable();
                $table->string('subject')->nullable();
                $table->text('message_body');
                $table->string('status')->default('sent'); // queued, sent, delivered, failed
                $table->string('message_id')->nullable();
                $table->string('provider')->default('system');
                $table->text('error_message')->nullable();
                $table->json('variables')->nullable();
                $table->json('provider_response')->nullable();
                $table->timestamps();
            });
        }
    }
}
