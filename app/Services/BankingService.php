<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\BalanceIsInsufficientException;
use App\Exceptions\TransactionFailedException;
use App\Models\Card;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class BankingService
{
    /**
     * @return array{income_transaction:Transaction,outcome_transaction:Transaction}
     * @throws Throwable
     */
    public static function cardToCard(Card $sourceCard, Card $destinationCard, int $amount): array
    {
        throw_if($sourceCard->account->balance < $amount, BalanceIsInsufficientException::class);

        $outcomeTransaction = $sourceCard->transactions()
            ->create([
                'amount' => $amount,
                'type' => TransactionTypeEnum::OUTCOME,
                'status' => TransactionStatusEnum::INIT,
                'track_id' => Str::uuid()->toString(),
            ]);

        try {
            DB::beginTransaction();

            $sourceCard->account()
                ->lockForUpdate()
                ->update([
                    'balance' => DB::raw("balance - ".($amount + ($fee = config('banking.fee_amount', 0))))
                ]);

            $destinationCard->account()
                ->lockForUpdate()
                ->update(['balance' => DB::raw("balance + ".$amount)]);

            $outcomeTransaction->update([
                'status' => TransactionStatusEnum::DONE,
                'done_at' => now()
            ]);

            $outcomeTransaction->fee()->create(['amount' => $fee]);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            Log::critical('card to card failed', [
                'exception' => get_class($exception),
                'exception_message' => $exception->getMessage(),
                'source' => $sourceCard->number,
                'destination' => $destinationCard->number,
                'amount' => $amount
            ]);

            $outcomeTransaction->update(['status' => TransactionStatusEnum::FAILED]);
            throw new TransactionFailedException($outcomeTransaction->track_id);
        }


        $incomeTransaction = $destinationCard->transactions()
            ->create([
                'amount' => $amount,
                'type' => TransactionTypeEnum::INCOME,
                'status' => TransactionStatusEnum::DONE,
                'track_id' => Str::uuid()->toString(),
            ]);

        return [
            'income_transaction' => $incomeTransaction,
            'outcome_transaction' => $outcomeTransaction,
        ];
    }


}
