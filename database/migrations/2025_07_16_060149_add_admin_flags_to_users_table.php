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
        Schema::table('users', function (Blueprint $table) {
      $table->boolean('is_supreme_admin')->default(0)->after('remember_token');
            $table->boolean('is_super_admin')->default(0)->after('is_supreme_admin');

            // Add role_id column and foreign key constraint
            $table->unsignedBigInteger('role_id')->nullable()->after('is_super_admin');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'is_supreme_admin', 'is_super_admin']);        });
    }
};
