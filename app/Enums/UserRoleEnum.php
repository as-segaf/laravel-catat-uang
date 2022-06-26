<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public static function generateOptions()
    {
        return collect(UserRoleEnum::cases())->mapWithKeys(function ($item, $key) {
            return [$item->value => ucwords(strtolower(str_replace("_", " ", $item->name)))];
        });
    }
}