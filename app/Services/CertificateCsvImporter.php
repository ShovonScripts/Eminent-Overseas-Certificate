<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CertificateStatus;
use App\Http\Requests\CertificateRequest;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class CertificateCsvImporter
{
    private const REQUIRED_COLUMNS = [
        'student_name_en',
        'student_date_of_birth',
        'student_gender',
        'course',
        'study_start',
        'study_end',
        'issued_on',
        'class_days',
        'hours_per_day',
        'instructors',
    ];

    public function __construct(
        private readonly CertificateIssuer $issuer,
        private readonly AttendanceSuggester $suggester,
    ) {}

    /**
     * @return array{issued: int, errors: array<int, array{row: int, message: string}>}
     */
    public function import(string $path, User $user): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('The uploaded file could not be read.');
        }

        try {
            $headers = $this->headers($handle);
            $monthColumns = $this->monthColumns($headers);
            $this->ensureHeaders($headers, $monthColumns);

            $issued = 0;
            $errors = [];
            $rowNumber = 1;

            while (($values = fgetcsv($handle)) !== false) {
                $rowNumber++;

                if ($this->isBlankRow($values)) {
                    continue;
                }

                $row = $this->combine($headers, $values);

                try {
                    $this->importRow($row, $monthColumns, $user);
                    $issued++;
                } catch (Throwable $exception) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'message' => $exception->getMessage(),
                    ];
                }
            }
        } finally {
            fclose($handle);
        }

        return ['issued' => $issued, 'errors' => $errors];
    }

    /**
     * @param  resource  $handle
     * @return array<int, string>
     */
    private function headers($handle): array
    {
        $raw = fgetcsv($handle);

        if ($raw === false) {
            throw new InvalidArgumentException('The CSV file is empty.');
        }

        $headers = [];

        foreach ($raw as $index => $header) {
            $header = strtolower(trim((string) $header));

            if ($index === 0) {
                $header = ltrim($header, "\xEF\xBB\xBF");
            }

            $headers[] = $header;
        }

        return $headers;
    }

    /**
     * @param  array<int, string>  $headers
     * @return array<int, string>
     */
    private function monthColumns(array $headers): array
    {
        return array_values(array_filter(
            $headers,
            static fn (string $header): bool => (bool) preg_match('/^\d{4}-\d{2}$/', $header),
        ));
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, string>  $monthColumns
     */
    private function ensureHeaders(array $headers, array $monthColumns): void
    {
        $missing = array_diff(self::REQUIRED_COLUMNS, $headers);

        if ($missing !== []) {
            throw new InvalidArgumentException(
                'Missing required column(s): '.implode(', ', $missing).'. Download the template for the expected format.',
            );
        }

        if ($monthColumns === []) {
            throw new InvalidArgumentException(
                'Add at least one attendance column named YYYY-MM (for example 2025-06).',
            );
        }
    }

    /**
     * @param  array<int, string|null>  $values
     */
    private function isBlankRow(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, string|null>  $values
     * @return array<string, string>
     */
    private function combine(array $headers, array $values): array
    {
        $row = [];

        foreach ($headers as $index => $header) {
            $row[$header] = trim((string) ($values[$index] ?? ''));
        }

        return $row;
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<int, string>  $monthColumns
     */
    private function importRow(array $row, array $monthColumns, User $user): void
    {
        $course = $this->course($row['course']);

        if (! $course) {
            throw new InvalidArgumentException("Course {$row['course']} was not found by level code or English name.");
        }

        $start = $this->date($row['study_start'], 'study_start');
        $end = $this->date($row['study_end'], 'study_end');
        $issuedOn = $this->date($row['issued_on'], 'issued_on');

        if ($end->lessThanOrEqualTo($start)) {
            throw new InvalidArgumentException('study_end must be after study_start.');
        }

        if ($issuedOn->lessThan($end)) {
            throw new InvalidArgumentException('issued_on must be on or after study_end.');
        }

        $classDays = $this->classDays($row['class_days']);
        $hoursPerDay = (int) $row['hours_per_day'];

        if ($hoursPerDay < 1 || $hoursPerDay > 12) {
            throw new InvalidArgumentException('hours_per_day must be between 1 and 12.');
        }

        $instructors = $this->instructors($row['instructors']);
        $holidays = $this->suggester->parseHolidays($row['national_holidays'] ?? null, $start->year, $end->year);
        $suggested = $this->suggester->suggest($start, $end, $classDays, $hoursPerDay, $holidays);
        $attendance = $this->attendance($row, $monthColumns, $suggested);

        if ($attendance === []) {
            throw new InvalidArgumentException('Add at least one attendance value in a YYYY-MM column.');
        }

        // Check for duplicate certificate (same student name, DOB, course, study period)
        $duplicate = Certificate::query()
            ->where('student_name_en', $row['student_name_en'])
            ->where('student_date_of_birth', $row['student_date_of_birth'])
            ->where('course_id', $course->id)
            ->whereDate('study_start', $start)
            ->whereDate('study_end', $end)
            ->whereIn('status', [CertificateStatus::Draft, CertificateStatus::Issued])
            ->exists();

        if ($duplicate) {
            throw new InvalidArgumentException('A draft or issued certificate already exists for this student, course and study period.');
        }

        DB::transaction(function () use ($course, $start, $end, $issuedOn, $classDays, $hoursPerDay, $instructors, $attendance, $row, $user): void {
            $certificate = Certificate::create([
                'course_id' => $course->id,
                'student_name_en' => $row['student_name_en'],
                'student_name_ja' => $row['student_name_ja'] ?? null,
                'student_date_of_birth' => $row['student_date_of_birth'],
                'student_gender' => $row['student_gender'],
                'student_email' => $row['student_email'] ?? null,
                'student_phone' => $row['student_phone'] ?? null,
                'study_start' => $start,
                'study_end' => $end,
                'hours_per_day' => $hoursPerDay,
                'days_per_week' => count($classDays),
                'class_days' => $classDays,
                'national_holidays' => $row['national_holidays'] ?: null,
                'issued_on' => $issuedOn,
            ]);

            foreach ($attendance as $item) {
                $certificate->attendances()->create($item);
            }

            $sync = [];

            foreach ($instructors as $index => $instructor) {
                $sync[$instructor->id] = ['position' => $index + 1];
            }

            $certificate->instructors()->sync($sync);

            try {
                $this->issuer->issue($certificate, $user);
            } catch (Throwable $exception) {
                $certificate->attendances()->delete();
                $certificate->instructors()->detach();
                $certificate->forceDelete();

                throw $exception;
            }
        });
    }

    private function course(string $value): ?Course
    {
        return Course::query()
            ->whereRaw('LOWER(level_code) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(name_en) = ?', [strtolower($value)])
            ->first();
    }

    private function date(string $value, string $field): CarbonImmutable
    {
        $date = CarbonImmutable::createFromFormat('!Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException("{$field} must be a valid YYYY-MM-DD date.");
        }

        return $date;
    }

    /**
     * @return array<int, string>
     */
    private function classDays(string $value): array
    {
        $days = array_values(array_filter(array_map(
            static fn (string $day): string => ucfirst(strtolower(trim($day))),
            preg_split('/[;,|]+/', $value) ?: [],
        )));

        if ($days === []) {
            throw new InvalidArgumentException('class_days is required, for example Saturday;Monday;Wednesday.');
        }

        foreach ($days as $day) {
            if (! in_array($day, CertificateRequest::WEEKDAYS, true)) {
                throw new InvalidArgumentException("Unknown class day: {$day}.");
            }
        }

        return array_values(array_unique($days));
    }

    /**
     * @return array<int, Instructor>
     */
    private function instructors(string $value): array
    {
        if ($value === '') {
            return Instructor::where('is_active', true)->orderBy('id')->limit(2)->get()->all();
        }

        $names = array_values(array_filter(array_map('trim', preg_split('/[;,|]+/', $value) ?: [])));
        $instructors = [];

        foreach ($names as $name) {
            $instructor = Instructor::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->orWhereRaw('LOWER(email) = ?', [strtolower($name)])
                ->first();

            if (! $instructor) {
                throw new InvalidArgumentException("Instructor {$name} was not found.");
            }

            $instructors[] = $instructor;
        }

        if (count($instructors) > 2) {
            throw new InvalidArgumentException('A certificate can have at most two instructors.');
        }

        return $instructors;
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<int, string>  $monthColumns
     * @param  array<string, int>  $suggested
     * @return array<int, array{year: int, month: int, class_hours: int, attendance_hours: int}>
     */
    private function attendance(array $row, array $monthColumns, array $suggested): array
    {
        $rows = [];

        foreach ($monthColumns as $column) {
            $value = trim($row[$column] ?? '');

            if ($value === '') {
                continue;
            }

            if (! is_numeric($value) || (int) $value < 0) {
                throw new InvalidArgumentException("Attendance for {$column} must be zero or a positive number.");
            }

            if (! array_key_exists($column, $suggested)) {
                throw new InvalidArgumentException("{$column} is outside the study period or contains no class days.");
            }

            $hours = (int) $value;

            if ($hours > $suggested[$column]) {
                throw new InvalidArgumentException(
                    "Attendance for {$column} ({$hours}) exceeds the calculated class hours ({$suggested[$column]}).",
                );
            }

            [$year, $month] = explode('-', $column);

            $rows[] = [
                'year' => (int) $year,
                'month' => (int) $month,
                'class_hours' => $suggested[$column],
                'attendance_hours' => $hours,
            ];
        }

        return $rows;
    }
}
