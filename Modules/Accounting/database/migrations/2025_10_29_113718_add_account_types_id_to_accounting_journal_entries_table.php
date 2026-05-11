<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting_journal_entries', function (Blueprint $table) {
            // Add the new column. We assume it's an unsigned big integer,
            // which is standard for foreign keys.
            // We also make it nullable if there is existing data in the table.
            $table->unsignedBigInteger('account_types_id')->nullable()->after('chart_of_account_id');

            // Optionally, add a foreign key constraint to ensure data integrity
            $table->foreign('account_types_id')
                  ->references('id')
                  ->on('account_types')
                  ->onDelete('set null'); // Or onDelete('cascade') depending on your needs
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting_journal_entries', function (Blueprint $table) {
            // First, drop the foreign key constraint
            $table->dropForeign(['account_types_id']);
            
            // Then, drop the column
            $table->dropColumn('account_types_id');
        });
    }
};