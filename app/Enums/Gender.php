<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
            self::Other => 'Other',
        };
    }

    public function japaneseLabel(): string
    {
        return match ($this) {
            self::Male => '男性',
            self::Female => '女性',
            self::Other => 'その他',
        };
    }
}
