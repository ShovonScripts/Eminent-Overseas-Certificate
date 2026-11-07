<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use App\Services\CertificateIssuer;
use Illuminate\Database\Seeder;

class DemoCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role', 'super_admin')->first();
        $course = Course::first();
        $instructors = Instructor::where('is_active', true)->take(2)->get();

        if (! $staff || ! $course || $instructors->count() < 2) {
            $this->command->error('Required data missing. Please seed courses, instructors, and super admin first.');
            return;
        }

        $issuer = app(CertificateIssuer::class);

        for ($i = 1; $i <= 2; $i++) {
            $cert = Certificate::create([
                'course_id' => $course->id,
                'student_name_en' => 'Demo Student ' . $i,
                'student_name_ja' => 'デモ学生 ' . $i,
                'student_date_of_birth' => '2000-01-01',
                'student_gender' => 'male',
                'student_email' => 'demo' . $i . '@example.com',
                'student_phone' => '+880171111111' . $i,
                'study_start' => '2025-01-01',
                'study_end' => '2025-12-31',
                'hours_per_day' => 3,
                'days_per_week' => 3,
                'class_days' => ['Saturday', 'Monday', 'Wednesday'],
                'national_holidays' => '1月1日; 5月1日; 8月15日',
                'issued_on' => now()->format('Y-m-d'),
            ]);

            // Add attendances
            $months = [
                ['year' => 2025, 'month' => 1, 'class_hours' => 36, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 2, 'class_hours' => 32, 'attendance_hours' => 32],
                ['year' => 2025, 'month' => 3, 'class_hours' => 40, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 4, 'class_hours' => 36, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 5, 'class_hours' => 40, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 6, 'class_hours' => 36, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 7, 'class_hours' => 40, 'attendance_hours' => 40],
                ['year' => 2025, 'month' => 8, 'class_hours' => 36, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 9, 'class_hours' => 40, 'attendance_hours' => 40],
                ['year' => 2025, 'month' => 10, 'class_hours' => 36, 'attendance_hours' => 36],
                ['year' => 2025, 'month' => 11, 'class_hours' => 40, 'attendance_hours' => 40],
                ['year' => 2025, 'month' => 12, 'class_hours' => 36, 'attendance_hours' => 36],
            ];

            foreach ($months as $m) {
                $cert->attendances()->create($m);
            }

            $cert->instructors()->sync([
                $instructors[0]->id => ['position' => 1],
                $instructors[1]->id => ['position' => 2],
            ]);

            // Issue the certificate
            $issuer = app(CertificateIssuer::class);
            $issuer->issue($cert, $staff);

            $this->command->info("Created certificate: {$cert->certificate_no} ({$cert->verification_code})");
        }

        $this->command->info('Demo certificates created successfully!');
    }
}