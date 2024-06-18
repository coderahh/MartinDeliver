<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => Str::uuid()->toString(),
            'name' => $this->faker->name,
            'mobile' => $this->faker->unique()->phoneNumber,
            'mobile_verified_at' => now(),
            'role' => $this->faker->randomElement([User::ROLE_CLIENT, User::ROLE_COURIER]),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'webhook_url' => $this->faker->url,
        ];
    }
}
