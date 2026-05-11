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
        Schema::table('billing_invoices_items', function (Blueprint $table) {
              $table->integer('quantity')->default(0)->after('item_id');
            $table->decimal('selling_unit_price', 15, 2)->default(0.00)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_invoices_items', function (Blueprint $table) {
          $table->dropColumn(['quantity', 'selling_unit_price']);

        });
    }
};
