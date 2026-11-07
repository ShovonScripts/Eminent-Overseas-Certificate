<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\CertificateAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CertificateAttendance>
 */
class CertificateAttendanceFactory extends Factory
{
    protected $model = CertificateAttendance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'certificate_id' => Certificate::factory(),
            'year' => 2025,
            'month' => fake()->numberBetween(1, 12),
            'class_hours' => 33,
            'attendance_hours' => 33,
        ];
    }
}
