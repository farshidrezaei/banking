<?php

namespace App\Notifications;

use App\Library\StringHelper;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Channels\SmsChannel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CardToCardIncreaseBalanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Transaction $outcome;
    private Transaction $income;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly array $transactions)
    {
        $this->income = $this->transactions['income_transaction'];
        $this->outcome = $this->transactions['outcome_transaction'];
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(): \Illuminate\Foundation\Application|array|string|Translator|Application|null
    {
        return __('notification.card_to_card.increase', [
            'amount' => $this->income->amount,
            'destination_name' => $this->income->card->account->user->name,
            'destination_card_number' => StringHelper::maskString($this->income->card->number, 4, 8),
            'source_name' => $this->outcome->card->account->user->name,
            'source_card_number' => StringHelper::maskString($this->outcome->card->number, 4, 8),
            'done_at' => $this->income->done_at,
            'track_id' => $this->income->track_id,
        ]);
    }

    public function viaQueues(): array
    {
        return [
            SmsChannel::class => 'sms-queue'
        ];
    }


    public function failed(Exception $exception): void
    {
        Log::critical('Card To Card Increase Balance Notification Failed', [
            'exception' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'transaction_id' => $this->income->id,
        ]);
    }
}
