<?php

namespace Tests\Feature;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\BalanceIsInsufficientException;
use App\Exceptions\TransactionFailedException;
use App\Models\Account;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery;
use Tests\TestCase;

class BankingControllerTest extends TestCase
{

    public function testCardToCardSuccessfully(): void
    {
        Notification::fake();

        $fee = config('banking.fee_amount');
        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstSourceBalance = 100000000])->create())
            ->create();
        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstDestinationBalance = 100000])->create())
            ->create();


        $response = $this->postJson(route('v1.banking.card-to-card'), [
            'source_card_number' => $sourceCard->number,
            'destination_card_number' => $destinationCard->number,
            'amount' => $amount = 10000,
        ]);

        $response->assertSuccessful();
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has('income_transaction.track_id')
                ->has('outcome_transaction.track_id')
                ->has('income_transaction.done_at')
                ->has('outcome_transaction.done_at')
                ->where('income_transaction.status', TransactionStatusEnum::DONE->value)
                ->where('outcome_transaction.status', TransactionStatusEnum::DONE->value)
                ->etc()
        );
        $this->assertDatabaseHas(Transaction::class, [
            'card_id' => $sourceCard->id,
            'amount' => $amount,
            'type' => TransactionTypeEnum::OUTCOME,
            'status' => TransactionStatusEnum::DONE->value
        ]);
        $this->assertDatabaseHas(Transaction::class, [
            'card_id' => $destinationCard->id,
            'amount' => $amount,
            'type' => TransactionTypeEnum::INCOME,
            'status' => TransactionStatusEnum::DONE->value
        ]);
        $this->assertDatabaseCount(Fee::class, 1);

        $this->assertEquals($firstSourceBalance - $amount - $fee, $sourceCard->account->balance);
        $this->assertEquals($firstDestinationBalance + $amount, $destinationCard->account->balance);
        Notification::assertCount(2);
    }

    public function testCardToCardFailedOnInvalidCardNumber(): void
    {
        Notification::fake();


        $response = $this->postJson(route('v1.banking.card-to-card'), [
            'source_card_number' => '1234123412341234',
            'destination_card_number' => '4321432143214321',
            'amount' => 10000,
        ]);

        $response->assertUnprocessable();

        Notification::assertCount(0);
    }

    public function testCardToCardFailedOnInvalidAmount(): void
    {
        Notification::fake();

        $fee = config('banking.fee_amount');
        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstSourceBalance = 100000000])->create())
            ->create();
        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstDestinationBalance = 100000])->create())
            ->create();


        $response = $this->postJson(route('v1.banking.card-to-card'), [
            'source_card_number' => $sourceCard->number,
            'destination_card_number' => $destinationCard->number,
            'amount' => $amount = 10,
        ]);


        $response->assertUnprocessable();
        $this->assertDatabaseMissing(Transaction::class, [
            'source_card_id' => $sourceCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => $amount,
            'status' => TransactionStatusEnum::DONE->value
        ]);

        $this->assertEquals($firstSourceBalance, $sourceCard->account->balance);
        $this->assertEquals($firstDestinationBalance, $destinationCard->account->balance);


        Notification::assertCount(0);
    }

    public function testCardToCardFailedOnBalanceInsufficient(): void
    {
        Notification::fake();

        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstSourceBalance = 50000])->create())
            ->create();
        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstDestinationBalance = 100000])->create())
            ->create();


        $response = $this->postJson(route('v1.banking.card-to-card'), [
            'source_card_number' => $sourceCard->number,
            'destination_card_number' => $destinationCard->number,
            'amount' => $amount = 5000000,
        ]);


        $response->assertForbidden();
        $this->assertEquals(BalanceIsInsufficientException::class, get_class($response->exception));
        $this->assertDatabaseMissing(Transaction::class, [
            'card_id' => $sourceCard->id,
            'amount' => $amount,
            'type' => TransactionTypeEnum::OUTCOME,
            'status' => TransactionStatusEnum::DONE->value
        ]);
        $this->assertDatabaseMissing(Transaction::class, [
            'card_id' => $destinationCard->id,
            'amount' => $amount,
            'type' => TransactionTypeEnum::INCOME,
            'status' => TransactionStatusEnum::DONE->value
        ]);
        $this->assertDatabaseCount(Fee::class, 0);

        $this->assertEquals($firstSourceBalance, $sourceCard->account->balance);
        $this->assertEquals($firstDestinationBalance, $destinationCard->account->balance);
        Notification::assertCount(0);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCardToCardFailedOnDbException(): void
    {
        $dbMock = Mockery::mock('alias:'.DB::class);
        $dbMock->shouldReceive('beginTransaction')->andThrow(Exception::class)->once();
        $dbMock->shouldReceive('rollback')->once();
        Notification::fake();

        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => 500000000])->create())
            ->create();
        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => 100000])->create())
            ->create();


        $response = $this->postJson(route('v1.banking.card-to-card'), [
            'source_card_number' => $sourceCard->number,
            'destination_card_number' => $destinationCard->number,
            'amount' => $amount = 5000000,
        ]);


        $response->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE);

        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has('track_id')
                ->etc()
        );

        $this->assertEquals(TransactionFailedException::class, get_class($response->exception));
        $this->assertDatabaseHas(Transaction::class, [
            'card_id' => $sourceCard->id,
            'amount' => $amount,
            'status' => TransactionStatusEnum::FAILED->value,
            'type' => TransactionTypeEnum::OUTCOME->value
        ]);

        Notification::assertCount(0);
    }


    public function testTopUserWorkSuccessfully(): void
    {
        $this->seed();

        $response = $this->getJson(route('v1.banking.top-users'));

        $response->assertSuccessful();

        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has('top_users.0.last_transactions.0.card_number')
                ->has('top_users.0.name')
                ->etc()
        );

        $this->assertGreaterThanOrEqual(
            $response->json('top_users.1.transactions_count'),
            $response->json('top_users.0.transactions_count')
        );
    }

}
