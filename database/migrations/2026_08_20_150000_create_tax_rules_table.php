<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tax_rules')) {
            Schema::create('tax_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('tax_class')->default('standard'); // standard, reduced, zero_rate, grocery, electronics
                $table->string('tax_type')->default('percentage'); // percentage, fixed
                $table->decimal('rate', 6, 2)->default(5.00);
                $table->string('country_code', 10)->default('*'); // e.g. US, AE, IN, GB, *
                $table->string('state_name')->nullable()->default('*'); // e.g. CA, NY, Dubai, *
                $table->string('postal_code_pattern')->nullable(); // e.g. 900*, 10001
                $table->boolean('is_compound')->default(false); // tax on tax
                $table->integer('priority')->default(1);
                $table->boolean('is_active')->default(true);
                $table->string('calculation_mode')->default('exclusive'); // exclusive (added at checkout), inclusive (in price)
                $table->timestamps();
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'tax_class')) {
                    $table->string('tax_class')->default('standard')->after('price');
                }
                if (!Schema::hasColumn('products', 'tax_rate')) {
                    $table->decimal('tax_rate', 6, 2)->nullable()->after('tax_class');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'tax_amount')) {
                    $table->decimal('tax_amount', 10, 2)->default(0.00)->nullable();
                }
                if (!Schema::hasColumn('orders', 'tax_breakdown')) {
                    $table->json('tax_breakdown')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
