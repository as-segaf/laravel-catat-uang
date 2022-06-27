<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case INCOME = 'Income';
    case OUTCOME = 'Outcome';

    public static function generateOptions()
    {
        return collect(TransactionTypeEnum::cases())->mapWithKeys(function ($item, $key) {
            return [$item->value => ucwords(strtolower(str_replace("_", " ", $item->name)))];
        });
    }
}