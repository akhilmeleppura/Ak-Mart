<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Flash sale & rating columns on products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'flash_sale_end')) {
                $table->dateTime('flash_sale_end')->nullable()->after('deal_of_the_day');
            }
            if (!Schema::hasColumn('products', 'rating_cache')) {
                $table->decimal('rating_cache', 3, 2)->default(5.00)->after('flash_sale_end');
            }
        });

        // 2. Verified purchase column on reviews
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'is_verified_purchase')) {
                $table->boolean('is_verified_purchase')->default(true)->after('status');
            }
        });

        // 3. Email templates table
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->string('subject');
                $table->text('body');
                $table->json('variables')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['flash_sale_end', 'rating_cache']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['is_verified_purchase']);
        });

        Schema::dropIfExists('email_templates');
    }
};
