<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\Instructor;
use App\Services\CertificateIssuer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CertificateIssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_issues_a_draft_with_number_code_totals_and_pdf(): void
    {
        Storage::fake('local');

        $certificate = Certificate::factory()
            ->withAttendance()
            ->withInstructors()
            ->create();

        $issued = app(CertificateIssuer::class)->issue($certificate);

        $this->assertSame(CertificateStatus::Issued, $issued->status);
        $this->assertSame('EOC-2026-000001', $issued->certificate_no);
        $this->assertMatchesRegularExpression('/^[0-9A-HJKMNP-TV-Z]{26}$/', $issued->verification_code);
        $this->assertSame(180, $issued->total_class_hours);
        $this->assertSame(180, $issued->total_attendance_hours);
        $this->assertSame('100.00', $issued->attendance_rate);
        $this->assertNotNull($issued->issued_at);
        $this->assertNotNull($issued->pdf_path);
        $this->assertNotNull($issued->pdf_sha256);

        Storage::disk('local')->assertExists($issued->pdf_path);
        $binary = Storage::disk('local')->get($issued->pdf_path);

        $this->assertStringStartsWith('%PDF', $binary);
        $this->assertSame(hash('sha256', $binary), $issued->pdf_sha256);
        $this->assertGreaterThan(1000, strlen($binary));
    }

    public function test_it_refuses_attendance_hours_above_class_hours(): void
    {
        $certificate = Certificate::factory()->withInstructors()->create();
        $certificate->attendances()->create([
            'year' => 2025,
            'month' => 6,
            'class_hours' => 10,
            'attendance_hours' => 12,
        ]);

        $this->expectException(ValidationException::class);

        app(CertificateIssuer::class)->issue($certificate->fresh());
    }

    public function test_it_refuses_to_issue_a_certificate_without_attendances(): void
    {
        $certificate = Certificate::factory()->withInstructors()->create();

        $this->expectException(ValidationException::class);

        app(CertificateIssuer::class)->issue($certificate);
    }

    public function test_it_refuses_an_issue_date_before_the_study_end(): void
    {
        $certificate = Certificate::factory()
            ->withAttendance()
            ->withInstructors()
            ->create(['issued_on' => '2025-09-01']);

        $this->expectException(ValidationException::class);

        app(CertificateIssuer::class)->issue($certificate);
    }

    public function test_it_attaches_the_instructors_used_in_the_pdf(): void
    {
        Storage::fake('local');

        $certificate = Certificate::factory()
            ->withAttendance()
            ->withInstructors()
            ->create();

        $issued = app(CertificateIssuer::class)->issue($certificate);

        $this->assertCount(2, $issued->instructors);
        $this->assertSame(Instructor::count(), 2);
    }
}
