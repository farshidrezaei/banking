<?php

namespace App\Managers\Sms;

use App\Models\SmsLog;
use Illuminate\Support\Str;

abstract class AbstractSmsProvider
{

    private ?string $providerSlug = null;

    abstract public function send(string $mobile, string $message): void;

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
}