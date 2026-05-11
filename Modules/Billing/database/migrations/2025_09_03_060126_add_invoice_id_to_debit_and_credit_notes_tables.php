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
        Schema::table('billing_debit_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('id');
            
            // Add foreign key constraint
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('billing_invoices')
                  ->onDelete('set null');
        });

        Schema::table('billing_credit_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('id');
            
            // Add foreign key constraint
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('billing_invoices')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_debit_notes', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['invoice_id']);
            // Then drop the column
            $table->dropColumn('invoice_id');
        });

        Schema::table('billing_credit_notes', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['invoice_id']);
            // Then drop the column
            $table->dropColumn('invoice_id');
        });
    }
};