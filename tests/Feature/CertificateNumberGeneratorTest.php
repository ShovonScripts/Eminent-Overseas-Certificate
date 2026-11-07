<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Services\CertificateNumberGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_numbers_are_sequential_per_year_and_never_reused(): void
    {
        $generator = app(CertificateNumberGenerator::class);

        $this->assertSame('EOC-2026-000001', $generator->make(Carbon::parse('2026-01-15')));

        Certificate::factory()->create(['certificate_no' => 'EOC-2026-000001']);
        $second = Certificate::factory()->create(['certificate_no' => 'EOC-2026-000002']);

        $this->assertSame('EOC-2026-000003', $generator->make(Carbon::parse('2026-02-01')));

        $second->delete();

        $this->assertSame('EOC-2026-000003', $generator->make(Carbon::parse('2026-03-01')));
        $this->assertSame('EOC-2027-000001', $generator->make(Carbon::parse('2027-01-01')));
    }
}
