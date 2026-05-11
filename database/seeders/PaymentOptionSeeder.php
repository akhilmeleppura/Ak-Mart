<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentOptions = [
            [
                'name' => 'Credit Card',
                'slug' => 'credit-card',
                'description' => 'Pay with your Visa, Mastercard, or Amex.',
                'gateway' => 'stripe',
                'is_active' => true,
            ],
            [
                'name' => 'PayPal',
                'slug' => 'paypal',
                'description' => 'Pay securely with your PayPal account.',
                'gateway' => 'paypal',
                'is_active' => true,
            ],
            [
                'name' => 'Bank Transfer',
                'slug' => 'bank-transfer',
                'description' => 'Direct bank wire transfer. May take 2-3 business days.',
                'gateway' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Cash on Delivery',
                'slug' => 'cash-on-delivery',
                'description' => 'Pay when you receive your order.',
                'gateway' => null,
                'is_active' => false, // Example of a disabled option
            ],
        ];

        DB::table('payment_options')->insert($paymentOptions);
    }
}
