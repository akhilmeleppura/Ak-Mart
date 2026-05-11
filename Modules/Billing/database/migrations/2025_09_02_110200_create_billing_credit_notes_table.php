<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('billing_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('document_prefix');
            $table->string('document_number');
            $table->foreignId('customer_id')->constrained('customers');
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->string('document_discount_type')->nullable();
            $table->decimal('document_discount_rate', 15, 2)->default(0);
            $table->decimal('document_discount_amount', 15, 2)->default(0);
            $table->foreignId('document_tax_id')->nullable()->constrained('taxes');
            $table->text('note')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->unique(['document_prefix', 'document_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('billing_credit_notes');
    }
};