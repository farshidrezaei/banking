<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardToCardDoneEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @param  array{income_transaction:Transaction,outcome_transaction:Transaction}  $transactions
     */
    public function __construct(public array $transactions)
    {
        //
    }

}
