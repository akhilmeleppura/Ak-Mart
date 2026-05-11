<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 0. Ensure Roles Exist
        $this->call(RolesPermissionsSeeder::class);

        // 1. Create Branches with Unique Codes
        $branches = [
            ['name' => 'Global HQ (New York)', 'code' => 'NY-01', 'address' => '5th Ave, NY'],
            ['name' => 'London Flagship', 'code' => 'LON-05', 'address' => 'Oxford St, London'],
            ['name' => 'Dubai Mall Branch', 'code' => 'DXB-09', 'address' => 'Downtown Dubai'],
        ];

        $branchModels = [];
        foreach ($branches as $b) {
            $branchModels[] = Branch::updateOrCreate(['name' => $b['name']], $b);
        }

        // 2. Create Admin Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'branch_id' => $branchModels[0]->id,
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Super Admin']);

        $manager = User::updateOrCreate(
            ['email' => 'manager@branch.com'],
            [
                'name' => 'Branch Manager',
                'password' => Hash::make('manager123'),
                'branch_id' => $branchModels[1]->id,
                'email_verified_at' => now(),
            ]
        );
        $manager->syncRoles(['Branch Manager']);

        // 3. Create Categories
        $categories = ['Electronics', 'Fashion', 'Home Decor', 'Accessories'];
        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[] = Category::updateOrCreate(
                ['slug' => Str::slug($cat)],
                [
                    'name' => $cat,
                    'branch_id' => $branchModels[0]->id 
                ]
            );
        }

        // 4. Create Products for each branch
        foreach ($branchModels as $branch) {
            foreach ($catModels as $category) {
                for ($i = 1; $i <= 3; $i++) {
                    $name = $category->name . " Product " . $i . " (" . $branch->name . ")";
                    $product = Product::updateOrCreate(
                        ['slug' => Str::slug($name) . '-' . $branch->id],
                        [
                            'name' => $name,
                            'description' => "Premium " . $category->name . " item with excellent quality and durability.",
                            'price' => rand(50, 500),
                            'compare_at_price' => rand(600, 1000),
                            'qty' => rand(20, 100),
                            'sku' => strtoupper(substr($category->name, 0, 3)) . "-" . rand(1000, 9999) . "-" . $branch->id,
                            'category_id' => $category->id,
                            'branch_id' => $branch->id,
                            'is_active' => true,
                            'meta_title' => "Buy " . $name . " | Best Online Store",
                            'meta_description' => "Get the best deals on " . $name . ". High quality " . $category->name . " available now.",
                        ]
                    );

                    // Add Variations
                    $product->variants()->delete();
                    $product->variants()->createMany([
                        [
                            'attribute_name' => 'Size',
                            'attribute_value' => 'Large',
                            'price' => $product->price + 20,
                            'qty' => 10,
                            'sku' => $product->sku . '-L'
                        ],
                        [
                            'attribute_name' => 'Color',
                            'attribute_value' => 'Premium Black',
                            'price' => $product->price + 5,
                            'qty' => 15,
                            'sku' => $product->sku . '-BLK'
                        ]
                    ]);
                }
            }

            // 5. Create Demo Orders for Analytics
            for ($j = 1; $j <= 10; $j++) {
                $orderNumber = 'ORD-' . strtoupper(Str::random(4)) . '-' . $branch->id . '-' . $j;
                $order = Order::updateOrCreate(
                    ['order_number' => $orderNumber],
                    [
                        'total_amount' => rand(100, 2000),
                        'order_status' => 'Delivered',
                        'payment_status' => 'Paid',
                        'branch_id' => $branch->id,
                        'created_at' => now()->subDays(rand(0, 30)),
                    ]
                );

                // Random Item
                $randomProduct = Product::where('branch_id', $branch->id)->inRandomOrder()->first();
                if ($randomProduct) {
                    $order->items()->delete();
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $randomProduct->id,
                        'product_name' => $randomProduct->name,
                        'qty' => $qty = rand(1, 3),
                        'price' => $randomProduct->price,
                        'total' => $randomProduct->price * $qty,
                    ]);
                }
            }
        }
    }
}
