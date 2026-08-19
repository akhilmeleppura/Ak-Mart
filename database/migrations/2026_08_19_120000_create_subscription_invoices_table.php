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
        if (!Schema::hasTable('subscription_invoices')) {
            Schema::create('subscription_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_subscription_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('invoice_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->enum('status', ['paid', 'pending', 'failed', 'refunded', 'canceled'])->default('paid');
                $table->string('payment_method')->nullable()->default('Credit Card');
                $table->string('plan_name')->nullable();
                $table->timestamp('billing_period_start')->nullable();
                $table->timestamp('billing_period_end')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                $table->foreign('tenant_subscription_id')->references('id')->on('tenant_subscriptions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
