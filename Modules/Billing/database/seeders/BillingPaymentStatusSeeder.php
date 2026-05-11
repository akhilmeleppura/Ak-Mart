<?php

namespace Modules\Billing\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillingPaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
 $statuses = [
            [
                'name' => 'Not Paid', 
                'value' => 0,
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Paid', 
                'value' => 1,
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Partially Paid', 
                'value' => 2,
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ];

        DB::table('billing_payment_status')->insert($statuses);
    }    }

