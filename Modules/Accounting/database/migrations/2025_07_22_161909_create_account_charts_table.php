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
        Schema::create('accounting_chartofaccounts', function (Blueprint $table) {
            $table->id();

            $table->string('account_name'); // Name of the account (e.g., Cash, Sales)
           $table->string('identifier')->nullable()->comment('Custom identifier code for accounts');

            // Foreign keys
            $table->foreignId('main_category_id')
                  ->nullable()
                  ->constrained('accounting_main_categories')
                  ->onDelete('set null');

            $table->foreignId('subcategory_id')
                  ->constrained('accounting_subcategories')
                  ->onDelete('cascade');

            // ✅ New Columns
            $table->decimal('cumulative_debit', 15, 2)->default(0);
            $table->decimal('cumulative_credit', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_chartofaccounts');
    }
};
