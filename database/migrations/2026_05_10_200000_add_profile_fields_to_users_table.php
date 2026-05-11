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
            $table->string('phone')->nullable()->after('email');
            $table->string('user_type')->default('customer')->after('phone');
            $table->string('address_line_1')->nullable()->after('user_type');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('town')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('town');
            $table->string('post_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('post_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'user_type',
                'address_line_1',
                'address_line_2',
                'town',
                'state',
                'post_code',
                'country'
            ]);
        });
    }
};
