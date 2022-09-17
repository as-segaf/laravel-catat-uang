<?php

namespace App\Enums;

use App\Enums\Traits\CanGenerateOptions;

enum TransactionTypeEnum: string
{
    use CanGenerateOptions;

    case INCOME = 'income';
    case OUTCOME = 'outcome';
}
