<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

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

        $pdfPath = null;

        try {
            DB::transaction(function () use ($certificate, $user, &$pdfPath): void {
                // Lock the row so two concurrent issue requests cannot both proceed.
                $locked = Certificate::query()
                    ->whereKey($certificate->getKey())
                    ->lockForUpdate()
                    ->first();

                if (! $locked || ! $locked->isDraft()) {
                    throw ValidationException::withMessages(['certificate' => 'Only draft certificates can be issued.']);
                }

                $certificate->refresh()->load(['attendances', 'instructors', 'course']);

                $this->validateForIssue($certificate);

                $totalClass = (int) $certificate->attendances->sum('class_hours');
                $totalAttendance = (int) $certificate->attendances->sum('attendance_hours');

                if ($totalClass <= 0) {
                    throw ValidationException::withMessages([
                        'attendances' => 'Total class hours must be greater than zero before issuing.',
                    ]);
                }

                $rate = round($totalAttendance / $totalClass * 100, 2);

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

                // The PDF is generated while the certificate is still a draft so
                // the immutability observer permits the pdf_path / pdf_sha256 update.
                $pdfPath = $this->pdf->generate($certificate);

                $certificate->forceFill([
                    'status' => CertificateStatus::Issued,
                    'issued_at' => now(),
                    'issued_by' => $user?->id,
                ])->save();
            });
        } catch (Throwable $exception) {
            if ($pdfPath !== null && Storage::disk('local')->exists($pdfPath)) {
                Storage::disk('local')->delete($pdfPath);
            }

            throw $exception;
        }

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
        $startYear = $certificate->study_start?->year ?? now()->year;
        $endYear = $certificate->study_end?->year ?? $startYear;
        $holidays = $this->suggester->parseHolidays($certificate->national_holidays, $startYear, $endYear);

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
