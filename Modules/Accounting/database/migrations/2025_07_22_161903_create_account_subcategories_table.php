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
        Schema::create('accounting_subcategories', function (Blueprint $table) {
            $table->id();
                $table->string('name'); // Name of the subcategory
            $table->text('description')->nullable(); // Optional description of the subcategory
            $table->foreignId('main_category_id') // Link to main category
                  ->constrained('accounting_main_categories') // Reference to main category table
                  ->onDelete('cascade'); // If main category is deleted, subcategories will be too
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_subcategories');
    }
};
