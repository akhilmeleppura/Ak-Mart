<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('billing_invoices_items', function (Blueprint $table) {
            // Rename taxes -> tax_id
            $table->unsignedBigInteger('tax_id')->nullable()->after('selling_unit_price');
            $table->dropColumn('taxes');

            // Add new columns
            $table->decimal('discount_rate', 8, 2)->default(0)->after('tax_id');
            $table->decimal('discount_amount', 15, 2)->default(0.00)->after('discount_rate');
            $table->string('discount_type', 50)->nullable()->after('discount_amount');
            $table->decimal('subtotal', 15, 2)->default(0.00)->after('discount_type');

            // Foreign key relation (assuming taxes table exists)
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('billing_invoices_items', function (Blueprint $table) {
            // Rollback changes
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['tax_id', 'discount_rate', 'discount_amount', 'discount_type', 'subtotal']);

            // Restore old column
            $table->longText('taxes')->nullable()->comment('JSON array of tax IDs e.g. ["1","2"]');
        });
    }
};
