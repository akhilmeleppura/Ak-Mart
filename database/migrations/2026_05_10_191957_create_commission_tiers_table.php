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
        Schema::create('commission_tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id')->nullable(); // Optional: link to a specific rule
            $table->decimal('min_amount', 15, 2); // e.g. 0
            $table->decimal('max_amount', 15, 2)->nullable(); // e.g. 10000 (null for infinity)
            $table->decimal('percentage', 5, 2); // e.g. 5%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_tiers');
    }
};
