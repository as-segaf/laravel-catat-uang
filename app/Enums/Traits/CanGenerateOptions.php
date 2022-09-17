<?php

namespace App\Enums\Traits;

use Illuminate\Support\Collection;

trait CanGenerateOptions
{
    public static function allLabels(): Collection
    {
        return collect(self::cases())->map(function ($item) {
            return ucfirst(strtolower(str_replace('_', ' ', $item->name)));
        });
    }

    public static function allValues(): Collection
    {
        return collect(self::cases())->map(function ($item) {
            return $item->value;
        });
    }

    public static function getValuesAndLabelsOptions(): Collection
    {
        return self::allValues()->combine(self::allLabels());
    }

    public static function getLablesAndValuesOptions(): Collection
    {
        return self::allLabels()->combine(self::allValues());
    }
}
