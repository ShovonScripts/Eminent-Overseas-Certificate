<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\Gender;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;

final class JapaneseDate
{
    public static function full(CarbonInterface|DateTimeInterface|string $date): string
    {
        $d = self::toCarbon($date);

        return "{$d->year}年{$d->month}月{$d->day}日";
    }

    public static function range(
        CarbonInterface|DateTimeInterface|string $start,
        CarbonInterface|DateTimeInterface|string $end,
    ): string {
        return self::full($start).' ～ '.self::full($end);
    }

    public static function gender(Gender|string $gender): string
    {
        $value = $gender instanceof Gender ? $gender : Gender::tryFrom($gender);

        return $value?->japaneseLabel() ?? 'その他';
    }

    public static function weekday(string $english): string
    {
        return [
            'Monday' => '月曜日',
            'Tuesday' => '火曜日',
            'Wednesday' => '水曜日',
            'Thursday' => '木曜日',
            'Friday' => '金曜日',
            'Saturday' => '土曜日',
            'Sunday' => '日曜日',
        ][$english] ?? $english;
    }

    public static function month(int $month): string
    {
        return "{$month}月";
    }

    public static function toCarbon(CarbonInterface|DateTimeInterface|string $date): CarbonInterface
    {
        if ($date instanceof CarbonInterface) {
            return $date;
        }

        if ($date instanceof DateTimeInterface) {
            return Carbon::instance($date);
        }

        return Carbon::parse($date);
    }
}
