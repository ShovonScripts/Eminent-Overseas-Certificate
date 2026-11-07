<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuggestAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'study_start' => ['required', 'date'],
            'study_end' => ['required', 'date', 'after:study_start'],
            'class_days' => ['required', 'array', 'min:1'],
            'class_days.*' => ['string', 'in:'.implode(',', CertificateRequest::WEEKDAYS)],
            'hours_per_day' => ['required', 'integer', 'min:1', 'max:12'],
            'national_holidays' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
