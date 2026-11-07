<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'name_ja',
        'name_en',
        'level_code',
        'aim_level_ja',
        'aim_level_en',
        'total_hours',
        'book_ja',
        'book_en',
        'learning_context',
        'default_hours_per_day',
        'default_days_per_week',
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'learning_context' => 'array',
            'total_hours' => 'integer',
            'default_hours_per_day' => 'integer',
            'default_days_per_week' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function learningContextJapanese(): array
    {
        return array_map(
            static fn (array $item): string => (string) ($item['ja'] ?? ''),
            $this->learning_context ?? [],
        );
    }

    /**
     * @return array<int, string>
     */
    public function learningContextEnglish(): array
    {
        return array_map(
            static fn (array $item): string => (string) ($item['en'] ?? ''),
            $this->learning_context ?? [],
        );
    }
}
