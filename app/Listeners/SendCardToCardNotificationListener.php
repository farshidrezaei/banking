<?php

namespace App\Listeners;

use App\Events\CardToCardDoneEvent;
use App\Notifications\CardToCardDecreaseBalanceNotification;
use App\Notifications\CardToCardIncreaseBalanceNotification;

class SendCardToCardNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CardToCardDoneEvent $event): void
    {
        $event->transactions['outcome_transaction']
            ->card
            ->account
            ->user
            ->notify(new CardToCardDecreaseBalanceNotification($event->transactions));

        $event->transactions['income_transaction']
            ->card
            ->account
            ->user
            ->notify(new CardToCardIncreaseBalanceNotification($event->transactions));
    }
}
