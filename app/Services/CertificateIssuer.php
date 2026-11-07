<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CertificateIssuer
{
    public function __construct(
        private readonly CertificateNumberGenerator $numbers,
        private readonly VerificationCodeGenerator $codes,
        private readonly CertificatePdfService $pdf,
        private readonly AttendanceSuggester $suggester,
    ) {}

    public function issue(Certificate $certificate, ?User $user = null): Certificate
    {
        if (! $certificate->exists) {
            throw ValidationException::withMessages(['certificate' => 'Save the certificate before issuing it.']);
        }

        $certificate->refresh()->load(['attendances', 'instructors', 'course']);

        if (! $certificate->isDraft()) {
            throw ValidationException::withMessages(['certificate' => 'Only draft certificates can be issued.']);
        }

        $this->validateForIssue($certificate);

        $totalClass = (int) $certificate->attendances->sum('class_hours');
        $totalAttendance = (int) $certificate->attendances->sum('attendance_hours');
        $rate = $totalClass > 0 ? round($totalAttendance / $totalClass * 100, 2) : 0;

        $certificate->fill([
            'total_class_hours' => $totalClass,
            'total_attendance_hours' => $totalAttendance,
            'attendance_rate' => $rate,
        ]);

        if (blank($certificate->certificate_no)) {
            $certificate->certificate_no = $this->numbers->make($certificate->issued_on);
        }

        if (blank($certificate->verification_code)) {
            $certificate->verification_code = $this->codes->make();
        }

        $certificate->save();

        // The PDF is generated while the certificate is still a draft so the
        // immutability observer permits the pdf_path / pdf_sha256 update.
        $this->pdf->generate($certificate);

        DB::transaction(function () use ($certificate, $user): void {
            $certificate->forceFill([
                'status' => CertificateStatus::Issued,
                'issued_at' => now(),
                'issued_by' => $user?->id,
            ])->save();
        });

        return $certificate->refresh();
    }

    public function revoke(Certificate $certificate, string $reason, ?User $user = null): Certificate
    {
        if (! $certificate->isIssued()) {
            throw ValidationException::withMessages(['certificate' => 'Only issued certificates can be revoked.']);
        }

        $certificate->forceFill([
            'status' => CertificateStatus::Revoked,
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ])->save();

        return $certificate->refresh();
    }

    public function preview(Certificate $certificate): string
    {
        return $this->pdf->render(
            $certificate->refresh()->load(['course', 'instructors', 'attendances']),
            $certificate->isDraft(),
        );
    }

    /**
     * @return array<string, int>
     */
    public function suggestMonthlyHours(Certificate $certificate): array
    {
        $year = $certificate->study_start?->year ?? now()->year;
        $holidays = $this->suggester->parseHolidays($certificate->national_holidays, $year);

        return $this->suggester->suggest(
            $certificate->study_start,
            $certificate->study_end,
            $certificate->class_days ?? [],
            (int) $certificate->hours_per_day,
            $holidays,
        );
    }

    private function validateForIssue(Certificate $certificate): void
    {
        $errors = [];

        if (blank($certificate->student_name_en)) {
            $errors['student_name_en'] = 'Student name is required.';
        }

        if (! $certificate->course_id) {
            $errors['course_id'] = 'Select a course.';
        }

        if ($certificate->study_end->lessThanOrEqualTo($certificate->study_start)) {
            $errors['study_end'] = 'The study end date must be after the start date.';
        }

        if ($certificate->issued_on->lessThan($certificate->study_end)) {
            $errors['issued_on'] = 'The issue date cannot be earlier than the study end date.';
        }

        if ($certificate->instructors->isEmpty()) {
            $errors['instructors'] = 'Select at least one instructor.';
        }

        if ($certificate->attendances->isEmpty()) {
            $errors['attendances'] = 'Add at least one monthly attendance row.';
        }

        foreach ($certificate->attendances as $attendance) {
            if ($attendance->attendance_hours > $attendance->class_hours) {
                $errors['attendances'] = "Attendance cannot exceed class hours for {$attendance->year}-{$attendance->month}.";
                break;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
