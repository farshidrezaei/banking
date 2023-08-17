<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @throws \Exception
     */
    public function run(): void
    {
        User::factory(10)->create() // create 10 user
        ->each(fn(User $user) => Account::factory(3) // create 3 account for each user
        ->create(['user_id' => $user->id])
            ->each(fn(Account $account) => Card::factory(2) // create 2 card for each account
            ->create(['account_id' => $account->id])
                ->each(fn(Card $card) => Transaction::factory(200)
                    ->create([
                        'card_id' => $card->id,
                        'done_at' => now()->subMinutes(random_int(1, 20)), // random done date for better realistic result
                    ]) // create 200 transaction for each card
                    ->each(fn(Transaction $transaction) => Fee::factory(1)
                        ->create([
                            'transaction_id' => $transaction->id,
                            'amount' => config('banking.fee_amount')
                        ]) // create fee for each transaction
                    )
                )
            )
        );
    }
}
