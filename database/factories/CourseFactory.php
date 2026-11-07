<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_ja' => '日本語初級',
            'name_en' => 'Basic Level',
            'level_code' => 'N5',
            'aim_level_ja' => 'JLPT N5 修了相当',
            'aim_level_en' => 'N5 Level',
            'total_hours' => 180,
            'book_ja' => 'みんなの日本語',
            'book_en' => 'Minna No Nihongo',
            'learning_context' => [
                ['ja' => '会話', 'en' => 'Conversation'],
                ['ja' => '語彙', 'en' => 'Vocabulary'],
                ['ja' => '文法', 'en' => 'Grammar'],
                ['ja' => '読解', 'en' => 'Reading'],
                ['ja' => '聴解', 'en' => 'Listening'],
                ['ja' => '作文', 'en' => 'Writing'],
            ],
            'default_hours_per_day' => 3,
            'default_days_per_week' => 3,
        ];
    }
}
