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
        Schema::create('maps_settings', function (Blueprint $table) {
            $table->id();
            $table->string('google_api_key')->nullable();
            $table->decimal('default_lat', 10, 7)->default(0.0);
            $table->decimal('default_lng', 10, 7)->default(0.0);
            $table->integer('default_zoom')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maps_settings');
    }
};
