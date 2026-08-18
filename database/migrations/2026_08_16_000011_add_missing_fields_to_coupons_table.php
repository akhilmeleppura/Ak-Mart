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
            if (!Schema::hasColumn('coupons', 'type')) {
                $table->string('type')->default('fixed')->after('code');
            }
            if (!Schema::hasColumn('coupons', 'value')) {
                $table->decimal('value', 10, 2)->default(0.00)->after('type');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit')) {
                $table->integer('usage_limit')->nullable()->after('value');
            }
            if (!Schema::hasColumn('coupons', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('usage_limit');
            }
            if (!Schema::hasColumn('coupons', 'min_spend')) {
                $table->decimal('min_spend', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('coupons', 'max_spend')) {
                $table->decimal('max_spend', 10, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
};
