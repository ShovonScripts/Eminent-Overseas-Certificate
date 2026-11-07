<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'student_name_en' => fake()->name(),
            'student_name_ja' => fake()->name(),
            'student_date_of_birth' => '2004-12-09',
            'student_gender' => 'male',
            'student_email' => fake()->unique()->safeEmail(),
            'student_phone' => '+8801711111111',
            'study_start' => '2025-06-02',
            'study_end' => '2025-10-27',
            'hours_per_day' => 3,
            'days_per_week' => 3,
            'class_days' => ['Saturday', 'Monday', 'Wednesday'],
            'national_holidays' => '6月9日; 6月11日; 8月16日; 10月1日',
            'total_class_hours' => 180,
            'total_attendance_hours' => 180,
            'attendance_rate' => 100,
            'issued_on' => '2026-01-15',
            'status' => CertificateStatus::Draft,
        ];
    }

    public function issued(): static
    {
        return $this->afterMaking(function (Certificate $certificate): void {
            if (blank($certificate->certificate_no)) {
                $year = $certificate->issued_on?->year ?? now()->year;
                $certificate->certificate_no = sprintf('EOC-%d-%06d', $year, fake()->unique()->numberBetween(1, 999999));
            }

            if (blank($certificate->verification_code)) {
                $certificate->verification_code = strtoupper((string) Str::ulid());
            }

            $certificate->status = CertificateStatus::Issued;
            $certificate->issued_at = now();
        })->afterCreating(function (Certificate $certificate): void {
            $certificate->forceFill([
                'status' => CertificateStatus::Issued,
                'certificate_no' => $certificate->certificate_no,
                'verification_code' => $certificate->verification_code,
                'issued_at' => now(),
            ])->saveQuietly();
        });
    }

    public function revoked(): static
    {
        return $this->issued()->afterCreating(function (Certificate $certificate): void {
            $certificate->forceFill([
                'status' => CertificateStatus::Revoked,
                'revoked_at' => now(),
                'revoke_reason' => 'Test revocation',
            ])->saveQuietly();
        });
    }

    public function withAttendance(): static
    {
        return $this->afterCreating(function (Certificate $certificate): void {
            $rows = [
                [2025, 6, 33, 33],
                [2025, 7, 39, 39],
                [2025, 8, 36, 36],
                [2025, 9, 39, 39],
                [2025, 10, 33, 33],
            ];

            foreach ($rows as [$year, $month, $class, $attendance]) {
                $certificate->attendances()->create([
                    'year' => $year,
                    'month' => $month,
                    'class_hours' => $class,
                    'attendance_hours' => $attendance,
                ]);
            }
        });
    }

    public function withInstructors(): static
    {
        return $this->afterCreating(function (Certificate $certificate): void {
            $instructors = Instructor::factory()->count(2)->create();

            foreach ($instructors as $index => $instructor) {
                $certificate->instructors()->attach($instructor->id, ['position' => $index + 1]);
            }
        });
    }
}
