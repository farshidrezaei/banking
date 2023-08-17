<?php

namespace Database\Factories;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $failed = $this->faker->boolean(90);
        return [
            'card_id' => Card::factory(),
            'type' => $this->faker->randomElement(TransactionTypeEnum::values()),
            'status' => $failed ? TransactionStatusEnum::FAILED : TransactionStatusEnum::DONE,
            'amount' => $this->faker->biasedNumberBetween(100000, 99999999),
            'track_id' => $failed ? null : Str::uuid()->toString(),
            'done_at' => $failed ? null : now(),
        ];
    }
}
