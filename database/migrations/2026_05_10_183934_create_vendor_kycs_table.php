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
        Schema::create('vendor_kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unique();
            $table->string('business_name');
            $table->string('business_type'); // sole_proprietor, llc, partnership, company
            $table->string('pan_number')->nullable();       // India: PAN
            $table->string('gst_number')->nullable();       // India: GST
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('document_type');                // aadhar, passport, driving_license, etc.
            $table->string('document_front_path');          // stored path in /storage
            $table->string('document_back_path')->nullable();
            $table->string('selfie_path')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // admin user id
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_kycs');
    }
};
