<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TransactionStatusEnum: string
{
    use EnumToArray;

    case INIT = 'init';
    case DONE = 'done';
    case FAILED = 'failed';

}
