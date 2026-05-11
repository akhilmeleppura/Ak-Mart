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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. FedEx Standard, Shiprocket Express
            $table->string('carrier_code'); // fedex, shiprocket, self
            $table->decimal('base_cost', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // API keys or zone rates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
