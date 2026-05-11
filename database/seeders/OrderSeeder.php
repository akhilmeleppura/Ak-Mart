<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Branch\Branch;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $branches = Branch::all();

        if ($products->isEmpty()) {
            return;
        }

        foreach (range(1, 10) as $index) {
            $user = $users->random();
            $branch = $branches->isNotEmpty() ? $branches->random() : null;
            
            $totalAmount = 0;
            $items = [];

            // Create 1-3 items per order
            foreach (range(1, rand(1, 3)) as $itemIndex) {
                $product = $products->random();
                $qty = rand(1, 5);
                $price = $product->price;
                $total = $price * $qty;
                
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'qty' => $qty,
                    'total' => $total,
                ];
                
                $totalAmount += $total;
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_status' => collect(['unpaid', 'paid'])->random(),
                'order_status' => collect(['pending', 'confirmed', 'completed'])->random(),
                'payment_method' => collect(['credit_card', 'paypal', 'cash'])->random(),
                'shipping_address' => 'Sample Shipping Address for ' . $user->name,
                'billing_address' => 'Sample Billing Address for ' . $user->name,
                'branch_id' => $branch ? $branch->id : null,
            ]);

            foreach ($items as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        }
    }
}
