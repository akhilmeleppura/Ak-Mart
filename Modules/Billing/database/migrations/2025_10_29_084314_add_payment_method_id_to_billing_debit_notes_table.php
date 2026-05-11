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
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('invoice_id');

            // Optional: add foreign key constraint (recommended)
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_options')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('billing_debit_notes', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });
    }
};
