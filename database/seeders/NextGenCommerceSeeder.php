<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\DeliverySlot;
use App\Models\B2bCompany;
use App\Models\B2bBuyer;
use App\Models\B2bTierPrice;
use App\Models\GiftCard;
use App\Models\WebhookSubscription;
use App\Models\Product;
use App\Models\User;

class NextGenCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Warehouses
        $wh1 = Warehouse::firstOrCreate(
            ['code' => 'WH-LDN-01'],
            [
                'name'           => 'Central London Distribution Center',
                'address'        => 'Unit 4, Gateway Business Park, Royal Docks',
                'city'           => 'London',
                'contact_person' => 'James Harrison',
                'phone'          => '+44 20 7946 0912',
                'is_active'      => true,
            ]
        );

        $wh2 = Warehouse::firstOrCreate(
            ['code' => 'WH-MAN-02'],
            [
                'name'           => 'Manchester Regional Logistics Hub',
                'address'        => 'Bay 12, Trafford Industrial Estate',
                'city'           => 'Manchester',
                'contact_person' => 'Sarah Walker',
                'phone'          => '+44 161 496 0382',
                'is_active'      => true,
            ]
        );

        // Allocate sample stock
        $products = Product::all();
        foreach ($products as $p) {
            WarehouseStock::firstOrCreate(
                ['warehouse_id' => $wh1->id, 'product_id' => $p->id],
                ['qty' => 50, 'bin_location' => 'AISLE-A1']
            );
            WarehouseStock::firstOrCreate(
                ['warehouse_id' => $wh2->id, 'product_id' => $p->id],
                ['qty' => 30, 'bin_location' => 'AISLE-B2']
            );
        }

        // 2. Delivery Slots
        DeliverySlot::firstOrCreate(
            ['name' => 'Morning Priority Slot (09:00 AM - 01:00 PM)'],
            [
                'start_time'     => '09:00:00',
                'end_time'       => '13:00:00',
                'max_orders'     => 25,
                'days_available' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active'      => true,
            ]
        );

        DeliverySlot::firstOrCreate(
            ['name' => 'Evening Express Slot (02:00 PM - 06:00 PM)'],
            [
                'start_time'     => '14:00:00',
                'end_time'       => '18:00:00',
                'max_orders'     => 30,
                'days_available' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'is_active'      => true,
            ]
        );

        // 3. B2B Corporate Accounts
        $b2b = B2bCompany::firstOrCreate(
            ['company_code' => 'B2B-APEX-01'],
            [
                'name'            => 'Apex Commerce Supplies Ltd',
                'contact_email'   => 'purchasing@apexsupplies.co.uk',
                'contact_phone'   => '+44 20 8912 3456',
                'tax_id'          => 'GB987654321',
                'credit_limit'    => 15000.00,
                'current_balance' => 2450.00,
                'payment_terms'   => 'net_30',
                'billing_address' => '72 High Holborn, London WC1V 6RL',
                'status'          => 'active',
            ]
        );

        $adminUser = User::first();
        if ($adminUser) {
            B2bBuyer::firstOrCreate(
                ['b2b_company_id' => $b2b->id, 'user_id' => $adminUser->id],
                [
                    'role'               => 'admin',
                    'spending_limit'     => 10000.00,
                    'can_approve_orders' => true,
                ]
            );
        }

        if ($products->count() > 0) {
            B2bTierPrice::firstOrCreate(
                ['product_id' => $products->first()->id, 'b2b_company_id' => $b2b->id, 'min_qty' => 10],
                ['unit_price' => max(1.0, (float)$products->first()->price * 0.80)]
            );
        }

        // 4. Sample Gift Cards
        GiftCard::firstOrCreate(
            ['code' => 'GC-WELCOME-100'],
            [
                'initial_balance' => 100.00,
                'current_balance' => 100.00,
                'currency'        => 'USD',
                'recipient_email' => 'vip@ak-mart.com',
                'pin'             => '4892',
                'expiry_date'     => now()->addYear(),
                'is_active'       => true,
            ]
        );

        // 5. Sample Webhook Subscription
        WebhookSubscription::firstOrCreate(
            ['name' => 'Internal ERP Sync Hook'],
            [
                'target_url' => 'https://webhook.site/akmart-test-hook',
                'secret'     => 'whsec_sample_secret_key_123',
                'events'     => ['order.created', 'order.paid', 'order.shipped'],
                'is_active'  => true,
            ]
        );
    }
}
