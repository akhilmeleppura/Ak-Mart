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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->enum('provider', ['gemini', 'openai', 'claude'])->default('gemini');
            $table->string('assistant_name')->nullable();
            $table->text('assistant_prompt')->nullable();
            $table->string('gemini_api_key')->nullable();
            $table->string('gemini_model')->nullable();
            $table->float('gemini_temperature')->nullable();
            $table->integer('gemini_max_tokens')->nullable();
            $table->string('openai_api_key')->nullable();
            $table->string('openai_model')->nullable();
            $table->float('openai_temperature')->nullable();
            $table->integer('openai_max_tokens')->nullable();
            $table->string('claude_api_key')->nullable();
            $table->string('claude_model')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
