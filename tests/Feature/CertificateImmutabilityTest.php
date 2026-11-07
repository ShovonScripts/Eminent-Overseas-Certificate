<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CertificateImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_issued_certificate_cannot_be_edited(): void
    {
        $certificate = Certificate::factory()->issued()->create();

        $this->expectException(HttpException::class);

        $certificate->update(['study_end' => '2026-02-01']);
    }

    public function test_an_issued_certificate_cannot_be_deleted(): void
    {
        $certificate = Certificate::factory()->issued()->create();

        $this->expectException(HttpException::class);

        $certificate->delete();
    }

    public function test_a_draft_can_still_be_edited(): void
    {
        $certificate = Certificate::factory()->create();

        $certificate->update(['study_end' => '2025-11-30']);

        $this->assertSame('2025-11-30', $certificate->fresh()->study_end->toDateString());
    }
}
