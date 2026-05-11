<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch\Branch;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $b1 = Branch::create(['name' => 'Main Branch', 'address' => 'Downtown']);
        $b2 = Branch::create(['name' => 'Sub Branch', 'address' => 'Uptown']);

        Product::query()->update(['branch_id' => $b1->id]);
        Order::query()->update(['branch_id' => $b1->id]);
        Category::query()->update(['branch_id' => $b1->id]);
    }
}
