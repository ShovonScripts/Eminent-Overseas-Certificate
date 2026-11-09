<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Services\CertificatePdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatePageCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_standard_5_months_certificate_is_strictly_one_page(): void
    {
        $certificate = Certificate::factory()
            ->withAttendance()
            ->withInstructors()
            ->issued()
            ->create();

        $pdfBinary = app(CertificatePdfService::class)->render($certificate);
        preg_match_all('/\/Type\s*\/Page\b/', $pdfBinary, $matches);
        $pageCount = count($matches[0]);

        $this->assertSame(1, $pageCount, "Standard certificate must be strictly 1 page, was {$pageCount}");
    }

    public function test_extended_8_months_certificate_is_strictly_one_page(): void
    {
        $certificate = Certificate::factory()
            ->withInstructors()
            ->issued()
            ->create();

        for ($m = 1; $m <= 8; $m++) {
            $certificate->attendances()->create([
                'year' => 2025,
                'month' => $m,
                'class_hours' => 30,
                'attendance_hours' => 30,
            ]);
        }

        $pdfBinary = app(CertificatePdfService::class)->render($certificate);
        preg_match_all('/\/Type\s*\/Page\b/', $pdfBinary, $matches);
        $pageCount = count($matches[0]);

        $this->assertSame(1, $pageCount, "8 months certificate must be strictly 1 page, was {$pageCount}");
    }

    public function test_full_year_12_months_certificate_is_strictly_one_page(): void
    {
        $certificate = Certificate::factory()
            ->withInstructors()
            ->issued()
            ->create();

        for ($m = 1; $m <= 12; $m++) {
            $certificate->attendances()->create([
                'year' => 2025,
                'month' => $m,
                'class_hours' => 30,
                'attendance_hours' => 30,
            ]);
        }

        $pdfBinary = app(CertificatePdfService::class)->render($certificate);
        preg_match_all('/\/Type\s*\/Page\b/', $pdfBinary, $matches);
        $pageCount = count($matches[0]);

        $this->assertSame(1, $pageCount, "12 months certificate must be strictly 1 page, was {$pageCount}");
    }

    public function test_extreme_content_certificate_is_strictly_one_page(): void
    {
        $inst1 = Instructor::factory()->create([
            'name' => 'Prof. Kenji Takahashi, Ph.D.',
            'title' => 'Chief Academic Director & Lead Japanese Language Instructor',
            'email' => 'k.takahashi@eminentoverseas.uk',
        ]);
        $inst2 = Instructor::factory()->create([
            'name' => 'Dr. Sayaka Watanabe',
            'title' => 'Senior Lecturer in Japanese Applied Linguistics',
            'email' => 's.watanabe@eminentoverseas.uk',
        ]);

        $certificate = Certificate::factory()
            ->issued()
            ->create([
                'student_name_en' => 'MOHAMMAD TANVIRUL ISLAM CHOWDHURY',
                'student_name_ja' => 'モハンマド・タンヴィルル・イスラム・チョードリー',
                'national_holidays' => '6月9日; 6月11日; 8月16日; 10月1日; 12月25日; 1月1日',
            ]);

        $certificate->instructors()->attach([$inst1->id, $inst2->id]);

        for ($m = 1; $m <= 6; $m++) {
            $certificate->attendances()->create([
                'year' => 2025,
                'month' => $m,
                'class_hours' => 32,
                'attendance_hours' => 32,
            ]);
        }

        $pdfBinary = app(CertificatePdfService::class)->render($certificate);
        preg_match_all('/\/Type\s*\/Page\b/', $pdfBinary, $matches);
        $pageCount = count($matches[0]);

        $this->assertSame(1, $pageCount, "Extreme content certificate must be strictly 1 page, was {$pageCount}");
    }

    public function test_demo_seeder_12_months_certificate_is_strictly_one_page(): void
    {
        $course = Course::factory()->create([
            'name_en' => 'Basic Level',
            'name_ja' => '基礎コース',
            'level_code' => 'N5',
            'aim_level_en' => 'JLPT N5',
            'aim_level_ja' => 'JLPT N5',
            'total_hours' => 180,
            'book_en' => 'Minna no Nihongo',
            'book_ja' => 'みんなの日本語',
            'learning_context' => [['ja' => '日本で働く', 'en' => 'Work in Japan']],
        ]);

        $certificate = Certificate::factory()
            ->withInstructors()
            ->issued()
            ->create([
                'course_id' => $course->id,
                'student_name_en' => 'Demo Student 2',
                'student_name_ja' => 'デモ学生 2',
                'study_start' => '2025-01-01',
                'study_end' => '2025-12-31',
                'national_holidays' => '1月1日; 5月1日; 8月15日',
                'issued_on' => '2026-11-07',
            ]);

        for ($m = 1; $m <= 12; $m++) {
            $certificate->attendances()->create([
                'year' => 2025,
                'month' => $m,
                'class_hours' => 36,
                'attendance_hours' => 36,
            ]);
        }

        $pdfBinary = app(CertificatePdfService::class)->render($certificate);
        preg_match_all('/\/Type\s*\/Page\b/', $pdfBinary, $matches);
        $pageCount = count($matches[0]);

        $this->assertSame(1, $pageCount, "Demo seeder 12 months certificate must be strictly 1 page, was {$pageCount}");
    }
}
