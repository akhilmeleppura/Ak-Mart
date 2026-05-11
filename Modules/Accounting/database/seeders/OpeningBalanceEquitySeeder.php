<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\App\Models\ChartOfAccount;


class OpeningBalanceEquitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
    {
        ChartOfAccount::firstOrCreate(
            ['identifier' => 'opening_balance_equity'],
            [
                'account_name' => 'Opening Balance Equity',
                'main_category_id' => 0,
                'subcategory_id' => 0,
                'cumulative_debit' => 0,
                'cumulative_credit' => 0

            ]
        );
    }
}
