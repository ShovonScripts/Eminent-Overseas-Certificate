<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateAttendance;

class CertificateTotals
{
    /**
     * @param  iterable<CertificateAttendance|array<string, mixed>>  $attendances
     * @return array{total_class_hours: int, total_attendance_hours: int, attendance_rate: float}
     */
    public function calculate(iterable $attendances): array
    {
        $totalClass = 0;
        $totalAttendance = 0;

        foreach ($attendances as $attendance) {
            $totalClass += is_array($attendance)
                ? (int) ($attendance['class_hours'] ?? 0)
                : (int) $attendance->class_hours;

            $totalAttendance += is_array($attendance)
                ? (int) ($attendance['attendance_hours'] ?? 0)
                : (int) $attendance->attendance_hours;
        }

        return [
            'total_class_hours' => $totalClass,
            'total_attendance_hours' => $totalAttendance,
            'attendance_rate' => $totalClass > 0
                ? round($totalAttendance / $totalClass * 100, 2)
                : 0.0,
        ];
    }

    /**
     * @return array{total_class_hours: int, total_attendance_hours: int, attendance_rate: float}
     */
    public function apply(Certificate $certificate): array
    {
        $totals = $this->calculate($certificate->attendances);

        $certificate->forceFill($totals)->save();

        return $totals;
    }
}
