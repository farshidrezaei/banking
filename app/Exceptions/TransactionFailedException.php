<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TransactionFailedException extends Exception
{
    public function __construct(private readonly string $trackId)
    {
        parent::__construct();
    }

    public function render(): JsonResponse
    {
        return new JsonResponse([
            'message' => __('exceptions.transaction_failed'),
            'track_id' => $this->trackId
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
