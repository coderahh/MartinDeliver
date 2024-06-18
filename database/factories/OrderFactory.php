<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'pickup_name' => $this->faker->name,
            'pickup_mobile' => $this->faker->phoneNumber,
            'pickup_address' => $this->faker->address,
            'pickup_lat' => $this->faker->latitude,
            'pickup_long' => $this->faker->longitude,
            'delivery_name' => $this->faker->name,
            'delivery_mobile' => $this->faker->phoneNumber,
            'delivery_address' => $this->faker->address,
            'delivery_lat' => $this->faker->latitude,
            'delivery_long' => $this->faker->longitude,
            'status' => Order::STATUS_PENDING,
        ];
    }
}
