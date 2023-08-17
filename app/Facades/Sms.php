<?php

namespace App\Facades;

use App\Managers\Sms\SmsProviderManager;
use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SmsProviderManager driver(string $driver);
 * @method static SmsProviderManager extend(string $driver, Closure $callback);
 * @method static void sendSms(string $number, string $message);
 */
class Sms extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return SmsProviderManager::class;
    }
}