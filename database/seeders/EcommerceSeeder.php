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
    }
}
