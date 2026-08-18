<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $cols = [
                'product_type_id', 'url_key', 'quantity', 'special_price',
                'out_of_stock_threshold', 'min_qty_allowed_in_shopping_cart',
                'max_qty_allowed_in_shopping_cart', 'qty_uses_decimals',
                'backorders', 'attribute_set_id', 'is_variant', 'parent_id'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    try {
                        DB::statement("ALTER TABLE products MODIFY `$col` NULL");
                    } catch (\Throwable $e) {}
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
