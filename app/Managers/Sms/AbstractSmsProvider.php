<?php

namespace App\Managers\Sms;

use App\Models\SmsLog;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class AbstractSmsProvider
{

    private ?string $providerSlug = null;


    /**
     * @param  string  $mobile
     * @param  string  $message
     * @return void
     * @throws Exception|RequestException
     */
    abstract protected function send(string $mobile, string $message): void;

    protected function storeLog(string $receptor, string $message): void
    {
        SmsLog::create([
            'provider' => $this->getProviderSlug(),
            'receptor' => $receptor,
            'message' => $message,
        ]);
    }

    /**
     * @return string|null
     */
    public function getProviderSlug(): ?string
    {
        return $this->providerSlug ?? Str::of(get_class($this))->lower()->toString();
    }

    /**
     * @throws RequestException
     */
    public function sendSms(string $mobile, string $message): void
    {
        try {
            $this->send($mobile, $message);
        } catch (RequestException $exception) {
            Log::critical('', [
                'exception' => get_class($exception),
                'provider' => $this->getProviderSlug(),
                'exception_message' => $exception->getMessage(),
                'response' => $exception->response->body(),
                'mobile' => $mobile,
            ]);
            throw $exception;
        } catch (Exception $exception) {
            Log::critical('', [
                'exception' => get_class($exception),
                'provider' => $this->getProviderSlug(),
                'exception_message' => $exception->getMessage(),
                'mobile' => $mobile,
            ]);
            throw $exception;
        }
        $this->storeLog($mobile, $message);
    }

}