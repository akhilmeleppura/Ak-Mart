<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('communication_templates')) {
            Schema::create('communication_templates', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('channel')->default('email'); // email, whatsapp, sms
                $table->string('category')->default('transactional'); // transactional, marketing, alert, reminder
                $table->string('subject')->nullable();
                $table->text('body');
                $table->json('variables_list')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('communication_logs')) {
            Schema::create('communication_logs', function (Blueprint $table) {
                $table->id();
                $table->string('channel')->default('email'); // email, whatsapp, in_app, sms
                $table->string('recipient');
                $table->string('recipient_name')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
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

                $table->index(['channel', 'status']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep tables to preserve communication history
    }
};
