<?php

namespace App\Managers\Sms\Providers;

use App\Managers\Sms\AbstractSmsProvider;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Ghasedak extends AbstractSmsProvider
{
    protected string $slug = 'ghasedak';

    /**
     * @throws RequestException
     */
    public function send(string $mobile, string $message): void
    {
         Http::asForm()->baseUrl(config('services.sms_providers.ghasedak.base_url'))
            ->withHeader('apikey', config('services.sms_providers.ghasedak.api_key'))
            ->post(
                'sms/send/simple?'.http_build_query([
                    'receptor' => $mobile,
                    'message' => $message
                ])
            )
            ->throw();
    }
}