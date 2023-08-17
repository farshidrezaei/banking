<?php

namespace Tests\Unit;


use App\Facades\Sms;
use App\Models\SmsLog;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsManagerTest extends TestCase
{
    public function testSmsSuccessfully(): void
    {
        Http::fakeSequence()->push([]);

        Sms::sendSms($mobile = '09354614194', 'salam');

        $this->assertDatabaseHas(SmsLog::class, ['receptor' => $mobile]);
    }

    public function testSmsFailedOn500Error(): void
    {
        Http::fakeSequence()->push([], 500);

        $this->expectException(RequestException::class);

        Sms::sendSms($mobile = '09354614194', 'salam');

        $this->assertDatabaseMissing(SmsLog::class, ['receptor' => $mobile]);
    }
}
