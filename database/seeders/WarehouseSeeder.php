<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $warehouses = [
            [
                'id' => 1,
                'code' => 'WH-NYC-01',
                'name' => 'Central Logistics & Distribution Hub',
                'address' => '500 Logistics Blvd, Jersey City, NJ 07305',
                'city' => 'New York Metro',
                'contact_person' => 'Robert Callahan',
                'phone' => '+1-201-555-0182',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'code' => 'WH-LON-02',
                'name' => 'Thames Cold Chain & Ambient Depot',
                'address' => 'Unit 12, River Way Industrial Estate, London SE10 0BE',
                'city' => 'London',
                'contact_person' => 'George Harrison',
                'phone' => '+44-20-7946-0441',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'code' => 'WH-DXB-03',
                'name' => 'JAFZA Rapid Fulfillment Terminal',
                'address' => 'Plot 48, Jebel Ali Free Zone North, Dubai, UAE',
                'city' => 'Dubai',
                'contact_person' => 'Farhan Qureshi',
                'phone' => '+971-4-881-2299',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $wh) {
            DB::table('warehouses')->updateOrInsert(
                ['code' => $wh['code']],
                array_merge($wh, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
