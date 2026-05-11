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
        Schema::create('payment_options', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Credit Card", "PayPal"
            $table->string('slug')->unique(); // e.g., "credit-card", "paypal" for easy lookups
            $table->text('description')->nullable(); // Optional description
            $table->string('gateway')->nullable(); // e.g., "stripe", "paypal"
            $table->boolean('is_active')->default(true); // To enable/disable a payment option
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_options');
    }
};
