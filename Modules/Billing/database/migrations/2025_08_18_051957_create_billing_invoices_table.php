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
       
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->date('issue_date');
            $table->decimal('sub_total', 10, 2);
            $table->tinyInteger('document_discount_type')->nullable()->comment('1=percentage, 2=amount');
            $table->float('document_discount_rate')->nullable()->comment('Percentage value if type is 1');
            $table->decimal('document_discount_amount', 10, 2)->nullable();
            $table->tinyInteger('payment_status')->default(0)->comment('0=not paid, 1=paid, 2=partially paid');
            
            // Foreign key constraint
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('restrict');
                  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_invoices');
    }
};
