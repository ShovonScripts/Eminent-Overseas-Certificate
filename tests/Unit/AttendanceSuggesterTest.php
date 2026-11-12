<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\AttendanceSuggester;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AttendanceSuggesterTest extends TestCase
{
    public function test_japanese_holidays_are_expanded_for_every_year_in_the_period(): void
    {
        $suggester = new AttendanceSuggester;

        $holidays = $suggester->parseHolidays('1月1日; 2026-05-05', 2025, 2026);

        $this->assertContains('2025-01-01', $holidays);
        $this->assertContains('2026-01-01', $holidays);
        $this->assertContains('2026-05-05', $holidays);
        $this->assertNotContains('2024-01-01', $holidays);
    }

    public function test_a_holiday_is_applied_across_a_year_boundary(): void
    {
        $suggester = new AttendanceSuggester;

        $holidays = $suggester->parseHolidays('1月1日', 2025, 2026);

        $monthly = $suggester->suggest(
            Carbon::parse('2025-12-01'),
            Carbon::parse('2026-01-31'),
            ['Thursday'],
            3,
            $holidays,
        );

        $this->assertSame(12, $monthly['2025-12']);
        $this->assertSame(12, $monthly['2026-01']);
    }
}
