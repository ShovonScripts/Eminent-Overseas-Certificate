<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
            'name_ja' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'level_code' => ['required', 'string', 'max:20'],
            'aim_level_ja' => ['required', 'string', 'max:255'],
            'aim_level_en' => ['required', 'string', 'max:255'],
            'total_hours' => ['required', 'integer', 'min:1', 'max:3000'],
            'book_ja' => ['required', 'string', 'max:255'],
            'book_en' => ['required', 'string', 'max:255'],
            'learning_context' => ['required', 'array', 'min:1', 'max:10'],
            'learning_context.*.ja' => ['required', 'string', 'max:255'],
            'learning_context.*.en' => ['required', 'string', 'max:255'],
            'default_hours_per_day' => ['required', 'integer', 'min:1', 'max:12'],
            'default_days_per_week' => ['required', 'integer', 'min:1', 'max:7'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name_ja' => 'course name (Japanese)',
            'name_en' => 'course name (English)',
            'level_code' => 'level code',
            'aim_level_ja' => 'aim level (Japanese)',
            'aim_level_en' => 'aim level (English)',
            'total_hours' => 'completion hours',
            'book_ja' => 'book name (Japanese)',
            'book_en' => 'book name (English)',
            'learning_context' => 'learning context',
        ];
    }
}
