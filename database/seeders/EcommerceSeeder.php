<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\Category::factory(5)->create();
        
        $products = \App\Models\Product::factory(20)->recycle($categories)->create();

        \App\Models\Order::factory(50)->create()->each(function ($order) use ($products) {
            $orderItems = $products->random(rand(1, 3));
            $total = 0;
            foreach ($orderItems as $product) {
                $qty = rand(1, 3);
                $itemTotal = $product->price * $qty;
                $total += $itemTotal;
                \App\Models\OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                    'total' => $itemTotal,
                ]);
            }
            $order->update(['total_amount' => $total]);
        });

        // Seed Demo Coupons
        $coupons = [
            ['code' => 'WELCOME10', 'type' => 'percentage', 'value' => 10.00, 'usage_limit' => 500, 'usage_count' => 124, 'is_active' => true],
            ['code' => 'SUMMER20', 'type' => 'percentage', 'value' => 20.00, 'usage_limit' => 200, 'usage_count' => 88, 'is_active' => true],
            ['code' => 'FREESHIP50', 'type' => 'fixed', 'value' => 15.00, 'usage_limit' => 1000, 'usage_count' => 412, 'is_active' => true],
            ['code' => 'VIP25', 'type' => 'percentage', 'value' => 25.00, 'usage_limit' => 50, 'usage_count' => 19, 'is_active' => true],
            ['code' => 'AKMART100', 'type' => 'fixed', 'value' => 100.00, 'usage_limit' => 100, 'usage_count' => 45, 'is_active' => true],
        ];

        foreach ($coupons as $coupon) {
            \App\Models\Coupon::updateOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}
