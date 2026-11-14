<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatePdfContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_completion_time_is_calculated_from_attendance_rows(): void
    {
        $course = Course::factory()->create(['total_hours' => 200]);
        $certificate = Certificate::factory()
            ->for($course)
            ->withInstructors()
            ->issued()
            ->create([
                'student_name_en' => 'Test Student',
                'student_name_ja' => 'テスト学生',
            ]);

        for ($month = 1; $month <= 6; $month++) {
            $certificate->attendances()->create([
                'year' => 2025,
                'month' => $month,
                'class_hours' => 32,
                'attendance_hours' => 32,
            ]);
        }

        $certificate->load(['course', 'instructors', 'attendances']);

        $html = view('certificates.pdf', [
            'c' => $certificate,
            'school' => config('school'),
            'qrBase64' => null,
            'draft' => false,
            'images' => [],
        ])->render();

        $this->assertStringContainsString('192 時間 / 192 Hours', $html);
        $this->assertStringNotContainsString('180 時間 / 180 Hours', $html);
    }
}
