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
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'max_discount')) {
                $table->decimal('max_discount', 10, 2)->nullable()->after('max_spend');
            }
            if (!Schema::hasColumn('coupons', 'description')) {
                $table->string('description')->nullable()->after('code');
            }
            if (!Schema::hasColumn('coupons', 'first_order_only')) {
                $table->boolean('first_order_only')->default(false)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'max_discount')) {
                $table->dropColumn('max_discount');
            }
            if (Schema::hasColumn('coupons', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('coupons', 'first_order_only')) {
                $table->dropColumn('first_order_only');
            }
        });
    }
};
