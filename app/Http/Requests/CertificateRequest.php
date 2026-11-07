<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CertificateRequest extends FormRequest
{
    public const WEEKDAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $instructors = array_values(array_filter(
            (array) $this->input('instructors', []),
            static fn ($value): bool => $value !== null && $value !== '',
        ));

        $this->merge(['instructors' => $instructors]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_name_en' => ['required', 'string', 'max:255'],
            'student_name_ja' => ['nullable', 'string', 'max:255'],
            'student_date_of_birth' => ['required', 'date', 'before:today'],
            'student_gender' => ['required', Rule::enum(Gender::class)],
            'student_email' => ['nullable', 'email', 'max:255'],
            'student_phone' => ['nullable', 'string', 'max:30'],

            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'study_start' => ['required', 'date'],
            'study_end' => ['required', 'date', 'after:study_start'],
            'hours_per_day' => ['required', 'integer', 'min:1', 'max:12'],
            'days_per_week' => ['required', 'integer', 'min:1', 'max:7'],
            'class_days' => ['required', 'array', 'min:1'],
            'class_days.*' => ['string', 'in:'.implode(',', self::WEEKDAYS)],
            'national_holidays' => ['nullable', 'string', 'max:2000'],
            'issued_on' => ['required', 'date', 'after_or_equal:study_end'],
            'instructors' => ['required', 'array', 'min:1', 'max:2'],
            'instructors.*' => ['integer', 'distinct', 'exists:instructors,id'],
            'attendances' => ['required', 'array', 'min:1', 'max:24'],
            'attendances.*.year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'attendances.*.month' => ['required', 'integer', 'min:1', 'max:12'],
            'attendances.*.class_hours' => ['required', 'integer', 'min:0', 'max:2000'],
            'attendances.*.attendance_hours' => ['required', 'integer', 'min:0', 'max:2000'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $rows = $this->input('attendances');

                if (! is_array($rows)) {
                    return;
                }

                $seen = [];

                foreach ($rows as $index => $row) {
                    if (! is_array($row)) {
                        continue;
                    }

                    $year = (int) ($row['year'] ?? 0);
                    $month = (int) ($row['month'] ?? 0);
                    $class = (int) ($row['class_hours'] ?? 0);
                    $attendance = (int) ($row['attendance_hours'] ?? 0);

                    $key = $year.'-'.$month;

                    if (isset($seen[$key])) {
                        $validator->errors()->add(
                            "attendances.{$index}.month",
                            'Each month can only appear once.',
                        );
                    }

                    $seen[$key] = true;

                    if ($attendance > $class) {
                        $validator->errors()->add(
                            "attendances.{$index}.attendance_hours",
                            'Attendance hours cannot exceed class hours.',
                        );
                    }
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_name_en' => 'student English name',
            'student_name_ja' => 'student Japanese name',
            'student_date_of_birth' => 'student date of birth',
            'student_gender' => 'student gender',
            'student_email' => 'student email',
            'student_phone' => 'student phone',
            'course_id' => 'course',
            'study_start' => 'study start date',
            'study_end' => 'study end date',
            'issued_on' => 'issue date',
            'class_days' => 'class days',
            'national_holidays' => 'other holidays',
            'instructors' => 'instructors',
            'attendances' => 'monthly attendance',
        ];
    }
}
