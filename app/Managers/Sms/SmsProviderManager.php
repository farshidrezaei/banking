<?php

namespace App\Managers\Sms;

use Illuminate\Support\Manager;

class SmsProviderManager extends Manager
{

    public function getDefaultDriver()
    {
        return config('manager.sms_provider.default');
    }
}