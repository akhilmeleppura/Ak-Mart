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
         Schema::create('billing_invoices_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('item_id');
            $table->json('taxes')->nullable()->comment('JSON array of tax IDs e.g. ["1","2"]');
            
            // Foreign key constraints
            $table->foreign('document_id')
                  ->references('id')
                  ->on('billing_invoices')
                  ->onDelete('cascade');
                  
            $table->foreign('item_id')
                  ->references('id')
                  ->on('billing_items')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_invoices_items');
    }
};
