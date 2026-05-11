<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . fake()->unique()->numberBetween(100000, 999999),
            'total_amount' => fake()->randomFloat(2, 50, 1000),
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'partially_paid']),
            'order_status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'wallet']),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
