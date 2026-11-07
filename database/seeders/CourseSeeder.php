<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::create([
            'name_en' => 'Basic Level',
            'name_ja' => '基礎コース',
            'level_code' => 'N5',
            'aim_level_en' => 'JLPT N5',
            'aim_level_ja' => 'JLPT N5',
            'total_hours' => 180,
            'book_en' => 'Minna no Nihongo',
            'book_ja' => 'みんなの日本語',
            'learning_context' => [
                ['ja' => '日本で働く', 'en' => 'Work in Japan'],
            ],
            'default_hours_per_day' => 3,
            'default_days_per_week' => 3,
        ]);

        Course::create([
            'name_en' => 'Intermediate Level',
            'name_ja' => '中級コース',
            'level_code' => 'N4',
            'aim_level_en' => 'JLPT N4',
            'aim_level_ja' => 'JLPT N4',
            'total_hours' => 180,
            'book_en' => 'Minna no Nihongo II',
            'book_ja' => 'みんなの日本語 II',
            'learning_context' => [
                ['ja' => '日本で学ぶ', 'en' => 'Study in Japan'],
            ],
            'default_hours_per_day' => 3,
            'default_days_per_week' => 3,
        ]);
    }
}
