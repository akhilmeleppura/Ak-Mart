<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Product Attributes (EAV)
        if (!Schema::hasTable('product_attributes')) {
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('type')->default('select'); // text, number, select, color, boolean
                $table->boolean('is_filterable')->default(true);
                $table->boolean('is_required')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Attribute Values
        if (!Schema::hasTable('attribute_values')) {
            Schema::create('attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
                $table->string('value');
                $table->string('color_code')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Product Attribute Values Pivot
        if (!Schema::hasTable('product_attribute_values')) {
            Schema::create('product_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
                $table->foreignId('attribute_value_id')->nullable()->constrained('attribute_values')->onDelete('cascade');
                $table->text('custom_value')->nullable();
                $table->timestamps();
            });
        }

        // 4. CMS Banners & Promotional Sliders
        if (!Schema::hasTable('cms_banners')) {
            Schema::create('cms_banners', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->string('image')->nullable();
                $table->string('link_url')->nullable();
                $table->string('button_text')->default('Shop Now');
                $table->string('position')->default('home_hero'); // home_hero, promo_banner, sidebar
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 5. CMS Static Pages & FAQ
        if (!Schema::hasTable('cms_pages')) {
            Schema::create('cms_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // 6. Newsletter Subscribers
        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('status')->default('subscribed'); // subscribed, unsubscribed
                $table->string('source')->default('storefront');
                $table->timestamp('subscribed_at')->useCurrent();
                $table->timestamp('unsubscribed_at')->nullable();
                $table->timestamps();
            });
        }

        // 7. SEO 301 Redirect Rules
        if (!Schema::hasTable('redirect_rules')) {
            Schema::create('redirect_rules', function (Blueprint $table) {
                $table->id();
                $table->string('from_url');
                $table->string('to_url');
                $table->integer('status_code')->default(301);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('redirect_rules');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('cms_banners');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
