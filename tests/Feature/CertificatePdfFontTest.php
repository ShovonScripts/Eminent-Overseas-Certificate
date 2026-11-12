<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Services\CertificatePdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatePdfFontTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_pdf_embeds_noto_sans_jp_instead_of_relying_on_system_fonts(): void
    {
        $certificate = Certificate::factory()
            ->withAttendance()
            ->withInstructors()
            ->issued()
            ->create();

        $pdf = app(CertificatePdfService::class)->render($certificate);

        $this->assertStringContainsString('NotoSansJP', $pdf);
        $this->assertGreaterThanOrEqual(
            2,
            substr_count($pdf, '/FontFile2'),
            'Both the regular and bold Noto Sans JP fonts must be embedded.',
        );
    }
}
