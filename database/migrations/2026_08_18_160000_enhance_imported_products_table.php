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
        Schema::table('imported_products', function (Blueprint $table) {
            if (!Schema::hasColumn('imported_products', 'asin')) {
                $table->string('asin', 50)->nullable()->index()->after('source_url');
            }
            if (!Schema::hasColumn('imported_products', 'canonical_url')) {
                $table->text('canonical_url')->nullable()->after('asin');
            }
            if (!Schema::hasColumn('imported_products', 'domain')) {
                $table->string('domain', 100)->nullable()->after('canonical_url');
            }
            if (!Schema::hasColumn('imported_products', 'confidence_score')) {
                $table->unsignedTinyInteger('confidence_score')->default(0)->after('domain');
            }
            if (!Schema::hasColumn('imported_products', 'sources')) {
                $table->json('sources')->nullable()->after('confidence_score');
            }
            if (!Schema::hasColumn('imported_products', 'warnings')) {
                $table->json('warnings')->nullable()->after('sources');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imported_products', function (Blueprint $table) {
            $table->dropColumn([
                'asin',
                'canonical_url',
                'domain',
                'confidence_score',
                'sources',
                'warnings'
            ]);
        });
    }
};
