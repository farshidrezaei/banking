<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TransactionTypeEnum: string
{
    use EnumToArray;

    case INCOME = 'INCOME';
    case OUTCOME = 'OUTCOME';

}
