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
        Schema::create('accounting_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')
                  ->constrained('accounting_journal_entries') // Make sure this table exists
                  ->onDelete('cascade');

            $table->decimal('debit_amount', 15, 2)->default(0.00);
            $table->decimal('credit_amount', 15, 2)->default(0.00);

            $table->foreignId('chart_of_account_id')
                  ->constrained('accounting_chartofaccounts') // Ensure this is correct table name
                  ->onDelete('cascade');

            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_opening_balances');
    }
};
