<?php

namespace App\Notifications\Channels;

use App\Facades\Sms;
use App\Models\User;
use App\Notifications\CardToCardIncreaseBalanceNotification;

class SmsChannel
{
    /**
     * @param User $notifiable
     * @param CardToCardIncreaseBalanceNotification $notification
     */
    public function send(User $notifiable, CardToCardIncreaseBalanceNotification $notification): void
    {
        Sms::sendSms($notifiable->routeNotificationForSms(), $notification->toSms());
    }
}
