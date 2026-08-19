<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Merchandising columns on Products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('products', 'is_trending')) {
                $table->boolean('is_trending')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('products', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_trending');
            }
            if (!Schema::hasColumn('products', 'is_new_arrival')) {
                $table->boolean('is_new_arrival')->default(false)->after('is_best_seller');
            }
            if (!Schema::hasColumn('products', 'deal_of_the_day')) {
                $table->boolean('deal_of_the_day')->default(false)->after('is_new_arrival');
            }
        });

        // 2. Merchandising & Media columns on Categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('image');
            }
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('categories', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_featured');
            }
        });

        // 3. Slider enhancements on CMS Banners
        Schema::table('cms_banners', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_banners', 'badge_text')) {
                $table->string('badge_text')->nullable()->after('subtitle');
            }
            if (!Schema::hasColumn('cms_banners', 'bg_color')) {
                $table->string('bg_color')->nullable()->after('position');
            }
            if (!Schema::hasColumn('cms_banners', 'mobile_image')) {
                $table->string('mobile_image')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_trending', 'is_best_seller', 'is_new_arrival', 'deal_of_the_day']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'icon', 'is_featured', 'sort_order']);
        });

        Schema::table('cms_banners', function (Blueprint $table) {
            $table->dropColumn(['badge_text', 'bg_color', 'mobile_image']);
        });
    }
};
