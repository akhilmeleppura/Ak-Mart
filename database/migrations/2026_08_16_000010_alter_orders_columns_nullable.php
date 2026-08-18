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
            $cols = ['first_name', 'last_name', 'email', 'phone', 'shipping_method_name', 'shipping_method_code'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    try {
                        DB::statement("ALTER TABLE orders MODIFY `$col` NULL");
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
