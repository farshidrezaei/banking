<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'number' => $this->faker->unique()->creditCardNumber,
            'cvv2' => $this->faker->numerify(),
            'expires_at' => now()->addYears(5),
            'password' => Hash::make('password'),
        ];
    }
}
