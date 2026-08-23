<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('search_query_logs')) {
            Schema::create('search_query_logs', function (Blueprint $table) {
                $table->id();
                $table->string('query');
                $table->string('cleaned_query')->nullable();
                $table->integer('results_count')->default(0);
                $table->boolean('is_zero_result')->default(false)->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('locale', 10)->default('en');
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('search_query_logs');
    }
};
