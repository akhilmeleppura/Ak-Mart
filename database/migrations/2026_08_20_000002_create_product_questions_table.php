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
        if (!Schema::hasTable('product_questions')) {
            Schema::create('product_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('question');
                $table->text('answer')->nullable();
                $table->foreignId('answered_by_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('answered_at')->nullable();
                $table->boolean('is_published')->default(true);
                $table->integer('upvotes')->default(0);
                $table->timestamps();

                $table->index(['product_id', 'is_published']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_questions');
    }
};
