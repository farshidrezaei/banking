<?php

namespace App\Http\Controllers\API\V1\Card;

use App\Events\CardToCardDoneEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CardToCardRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Card;
use App\Services\BankingService;
use Illuminate\Http\JsonResponse;
use Throwable;

class BankingController extends Controller
{
    /**
     * @throws Throwable
     */
    public function cardToCard(CardToCardRequest $request): JsonResponse
    {
        $sourceCard = Card::query()->with('account.user')
            ->whereNumber($request->validated('source_card_number'))
            ->firstOrFail();

        $destinationCard = Card::query()->with('account.user')
            ->whereNumber($request->validated('destination_card_number'))
            ->firstOrFail();

        $transactions = BankingService::cardToCard(
            sourceCard: $sourceCard,
            destinationCard: $destinationCard,
            amount: $request->validated('amount')
        );

        CardToCardDoneEvent::dispatch($transactions);

        return new  JsonResponse([
            'income_transaction' => TransactionResource::make($transactions['income_transaction']),
            'outcome_transaction' => TransactionResource::make($transactions['outcome_transaction'])
        ]);
    }


}
