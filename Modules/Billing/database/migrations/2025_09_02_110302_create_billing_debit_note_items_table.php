<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('billing_debit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('billing_debit_notes');
            $table->foreignId('item_id')->constrained('billing_items');
            $table->text('description')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('selling_unit_price', 15, 2)->default(0);
            $table->foreignId('tax_id')->nullable()->constrained('taxes');
            $table->decimal('discount_rate', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('branch_id')->constrained('branches');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('billing_debit_note_items');
    }
};