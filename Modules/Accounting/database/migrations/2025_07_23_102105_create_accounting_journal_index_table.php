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
      Schema::create('accounting_journal_indexes', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('journal_number')->unique();
            $table->unsignedBigInteger('created_by');
            $table->unsignedInteger('number_of_entries')->default(0);
            $table->decimal('transaction_amount', 15, 2)->default(0);
            $table->string('summary')->nullable(); // <-- Added summary column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('accounting_journal_entries');
    Schema::dropIfExists('accounting_opening_balance_equities');
    Schema::dropIfExists('accounting_ledger');
    Schema::dropIfExists('accounting_journal_indexes');
    }
};
