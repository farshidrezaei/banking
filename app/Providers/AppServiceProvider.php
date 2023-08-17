<?php

namespace App\Providers;

use App\Facades\Sms;
use App\Managers\Sms\AbstractSmsProvider;
use App\Managers\Sms\Providers\Ghasedak;
use App\Managers\Sms\Providers\Kavehnegar;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sms::extend(
            driver: 'kavehnegar',
            callback: static fn(): AbstractSmsProvider => app(Kavehnegar::class)
        );
        Sms::extend(
            driver: 'ghasedak',
            callback: static fn(): AbstractSmsProvider => app(Ghasedak::class)
        );
    }
}
