<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class AccountMainCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
{
    $timestamp = Carbon::now();

    DB::table('accounting_main_categories')->insert([
        [
            'name' => 'Asset',
            'type' => 'Debit',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'name' => 'Income',
            'type' => 'Credit',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'name' => 'Expense',  // Fixed typo from "Expence" to "Expense"
            'type' => 'Debit',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'name' => 'Liability',
            'type' => 'Credit',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
    ]);
}
}
