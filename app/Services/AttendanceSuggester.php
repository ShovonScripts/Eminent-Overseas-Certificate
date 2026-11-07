<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class AttendanceSuggester
{
    /**
     * Suggest class hours per year-month based on the study period, class days,
     * hours per day and a list of holidays.
     *
     * @param  array<int, string>  $classDays  English day names, e.g. ["Saturday"]
     * @param  array<int, string>  $holidays  Dates in "Y-m-d" format
     * @return array<string, int> Keyed by "Y-m"
     */
    public function suggest(
        CarbonInterface $start,
        CarbonInterface $end,
        array $classDays,
        int $hoursPerDay,
        array $holidays = [],
    ): array {
        $byMonth = [];

        foreach (CarbonPeriod::create($start->toDateString(), $end->toDateString()) as $date) {
            /** @var CarbonImmutable $date */
            if (! in_array($date->englishDayOfWeek, $classDays, true)) {
                continue;
            }

            if (in_array($date->toDateString(), $holidays, true)) {
                continue;
            }

            $key = $date->format('Y-m');
            $byMonth[$key] = ($byMonth[$key] ?? 0) + $hoursPerDay;
        }

        ksort($byMonth);

        return $byMonth;
    }

    /**
     * Parse a free-text holiday list such as "6月9日; 6月11日; 8月16日; 10月1日"
     * into ISO dates for the given year. Also accepts Y-m-d lines.
     *
     * @return array<int, string>
     */
    public function parseHolidays(?string $text, int $year): array
    {
        if (blank($text)) {
            return [];
        }

        $parts = preg_split('/[;\n,]+/u', $text) ?: [];
        $dates = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $part, $m)) {
                $dates[] = sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);

                continue;
            }

            if (preg_match('/^(\d{1,2})月(\d{1,2})日$/u', $part, $m)) {
                $dates[] = sprintf('%04d-%02d-%02d', $year, (int) $m[1], (int) $m[2]);
            }
        }

        return array_values(array_unique($dates));
    }
}
