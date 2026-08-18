<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $cols = ['special_price', 'product_type_id', 'is_in_stock', 'url_key', 'tax_class_id', 'quantity'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    try {
                        DB::statement("ALTER TABLE products MODIFY `$col` NULL");
                    } catch (\Throwable $e) {
                        // Ignore missing column alter exception
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
