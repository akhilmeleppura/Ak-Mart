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
        Schema::table('general_document_templates', function (Blueprint $table) {
            $table->integer('header_height')->nullable()->after('header_image');
            $table->integer('footer_height')->nullable()->after('footer_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_document_templates', function (Blueprint $table) {
            $table->dropColumn(['header_height', 'footer_height']);
        });
    }
};
