<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Gender;
use App\Support\JapaneseDate;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class JapaneseDateTest extends TestCase
{
    public function test_formats_a_full_japanese_date(): void
    {
        $this->assertSame('2025年6月2日', JapaneseDate::full(Carbon::parse('2025-06-02')));
    }

    public function test_formats_a_date_range(): void
    {
        $this->assertSame(
            '2025年6月2日 ～ 2025年10月27日',
            JapaneseDate::range(Carbon::parse('2025-06-02'), Carbon::parse('2025-10-27')),
        );
    }

    public function test_formats_gender_and_weekday(): void
    {
        $this->assertSame('男性', JapaneseDate::gender(Gender::Male));
        $this->assertSame('女性', JapaneseDate::gender('female'));
        $this->assertSame('土曜日', JapaneseDate::weekday('Saturday'));
        $this->assertSame('10月', JapaneseDate::month(10));
    }
}
