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
        Schema::create('accounting_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->string('transaction_data');
            $table->text('description')->nullable();
            $table->decimal('credit_amount', 15, 2)->default(0.00);
            $table->decimal('debit_amount', 15, 2)->default(0.00);
            $table->decimal('total_credit', 15, 2)->default(0.00);
            $table->decimal('total_debit', 15, 2)->default(0.00);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_ledgers');
    }
};
