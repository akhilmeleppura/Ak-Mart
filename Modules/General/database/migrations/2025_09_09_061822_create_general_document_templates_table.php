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
        Schema::create('general_document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // unique identifier
            $table->string('name')->unique();
            $table->string('type')->nullable();
            $table->string('header_image')->nullable();
            $table->string('footer_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_document_templates');
    }
};
