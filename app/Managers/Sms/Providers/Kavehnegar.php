<?php

namespace App\Managers\Sms\Providers;

use App\Managers\Sms\AbstractSmsProvider;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Kavehnegar extends AbstractSmsProvider
{

    /**
     * @throws RequestException
     */
    public function send(string $mobile, string $message): void
    {
        $response = Http::baseUrl(
            config('services.sms_providers.kavenegar.base_url')."/".config(
                'services.sms_providers.kavenegar.api_key'
            )
        )
            ->get('sms/send.json', [
                'receptor' => $mobile,
                'message' => $message
            ])
            ->throw();

        $this->storeLog($mobile, $message);
    }
}