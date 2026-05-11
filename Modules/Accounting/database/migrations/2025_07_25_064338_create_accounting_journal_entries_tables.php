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
        Schema::create('accounting_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id');
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('chart_of_account_id');
            $table->string('description')->nullable(); // <-- Add this line
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('journal_id')->references('id')->on('accounting_journal_indexes')->onDelete('cascade');
            $table->foreign('chart_of_account_id')->references('id')->on('accounting_chartofaccounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_entries');
    }
};
