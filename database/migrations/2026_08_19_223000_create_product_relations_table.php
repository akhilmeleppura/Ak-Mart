<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_relations')) {
            Schema::create('product_relations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('related_id')->constrained('products')->cascadeOnDelete();
                $table->string('type')->default('related'); // related, suggested, cross_sell, upsell
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'related_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_relations');
    }
};
