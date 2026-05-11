<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'product_id' => \App\Models\Product::factory(),
            'product_name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 500),
            'qty' => fake()->numberBetween(1, 5),
            'total' => fake()->randomFloat(2, 10, 500),
        ];
    }
}
