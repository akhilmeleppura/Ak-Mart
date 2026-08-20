<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add driver reference and status enum to orders table.
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                // Nullable driver foreign key
                if (!Schema::hasColumn('orders', 'driver_id')) {
                    $table->unsignedBigInteger('driver_id')->nullable()->after('order_status');
                    $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'driver_id')) {
                    $table->dropForeign(['driver_id']);
                    $table->dropColumn('driver_id');
                }
            });
        }
    }
};
