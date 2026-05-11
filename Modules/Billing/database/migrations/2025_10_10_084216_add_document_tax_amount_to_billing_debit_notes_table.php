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
            $table->decimal('document_tax_amount', 15, 2)->default(0)->after('document_tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_debit_notes', function (Blueprint $table) {
            $table->dropColumn('document_tax_amount');
        });
    }
};
