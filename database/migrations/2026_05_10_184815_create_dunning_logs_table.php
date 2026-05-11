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
        Schema::create('dunning_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('tenant_subscription_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number'); // 1, 2, 3, 4...
            $table->enum('type', ['email_reminder', 'grace_period_warning', 'subscription_suspended', 'subscription_canceled']);
            $table->boolean('email_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dunning_logs');
    }
};
