<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BalanceIsInsufficientException extends Exception
{
    public function render(): JsonResponse
    {
        return new JsonResponse([
            'message' =>__('exceptions.balance_is_insufficient'),
        ], Response::HTTP_FORBIDDEN);
    }
}
