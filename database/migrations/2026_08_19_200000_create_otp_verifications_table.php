<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('identifier'); // email or phone
            $table->string('purpose');    // LOGIN, PASSWORD_RESET, EMAIL_VERIFICATION, PHONE_VERIFICATION
            $table->string('otp_hash');   // hashed OTP — NEVER store plaintext
            $table->string('session_token')->nullable(); // binds OTP to specific session
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(5);
            $table->unsignedTinyInteger('resend_count')->default(0);
            $table->unsignedTinyInteger('max_resends')->default(3);
            $table->timestamp('last_sent_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->boolean('is_invalidated')->default(false);
            $table->timestamps();

            $table->index(['identifier', 'purpose', 'is_invalidated']);
            $table->index(['user_id', 'purpose']);
            $table->index('expires_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
